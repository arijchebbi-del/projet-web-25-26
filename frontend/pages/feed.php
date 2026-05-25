<?php
session_start();


require_once '../../backend/config/ConnexionDB.php';
require_once '../../backend/repository/jobRepository.php';
require_once '../../backend/repository/userRepository.php';

// AUTH
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// REPOSITORIES
$jobRepo  = new jobRepository();
$userRepo = new userRepository();

$profiles    = $userRepo->findAllProfiles();
$jobs        = $jobRepo->findAllJobs();
$internships = $jobRepo->findInternships();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Feed</title>

    <link rel="stylesheet" href="/frontend/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/frontend/assets/css/feed.css">
</head>

<body>

<div class="container mt-4">

    <h2 class="mt-4">Meet INSATiens</h2>

    <div class="d-flex gap-3 overflow-auto">

        <?php foreach ($profiles as $p): ?>

            <div class="card" style="min-width: 250px;">

                <img src="<?= $p->avatar_url ?>" class="card-img-top">

                <div class="card-body">

                    <h5>
                        <?= $p->prenom . " " . $p->nom ?>
                    </h5>

                    <p><?= $p->bio ?></p>

                    <a href="profil.php?id=<?= $p->id ?>" class="btn btn-primary w-100">
                        View Profile
                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    </div>


    <h2 class="mt-4">Jobs</h2>

    <div class="d-flex gap-3 overflow-auto">

        <?php foreach ($jobs as $job): ?>

            <div class="card" style="min-width: 250px;">

                <div class="card-body">

                    <h5><?= htmlspecialchars($job->titre) ?></h5>

                    <p><?= substr($job->description, 0, 100) ?>...</p>

                    <a href="job.php?id=<?= $job->id ?>" class="btn btn-primary w-100">
                        See more
                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <h2 class="mt-4">Internships</h2>

    <div class="d-flex gap-3 overflow-auto">

        <?php foreach ($internships as $job): ?>

            <div class="card" style="min-width: 250px;">

                <div class="card-body">

                    <h5><?= htmlspecialchars($job->titre) ?></h5>

                    <p><?= substr($job->description, 0, 100) ?>...</p>

                    <a href="job.php?id=<?= $job->id ?>" class="btn btn-primary w-100">
                        See more
                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

</body>
</html>