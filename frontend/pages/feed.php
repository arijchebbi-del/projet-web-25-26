<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: /frontend/pages/login.php");
exit();
}

require_once '../../backend/config/ConnexionDB.php';
require_once '../../backend/repository/jobRepository.php';
require_once '../../backend/repository/userRepository.php';
require_once '../../backend/repository/postRepository.php';


$jobRepo = new jobRepository();
$userRepo = new userRepository();
$postRepo = new postRepository();

$profiles = $userRepo->findAllProfiles();
$jobs = $jobRepo->findAllJobs();
$internships = $jobRepo->findInternships();
$posts = $postRepo->findAllPosts();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
              <?= htmlspecialchars($p->tagline) ?>
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
<h1 class="h1">New Posts</h1>

<div class="marquee-container">

    <button class="marquee-btn btn-left" onclick="scrollMarquee(this, -1)">
        &#60;
    </button>

    <div class="marquee-wrapper">

        <div class="marquee-track" id="posts">

            <?php if (!empty($posts)): ?>

                <?php foreach ($posts as $post): ?>

                    <div class="card feed-card">

                        <div class="card-body">

                            <div class="post-header">

                                <div class="user-info">

                                    <h5 class="card-title">
                                        <?= htmlspecialchars(($post->prenom ?? '') . ' ' . ($post->nom ?? '')) ?>
                                    </h5>

                                    <span class="post-date">
                                        <?= date('d M Y', strtotime($post->created_at)) ?>
                                    </span>

                                </div>

                            </div>

                            <p class="card-text">
                                <?= nl2br(htmlspecialchars(substr($post->content, 0, 150))) ?>

                                <?php if (strlen($post->content) > 150): ?>
                                    ...
                                <?php endif; ?>
                            </p>

                            <a href="profil.php?id=<?= $post->userId ?>" class="btn btn-primary w-100">
                              View Profile
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <p class="no-posts">No posts available.</p>

            <?php endif; ?>

        </div>

    </div>

    <button class="marquee-btn btn-right" onclick="scrollMarquee(this, 1)">
        &#62;
    </button>

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
