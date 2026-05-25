<?php
session_start();

require_once '../../backend/config/ConnexionDB.php';
require_once '../../backend/repository/jobRepository.php';

$jobRepo = new jobRepository();

$id = $_GET['id'] ?? null;

if (!$id) {
    die("No job ID provided");
}

$job = $jobRepo->findById($id);

if (!$job) {
    die("Job not found");
}
?>
<!--codek ya talel hedha 
  <script src="/frontend/assets/js/auth.js"></script>
  <script>
    requireAuth();
  </script>
    <title>Alumini | Jobs</title>
    <link rel="stylesheet" href="/frontend/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/frontend/assets/css/footer_navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/frontend/assets/css/job.css">
</head>
 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($job->titre) ?> | Alumni
    </title>

    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/post.css">
    <link rel="stylesheet" href="../assets/css/footer_navbar.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="../assets/js/auth.js"></script>
    <script>
        requireAuth();
    </script>
</head>

<body id="body">

<div id="navbar"></div>
<div class="page-content container-fluid px-4 py-4">

    <div class="row justify-content-center">

        <div class="col-lg-10">

            <!-- job card -->
            <div class="card shadow-sm job-detail-card">

                <div class="card-body p-4">

                    <!-- header -->
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">

                        <div>
                            <h1 class="job-title mb-2">
                                <?= htmlspecialchars($job->titre) ?>
                            </h1>

                            <h5 class="text-muted">
                                <?= htmlspecialchars($job->entreprise) ?>
                            </h5>
                        </div>

                        <div>
                            <span class="badge bg-primary fs-6 p-2">
                                <?= htmlspecialchars($job->job_type) ?>
                            </span>
                        </div>

                    </div>

                    <hr>
                    <div class="row g-3 mb-4">

                        <div class="col-md-4">
                            <div class="job-meta-box">
                                <small class="text-muted">Job Mode</small>
                                <div>
                                    <?= htmlspecialchars($job->job_mode) ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="job-meta-box">
                                <small class="text-muted">Required Experience</small>
                                <div>
                                    <?= htmlspecialchars($job->req_experience) ?> years
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="job-meta-box">
                                <small class="text-muted">Salary</small>
                                <div>
                                    <?= htmlspecialchars($job->salary_min) ?>
                                    -
                                    <?= htmlspecialchars($job->salary_max) ?>
                                    DT
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="job-meta-box">
                                <small class="text-muted">Country</small>
                                <div>
                                    <?= htmlspecialchars($job->country_id) ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="job-meta-box">
                                <small class="text-muted">City</small>
                                <div>
                                    <?= htmlspecialchars($job->city_id) ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="job-meta-box">
                                <small class="text-muted">Publication Date</small>
                                <div>
                                    <?= htmlspecialchars($job->date_publication) ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="job-meta-box">
                                <small class="text-muted">Deadline</small>
                                <div>
                                    <?= htmlspecialchars($job->deadline) ?>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="mb-4">

                        <h4>Description</h4>

                        <p class="job-text">
                            <?= nl2br(htmlspecialchars($job->description)) ?>
                        </p>

                    </div>

                    <div class="mb-4">

                        <h4>Requirements</h4>

                        <p class="job-text">
                            <?= nl2br(htmlspecialchars($job->requirements)) ?>
                        </p>

                    </div>

                    <div class="mb-4">

                        <h4>Responsibilities</h4>

                        <p class="job-text">
                            <?= nl2br(htmlspecialchars($job->responsibilities)) ?>
                        </p>

                    </div>
                    <div class="mb-4">

                        <h4>Links & Contact</h4>

                        <div class="d-flex flex-column gap-2">

                            <?php if (!empty($job->company_link)): ?>
                                <a href="<?= htmlspecialchars($job->company_link) ?>"
                                   target="_blank"
                                   class="btn btn-outline-primary">
                                    Company Website
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($job->application_link)): ?>
                                <a href="<?= htmlspecialchars($job->application_link) ?>"
                                   target="_blank"
                                   class="btn btn-primary">
                                    Apply Now
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($job->contact_email)): ?>
                                <a href="mailto:<?= htmlspecialchars($job->contact_email) ?>"
                                   class="btn btn-outline-dark">
                                    <?= htmlspecialchars($job->contact_email) ?>
                                </a>
                            <?php endif; ?>

                        </div>

                    </div>

                </div>

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
</script>

</body>
</html>