<?php
session_start();


if (!isset($_SESSION['email'])) {
    header('Location: /frontend/pages/login.php');
    exit();
}

require_once '../../backend/config/ConnexionDB.php';

$conn = ConnexionDB::getInstance();
$userStmt = $conn->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
$userStmt->execute([':email' => $_SESSION['email']]);
$currentUserId = (int) $userStmt->fetchColumn();

if (!$currentUserId) {
    header('Location: /frontend/pages/logout.php');
    exit();
}

$_SESSION['user_id'] = $currentUserId;

/* ═══════════════════════════════════════════════════════════════════════
   POST handler  –  runs only on form submission, before any HTML output
   ═══════════════════════════════════════════════════════════════════════ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ── Helpers ──────────────────────────────────────────────────────── */

    function sanitizeStr(?string $v): string
    {
        return htmlspecialchars(strip_tags(trim($v ?? '')), ENT_QUOTES, 'UTF-8');
    }

    function nullIfEmpty(?string $v): ?string
    {
        $s = trim($v ?? '');
        return $s === '' ? null : $s;
    }

    /* ── 1. Collect & sanitize ────────────────────────────────────────── */

    $postType    = sanitizeStr($_POST['postType']    ?? 'job');
    $postTitle   = sanitizeStr($_POST['postTitle']   ?? '');
    $postCompany = sanitizeStr($_POST['postCompany'] ?? '');

    $postCountry = sanitizeStr($_POST['postCountry'] ?? '');
    if ($postCountry === 'other') {
        $postCountry = sanitizeStr($_POST['postCountryOther'] ?? '');
    }

    $postCity      = sanitizeStr($_POST['postCity']         ?? '');
    $jobTypeRaw    = sanitizeStr($_POST['jobType']          ?? 'Full-time');
    $workModeRaw   = sanitizeStr($_POST['workMode']         ?? 'On-site');
    $experienceKey = sanitizeStr($_POST['postExperience']   ?? '');
    $salaryRaw     = sanitizeStr($_POST['postSalary']       ?? '');
    $description   = sanitizeStr($_POST['postDescription']  ?? '');
    $appLink       = nullIfEmpty(filter_var($_POST['postLink']           ?? '', FILTER_SANITIZE_URL));
    $companyWebsite= nullIfEmpty(filter_var($_POST['postCompanyWebsite'] ?? '', FILTER_SANITIZE_URL));
    $contact       = nullIfEmpty(filter_var($_POST['postContact']        ?? '', FILTER_SANITIZE_EMAIL));
    $deadlineRaw   = sanitizeStr($_POST['postDeadline'] ?? '');

    $skillsRaw = array_filter(
        array_map('trim', (array)($_POST['skills'] ?? [])),
        fn($s) => $s !== ''
    );

    /* ── 2. Map form values to DB enums ───────────────────────────────── */

    /* job_type ENUM('part-time','full-time','internship') */
    if ($postType === 'internship') {
        $jobType = 'internship';
    } else {
        $jobTypeMap = [
            'Full-time' => 'full-time',
            'Part-time' => 'part-time',
            'Freelance' => 'part-time',   // not in enum; closest value
        ];
        $jobType = $jobTypeMap[$jobTypeRaw] ?? 'full-time';
    }

    /* job_mode ENUM('remote','onsite','hybrid') */
    $modeMap = ['On-site' => 'onsite', 'Remote' => 'remote', 'Hybrid' => 'hybrid'];
    $jobMode = $modeMap[$workModeRaw] ?? 'onsite';

    /* req_experience INT (years) */
    $experienceMap = ['none' => 0, 'lt1' => 0, '1-3' => 1, '3-5' => 3, '5+' => 5];
    $reqExperience = isset($experienceMap[$experienceKey]) ? $experienceMap[$experienceKey] : null;

    /* salary_min / salary_max / currency */
    $salaryMin = null;
    $salaryMax = null;
    $currency  = 'TND';

    if (!empty($salaryRaw) && strtolower($salaryRaw) !== 'unpaid') {
        preg_match_all('/\d[\d,]*(?:\.\d+)?/', $salaryRaw, $m);
        if (!empty($m[0])) {
            $salaryMin = (float) str_replace(',', '', $m[0][0]);
            if (isset($m[0][1])) $salaryMax = (float) str_replace(',', '', $m[0][1]);
        }
        if      (preg_match('/\bTND\b|\bDT\b/i', $salaryRaw)) $currency = 'TND';
        elseif  (preg_match('/\bEUR\b|€/i',       $salaryRaw)) $currency = 'EUR';
        elseif  (preg_match('/\bUSD\b|\$/i',       $salaryRaw)) $currency = 'USD';
        elseif  (preg_match('/\bGBP\b|£/i',        $salaryRaw)) $currency = 'GBP';
    }

    /* requirements TEXT – skills stored as CSV (no job_skills pivot in schema) */
    $requirements = nullIfEmpty(implode(', ', $skillsRaw));

    /* deadline TIMESTAMP */
    $deadline = nullIfEmpty($deadlineRaw);
    if ($deadline !== null) {
        $deadline = date('Y-m-d H:i:s', strtotime($deadline));
    }

    /* contact_email is NOT NULL in schema – fall back to session email */
    if ($contact === null) {
        $contact = $_SESSION['email'] ?? 'noreply@insat.tn';
    }

    /* ── 3. Validate required fields ──────────────────────────────────── */

    $errors = [];
    if ($postTitle   === '') $errors[] = 'Title is required.';
    if ($postCompany === '') $errors[] = 'Company is required.';
    if ($description === '') $errors[] = 'Description is required.';
    if ($appLink    === null) $errors[] = 'Application link is required.';

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    /* ── 4. Resolve country_id (insert if unknown) ────────────────────── */

    $countryId = null;

    if ($postCountry !== '') {
        $stmt = $conn->prepare('SELECT id FROM countries WHERE name = :name');
        $stmt->execute([':name' => $postCountry]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $countryId = (int) $row['id'];
        } else {
            $stmt = $conn->prepare('INSERT INTO countries (name) VALUES (:name)');
            $stmt->execute([':name' => $postCountry]);
            $countryId = (int) $conn->lastInsertId();
        }
    }

    /* ── 5. Resolve city_id (insert if unknown) ───────────────────────── */

    $cityId = null;

    if ($postCity !== '' && $countryId !== null) {
        $stmt = $conn->prepare(
            'SELECT id FROM cities WHERE name = :name AND country_id = :cid'
        );
        $stmt->execute([':name' => $postCity, ':cid' => $countryId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $cityId = (int) $row['id'];
        } else {
            $stmt = $conn->prepare(
                'INSERT INTO cities (country_id, name) VALUES (:cid, :name)'
            );
            $stmt->execute([':cid' => $countryId, ':name' => $postCity]);
            $cityId = (int) $conn->lastInsertId();
        }
    }

    /* ── 6. Insert into jobs ──────────────────────────────────────────── */

    try {
        $stmt = $conn->prepare('
            INSERT INTO jobs (
                titre, entreprise, job_type, job_mode,
                description, application_link, company_link,
                contact_email, requirements,
                salary_min, salary_max, currency,
                req_experience,
                country_id, city_id,
                deadline, created_by
            ) VALUES (
                :titre, :entreprise, :job_type, :job_mode,
                :description, :app_link, :company_link,
                :contact_email, :requirements,
                :salary_min, :salary_max, :currency,
                :req_experience,
                :country_id, :city_id,
                :deadline, :created_by
            )
        ');

        $stmt->execute([
            ':titre'          => $postTitle,
            ':entreprise'     => $postCompany,
            ':job_type'       => $jobType,
            ':job_mode'       => $jobMode,
            ':description'    => $description,
            ':app_link'       => $appLink,
            ':company_link'   => $companyWebsite,
            ':contact_email'  => $contact,
            ':requirements'   => $requirements,
            ':salary_min'     => $salaryMin,
            ':salary_max'     => $salaryMax,
            ':currency'       => $currency,
            ':req_experience' => $reqExperience,
            ':country_id'     => $countryId,
            ':city_id'        => $cityId,
            ':deadline'       => $deadline,
            ':created_by'     => $currentUserId,
        ]);

        $_SESSION['new_job_id'] = (int) $conn->lastInsertId();

    } catch (PDOException $e) {
        /* Duplicate contact_email (UNIQUE constraint, code 23000) */
        $msg = ($e->getCode() === '23000')
            ? 'A post with this contact email already exists. Please use a different one.'
            : 'Database error: ' . $e->getMessage();

        $_SESSION['form_errors'] = [$msg];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    /* ── 7. Redirect to self with success flag ────────────────────────── */
    header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
    exit();
}
/* ═══════════════════════════════════════════════════════════════════════
   End of POST handler – HTML rendering starts below
   ═══════════════════════════════════════════════════════════════════════ */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--<script src="/frontend/assets/js/auth.js"></script>-->

    <title>Alumini | Post an Opportunity</title>
    <link rel="stylesheet" href="/frontend/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/frontend/assets/css/footer_navbar.css">
    <link rel="stylesheet" href="/frontend/assets/css/createPost.css">

    <?php if (!empty($_SESSION['form_errors'])): ?>
    <script>
        window.__formErrors = <?= json_encode($_SESSION['form_errors']) ?>;
    </script>
    <?php unset($_SESSION['form_errors']); endif; ?>
</head>
<body>
<div id="navbar"></div>

<div class="cp-hero">
    <div class="cp-hero-inner">
        <span class="cp-hero-tag">New Opportunity</span>
        <h1 class="cp-hero-title">Post an <span>Opportunity</span></h1>
        <p class="cp-hero-sub">Share a job offer or internship with the INSAT community.</p>
    </div>
</div>

<div class="cp-wrapper">
    <section class="cp-form-col">
        <div class="cp-form-card">

            <form id="createPostForm" method="POST" action="" novalidate>

                <!-- Step 1 – Opportunity Type -->
                <div class="cp-section">
                    <div class="cp-section-label">
                        <span class="cp-step">1</span>
                        Opportunity Type
                    </div>
                    <div class="cp-type-selector">
                        <label class="cp-type-btn active" id="typeJob">
                            <input type="radio" name="postType" value="job" checked hidden>
                            <i class="bi bi-briefcase-fill"></i>
                            <span>Job</span>
                        </label>
                        <label class="cp-type-btn" id="typeInternship">
                            <input type="radio" name="postType" value="internship" hidden>
                            <i class="bi bi-mortarboard-fill"></i>
                            <span>Internship</span>
                        </label>
                    </div>
                </div>

                <!-- Step 2 – Basic Information -->
                <div class="cp-section">
                    <div class="cp-section-label">
                        <span class="cp-step">2</span>
                        Basic Information
                    </div>

                    <div class="cp-row">
                        <div class="cp-field">
                            <label class="cp-label" for="postTitle">Title <span class="cp-req">*</span></label>
                            <input type="text" class="cp-input" id="postTitle"
                                   name="postTitle"
                                   placeholder="e.g. Full-Stack Developer"
                                   required maxlength="255">
                        </div>
                        <div class="cp-field">
                            <label class="cp-label" for="postCompany">Company <span class="cp-req">*</span></label>
                            <input type="text" class="cp-input" id="postCompany"
                                   name="postCompany"
                                   placeholder="e.g. Vermeg"
                                   required maxlength="255">
                        </div>
                    </div>

                    <div class="cp-row">
                        <div class="cp-field">
                            <label class="cp-label" for="postCountry">Country</label>
                            <select class="cp-input" id="postCountry" name="postCountry">
                                <option value="" disabled selected>Select country</option>
                                <option value="Tunisia">Tunisia</option>
                                <option value="France">France</option>
                                <option value="Morocco">Morocco</option>
                                <option value="Germany">Germany</option>
                                <option value="UAE">UAE</option>
                                <option value="other">Other</option>
                            </select>
                            <input type="text" class="cp-input" id="postCountryOther"
                                   name="postCountryOther"
                                   placeholder="Type your country…"
                                   maxlength="120"
                                   style="display:none; margin-top:8px;">
                        </div>
                        <div class="cp-field">
                            <label class="cp-label" for="postCity">City</label>
                            <input type="text" class="cp-input" id="postCity"
                                   name="postCity"
                                   placeholder="e.g. Tunis"
                                   maxlength="120">
                        </div>
                    </div>
                </div>

                <!-- Step 3 – Role Details -->
                <div class="cp-section">
                    <div class="cp-section-label">
                        <span class="cp-step">3</span>
                        Role Details
                    </div>

                    <div class="cp-row">
                        <div class="cp-field">
                            <label class="cp-label">Job Type</label>
                            <div class="cp-chip-group" id="jobTypeChips">
                                <label class="cp-chip active">
                                    <input type="radio" name="jobType" value="Full-time" checked hidden>
                                    Full-time
                                </label>
                                <label class="cp-chip">
                                    <input type="radio" name="jobType" value="Part-time" hidden>
                                    Part-time
                                </label>
                                <label class="cp-chip">
                                    <input type="radio" name="jobType" value="Freelance" hidden>
                                    Freelance
                                </label>
                            </div>
                        </div>
                        <div class="cp-field">
                            <label class="cp-label">Work Mode</label>
                            <div class="cp-chip-group" id="workModeChips">
                                <label class="cp-chip active">
                                    <input type="radio" name="workMode" value="On-site" checked hidden>
                                    On-site
                                </label>
                                <label class="cp-chip">
                                    <input type="radio" name="workMode" value="Remote" hidden>
                                    Remote
                                </label>
                                <label class="cp-chip">
                                    <input type="radio" name="workMode" value="Hybrid" hidden>
                                    Hybrid
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="cp-row">
                        <div class="cp-field">
                            <label class="cp-label" for="postExperience">Experience</label>
                            <select class="cp-input" id="postExperience" name="postExperience">
                                <option value="" disabled selected>Select level</option>
                                <option value="none">No experience required</option>
                                <option value="lt1">Less than 1 year</option>
                                <option value="1-3">1–3 years</option>
                                <option value="3-5">3–5 years</option>
                                <option value="5+">5+ years</option>
                            </select>
                        </div>
                        <div class="cp-field">
                            <label class="cp-label" for="postSalary">Salary</label>
                            <input type="text" class="cp-input" id="postSalary"
                                   name="postSalary"
                                   placeholder="e.g. 1200 DT/month or Unpaid">
                        </div>
                    </div>
                </div>

                <!-- Step 4 – Required Skills -->
                <div class="cp-section">
                    <div class="cp-section-label">
                        <span class="cp-step">4</span>
                        Required Skills
                    </div>
                    <div class="cp-skill-input-row" id="skillRow1">
                        <input type="text" class="cp-input" id="skillInput1"
                               name="skills[]"
                               placeholder="Type a skill and press Enter…">
                        <button type="button" class="cp-skill-add-btn" id="addSkillBtn">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 5 – Description -->
                <div class="cp-section">
                    <div class="cp-section-label">
                        <span class="cp-step">5</span>
                        Description <span class="cp-req">*</span>
                    </div>
                    <textarea class="cp-input cp-textarea" id="postDescription"
                              name="postDescription"
                              rows="6"
                              placeholder="Describe the role, responsibilities, and what makes it exciting…"
                              required
                              maxlength="60000"></textarea>
                    <div class="cp-char-count"><span id="charCount">0</span> / 1000</div>
                </div>

                <!-- Step 6 – Application -->
                <div class="cp-section">
                    <div class="cp-section-label">
                        <span class="cp-step">6</span>
                        Application
                    </div>
                    <div class="cp-row">
                        <div class="cp-field">
                            <label class="cp-label" for="postLink">Application Link <span class="cp-req">*</span></label>
                            <input type="url" class="cp-input" id="postLink"
                                   name="postLink"
                                   placeholder="https://…"
                                   required maxlength="1000">
                        </div>
                        <div class="cp-field">
                            <label class="cp-label" for="postDeadline">Deadline</label>
                            <input type="date" class="cp-input" id="postDeadline"
                                   name="postDeadline">
                        </div>
                    </div>
                    <div class="cp-field mt-2">
                        <label class="cp-label" for="postContact">Contact Email</label>
                        <input type="email" class="cp-input" id="postContact"
                               name="postContact"
                               placeholder="hr@company.com"
                               maxlength="150">
                    </div>
                    <div class="cp-field mt-2">
                        <label class="cp-label" for="postCompanyWebsite">Company Website</label>
                        <input type="url" class="cp-input" id="postCompanyWebsite"
                               name="postCompanyWebsite"
                               placeholder="https://company.com"
                               maxlength="1000">
                    </div>
                </div>

                <!-- Actions -->
                <div class="cp-actions">
                    <a href="/frontend/pages/feed.php" class="cp-cancel-btn">Cancel</a>
                    <button type="submit" class="cp-submit-btn" id="submitPostBtn">
                        <span class="cp-submit-text">
                            <i class="bi bi-send-fill me-2"></i>Publish
                        </span>
                        <span class="cp-submit-loader d-none" id="postLoader">
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Publishing…
                        </span>
                    </button>
                </div>

            </form>

            <!-- Success state -->
            <div class="cp-success d-none" id="postSuccess">
                <div class="cp-success-icon">🚀</div>
                <h3>Published</h3>
                <p>Your post is now in the feed. INSATiens can discover and apply to it right away.</p>
                <div class="cp-success-actions">
                    <a href="/frontend/pages/feed.php" class="cp-submit-btn">← Back to Feed</a>
                    <button class="cp-cancel-btn" onclick="resetForm()">Post Another</button>
                </div>
            </div>

        </div>
    </section>
</div>

<div id="footer"></div>

<script src="/frontend/assets/js/bootstrap.bundle.min.js"></script>
<script src="/frontend/assets/js/root.js"></script>
<script src="/frontend/assets/js/createPost.js"></script>
<script>
    loadComponent("navbar", "/frontend/components/navbar.php", function () {
        initTheme();
        setActiveNav();
    });
    loadComponent("footer", "/frontend/components/footer.php");
</script>

</body>
</html>
