<?php
session_start();

require_once '../../backend/config/ConnexionDB.php';
require_once '../../backend/repository/jobRepository.php';
require_once '../../backend/repository/userRepository.php';

$jobRepo = new jobRepository();
$userRepo = new userRepository();

$profiles = $userRepo->findAllProfiles();
$jobs = $jobRepo->findAllJobs();
$internships = $jobRepo->findInternships();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!--<script src="/frontend/assets/js/auth.js"></script>-->
  <script>
    requireAuth();
  </script>
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">

  <link rel="stylesheet" href="../assets/css/feed.css">
  <link rel="stylesheet" href="../assets/css/footer_navbar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <title>Alumni | Feed</title>
</head>

<body>

<!-- NAVBAR -->
<div id="navbar"></div>

<h1 class="h1">Meet INSATiens</h1>

<div class="marquee-container">
  <button class="marquee-btn btn-left" onclick="scrollMarquee(this, -1)">&#60;</button>

  <div class="marquee-wrapper">
    <div class="marquee-track" id="profiles">

      <?php foreach ($profiles as $p): ?>
        <div class="profile-card card">
          <img src="<?= htmlspecialchars($p->avatar_url) ?>" class="card-img-top">

          <div class="card-body">
            <h5 class="card-title">
              <?= htmlspecialchars($p->prenom . " " . $p->nom) ?>
            </h5>

            <p class="card-text">
              <?= htmlspecialchars($p->bio) ?>
            </p>

            <a href="profil.php?id=<?= $p->id ?>" class="btn btn-primary w-100">
              View Profile
            </a>
          </div>
        </div>
      <?php endforeach; ?>

    </div>
  </div>

  <button class="marquee-btn btn-right" onclick="scrollMarquee(this, 1)">&#62;</button>
</div>

<h1 class="h1">Job Offers</h1>

<div class="marquee-container">
  <button class="marquee-btn btn-left" onclick="scrollMarquee(this, -1)">&#60;</button>

  <div class="marquee-wrapper">
    <div class="marquee-track" id="jobs">

      <?php foreach ($jobs as $job): ?>
        <div class="card">
          <div class="card-body">

            <h5 class="card-title">
              <?= htmlspecialchars($job->titre) ?>
            </h5>

            <p class="card-text">
              <?= htmlspecialchars(substr($job->description, 0, 100)) ?>...
            </p>

            <a href="post.php?id=<?= $job->id ?>" class="btn btn-primary"></a>
              See more
            </a>

          </div>
        </div>
      <?php endforeach; ?>

    </div>
  </div>

  <button class="marquee-btn btn-right" onclick="scrollMarquee(this, 1)">&#62;</button>
</div>


<h1 class="h1">Internship Offers</h1>

<div class="marquee-container">
  <button class="marquee-btn btn-left" onclick="scrollMarquee(this, -1)">&#60;</button>

  <div class="marquee-wrapper">
    <div class="marquee-track" id="internships">

      <?php foreach ($internships as $job): ?>
        <div class="card">
          <div class="card-body">

            <h5 class="card-title">
              <?= htmlspecialchars($job->titre) ?>
            </h5>

            <p class="card-text">
              <?= htmlspecialchars(substr($job->description, 0, 100)) ?>...
            </p>

            <a href="post.php?id=<?= $job->id ?>" class="btn btn-primary">
              See more
            </a>

          </div>
        </div>
      <?php endforeach; ?>

    </div>
  </div>

  <button class="marquee-btn btn-right" onclick="scrollMarquee(this, 1)">&#62;</button>
</div>

<!-- FOOTER -->
<div class="footer" id="footer"></div>

<!-- SCRIPTS -->
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/root.js"></script>
<script src="../assets/js/feed.js"></script>

<script>
loadComponent("navbar", "../components/navbar.php", function () {
  initTheme();
  setActiveNav();
});

loadComponent("footer", "../components/footer.php");
</script>

<script>
function scrollMarquee(button, direction) {
  const container = button.parentElement;
  const wrapper = container.querySelector('.marquee-wrapper');
  const scrollAmount = 320;

  wrapper.scrollTo({
    left: wrapper.scrollLeft + direction * scrollAmount,
    behavior: 'smooth'
  });
}
</script>

</body>
</html>