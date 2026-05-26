<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: /frontend/pages/login.php");
    exit();
}

require_once '../../backend/config/ConnexionDB.php';
require_once '../../backend/repository/jobRepository.php';

$jobRepo = new jobRepository();

/*filters*/
$title = $_GET['title'] ?? '';
$country = $_GET['country'] ?? '';
$city = $_GET['city'] ?? '';
$jobType = $_GET['job_type'] ?? '';
$remote = $_GET['remote'] ?? '';
$onsite = $_GET['onsite'] ?? '';
$hybrid = $_GET['hybrid'] ?? '';
$salary = $_GET['salary'] ?? 0;

/*data */
$jobs = $jobRepo->findFiltered(
    $title,
    $country,
    $city,
    $jobType,
    $remote,
    $salary,
    $onsite,
    $hybrid
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Alumni | Jobs</title>

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/job.css">
    <link rel="stylesheet" href="../assets/css/footer_navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div id="navbar"></div>

<div class="page-content container-fluid px-4">

    <div class="row g-4">

        <!-- filters-->
        <div class="col-lg-3">

            <form method="GET" class="card shadow-sm sticky-top" style="top:90px;">
                <div class="card-body">

                    <h5>Filters</h5>
                    <hr>

                    <!-- job type -->
                    <div class="mb-3">
                        <h6 class="text-muted small">Job Type</h6>

                        <div class="bubble-group">
                            <div class="bubble <?= $jobType==''?'active':'' ?>" data-value="">All</div>
                            <div class="bubble" data-value="full-time">Full-time</div>
                            <div class="bubble" data-value="part-time">Part-time</div>
                            <div class="bubble" data-value="internship">Internship</div>
                        </div>

                        <input type="hidden" name="job_type" id="jobTypeInput" value="<?= htmlspecialchars($jobType) ?>">
                    </div>

                    <!-- mode-->
                    <div class="mb-3">
                        <h6 class="text-muted small">Work Mode</h6>

                        <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="remoteCheck"
                                   name="remote"
                                   value="1"
                                   <?= $remote=='1'?'checked':'' ?>>
                            <label class="form-check-label">Remote</label>
                        </div>
                         <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="onsiteCheck"
                                   name="onsite"
                                   value="1"
                                   <?= $onsite=='1'?'checked':'' ?>>
                            <label class="form-check-label">Onsite</label>
                        </div>
                         <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="hybridCheck"
                                   name="hybrid"
                                   value="1"
                                   <?= $hybrid=='1'?'checked':'' ?>>
                            <label class="form-check-label">Hybrid</label>
                        </div>
                    </div>

                    <!-- Salaire -->
                    <div class="mb-3">
                        <h6 class="text-muted small">Salary</h6>

                        <input type="range"
                               class="form-range"
                               min="100"
                               max="5000"
                               value="<?= htmlspecialchars($salary) ?>"
                               id="range4"
                               name="salary">

                        <div class="d-flex justify-content-between small mt-1">
                            <span>100</span>
                            <span id="rangeValue"><?= $salary ?> DT</span>
                            <span>5000</span>
                        </div>
                    </div>

                </div>
            </form>

        </div>

        <!--main -->
        <div class="col-lg-9">

                <!-- Search -->
                <div class="card shadow-sm mb-4">
    <div class="card-body">

        <form method="GET" class="row g-2">

            <div class="col-md-5">
                <input type="text" name="title" class="form-control"
                       placeholder="Job title..."
                       value="<?= htmlspecialchars($title) ?>">
            </div>

            <div class="col-md-3">
                <select name="country" class="form-select">
                    <option value="">Country</option>
                    <option value="1" <?= $country=='1'?'selected':'' ?>>Tunisia</option>
                    <option value="2" <?= $country=='2'?'selected':'' ?>>France</option>
                    <option value="3" <?= $country=='3'?'selected':'' ?>>Canada</option>
                    <option value="4" <?= $country=='4'?'selected':'' ?>>Germany</option>
                </select>
            </div>

            <div class="col-md-3">
                <select name="city" class="form-select">
                    <option value="">City</option>
                    <option value="1" <?= $city=='1'?'selected':'' ?>>Tunis</option>
                    <option value="2" <?= $city=='2'?'selected':'' ?>>Sfax</option>
                    <option value="3" <?= $city=='3'?'selected':'' ?>>Sousse</option>
                    <option value="4" <?= $city=='4'?'selected':'' ?>>Nabeul</option>
                    <option value="5" <?= $city=='5'?'selected':'' ?>>Monastir</option>
                    <option value="6" <?= $city=='6'?'selected':'' ?>>Ariana</option>
                    <option value="7" <?= $city=='7'?'selected':'' ?>>Paris</option>
                    <option value="8" <?= $city=='8'?'selected':'' ?>>Lyon</option>
                    <option value="9" <?= $city=='9'?'selected':'' ?>>Montreal</option>
                    <option value="10" <?= $city=='10'?'selected':'' ?>>Berlin</option>
                </select>
            </div>

            <div class="col-md-1 d-grid">
                <button class="btn btn-primary">Search</button>
            </div>

            <div class="col-12 d-flex justify-content-end">
                <a class="btn btn-outline-primary" href="/frontend/pages/old%20createpost.php">
                    + Post a Job
                </a>
            </div>

                </form>

            </div>
        </div>
            

            <!-- JOBS -->
            <div class="row g-3">

                <?php foreach ($jobs as $job): ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="card job-card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">

                                <h5><?= htmlspecialchars($job->titre) ?></h5>

                                <div class="text-muted small mb-2">
                                    <?= htmlspecialchars($job->job_type) ?> •
                                    <?= htmlspecialchars($job->job_mode) ?>
                                </div>

                                <p class="flex-grow-1">
                                    <?= htmlspecialchars(substr($job->description,0,110)) ?>...
                                </p>

                                <a href="post.php?id=<?= $job->id ?>" class="btn btn-primary mt-auto">
                                    View details
                                </a>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>

        </div>

    </div>
</div>

<div id="footer"></div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/root.js"></script>

<script>
loadComponent("navbar", "../components/navbar.php", function () {
    initTheme();
    setActiveNav();
});
loadComponent("footer", "../components/footer.php");


const bubbles = document.querySelectorAll(".bubble");
const jobTypeInput = document.getElementById("jobTypeInput");

bubbles.forEach(b => {
    b.addEventListener("click", () => {
        bubbles.forEach(x => x.classList.remove("active"));
        b.classList.add("active");
        jobTypeInput.value = b.dataset.value;
        b.closest("form").submit();
    });
});


const range = document.getElementById("range4");
const rangeValue = document.getElementById("rangeValue");

range.addEventListener("input", () => {
    rangeValue.textContent = range.value + " DT";
});

range.addEventListener("change", () => {
    range.form.submit();
});


document.getElementById("remoteCheck").addEventListener("change", function () {
    this.form.submit();
});

document.getElementById("onsiteCheck").addEventListener("change", function () {
    this.form.submit();
});

document.getElementById("hybridCheck").addEventListener("change", function () {
    this.form.submit();
});
</script>

</body>
</html>
