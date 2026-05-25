<?php
<<<<<<< HEAD
/**
 * profil.php — Affichage du profil d'un utilisateur.
 *
 * CORRECTIONS :
 *  - session_start() protégé par session_status() pour éviter le double appel
 *    quand ce fichier est inclus depuis myprofile.php
 *  - Les redirections header() sont ignorées en mode embed (isEmbed)
 *    pour éviter les boucles de redirection et les "headers already sent"
 */

if (session_status() === PHP_SESSION_NONE) {   // CORRECTION : pas de double session_start
    session_start();
}

// En mode embed, on suppose que l'auth a déjà été vérifiée par myprofile.php
if (!isset($_SESSION['is_logged_in']) || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../../backend/config/ConnexionDB.php';
require_once '../../backend/services/ProfileService.php';

$service  = new ProfileService();
$userId   = (int) $_SESSION['user_id'];
$targetId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Mode embed : inclus depuis myprofile.php — on ne redirige PAS
$isEmbed = isset($_GET['embed']) && $_GET['embed'] === '1';

if (!$targetId) {
    // CORRECTION : en embed on ne peut pas rediriger (headers déjà envoyés)
    if (!$isEmbed) {
        header('Location: recherche.php');
        exit;
    }
    echo '<p class="text-danger">Profil introuvable.</p>';
    return;   // stoppe l'include sans tuer la page parente
}

// Si c'est son propre profil et qu'on n'est PAS en mode embed → rediriger
if ($targetId === $userId && !$isEmbed) {
    header('Location: myprofile.php');
    exit;
}

// Traitement du formulaire de recommandation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['texte'])) {
    try {
        $service->addRecommendation($userId, $targetId, $_POST['texte']);
        if (!$isEmbed) {
            header('Location: profil.php?id=' . $targetId);
            exit;
        }
    } catch (\Exception $e) {
        $error = $e->getMessage();
    }
}

$data            = $service->getFullProfile($targetId);
$user            = $data['user'];
$skills          = $data['skills'];
$experiences     = $data['experiences'];
$projects        = $data['projects'];
$achievements    = $data['achievements'];
$recommendations = $data['recommendations'];
$posts           = $data['posts'];

if (!$user) {
    if (!$isEmbed) {
        header('Location: recherche.php');
        exit;
    }
    echo '<p class="text-danger">Utilisateur introuvable.</p>';
    return;
}

$isOwner = ($targetId === $userId);

function h(?string $val): string
{
    return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8');
}

// ── En mode embed on ne génère PAS le squelette HTML complet ─────────────────
if (!$isEmbed):
=======
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: /frontend/pages/login.php");
    exit();
}
>>>>>>> 15f971717fb73f5145aa78a5ccea03e08145af33
?>
<!doctype html>
<html lang="fr">
<head>
  <title>Alumini | <?= h($user['prenom'] . ' ' . $user['nom']) ?></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="/frontend/assets/css/profil.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
        crossorigin="anonymous">
  <link rel="stylesheet" href="/frontend/assets/css/footer_navbar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div id="navbar"></div>
<?php endif; ?>

<!-- ══════════════ En-tête profil ══════════════ -->
<div class="container">
  <div class="profile-main">

    <img src="<?= h($user['avatar_url'] ?: '/frontend/assets/images/icon-7797704_1280.png') ?>"
         class="profile-pic" alt="Photo de profil">

    <div class="profile-info">

      <div class="profile-name-row">
        <h1 class="nom"><?= h($user['prenom'] . ' ' . $user['nom']) ?></h1>
        <?php if ($isOwner): ?>
          <button id="editBtn" type="button" class="btn-edit"
                  data-bs-toggle="modal" data-bs-target="#editProfileModal">
            ✏️ Modifier
          </button>
        <?php endif; ?>
      </div>

      <div class="profile-meta">
        <?php if (!empty($user['filiere'])): ?>
          <span class="meta-badge"><?= h($user['filiere']) ?> · <?= h($user['parcours']) ?></span>
          <span class="meta-sep">·</span>
        <?php endif; ?>
        <span class="meta-badge">Promo <span class="num-id"><?= h($user['promo_year'] ?? 'N/A') ?></span></span>
        <span class="meta-sep">·</span>
        <span class="meta-badge">ID <span class="num-id"><?= h($user['insatien_id']) ?></span></span>
      </div>

      <?php if (!empty($user['tagline'])): ?>
        <p class="profile-tagline"><?= h($user['tagline']) ?></p>
      <?php endif; ?>

      <p class="profile-bio info-text"><?= h($user['bio'] ?: 'No bio provided.') ?></p>

      <div class="profile-links">
        <?php if (!empty($user['github_link'])): ?>
          <a href="<?= h($user['github_link']) ?>" class="profile-link-btn" target="_blank">
            <i class="fab fa-github"></i> GitHub
          </a>
        <?php endif; ?>
        <?php if (!empty($user['linkedin_link'])): ?>
          <a href="<?= h($user['linkedin_link']) ?>" class="profile-link-btn" target="_blank">
            <i class="fab fa-linkedin"></i> LinkedIn
          </a>
        <?php endif; ?>
        <?php if (!empty($user['profile_link'])): ?>
          <a href="<?= h($user['profile_link']) ?>" class="profile-link-btn" target="_blank">
            <i class="fas fa-file-alt"></i> CV
          </a>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<!-- ══════════════ Sections ══════════════ -->
<div class="container" style="max-width:900px; margin:0 auto; padding:0 20px 60px;">

  <!-- Skills -->
  <div class="profile-section">
    <span class="section-title">Skills</span>
    <div class="skills-grid">
      <?php if ($skills): ?>
        <?php foreach ($skills as $skill): ?>
          <span class="skill-badge"><?= h($skill['name']) ?></span>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-muted">No skills added yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Expérience -->
  <div class="profile-section">
    <span class="section-title">Experience</span>
    <div class="experience-list">
      <?php if ($experiences): ?>
        <?php foreach ($experiences as $exp): ?>
          <div class="experience-card">
            <div class="exp-icon">🏢</div>
            <div>
              <p class="exp-title"><?= h($exp['entreprise'] ?? '') ?></p>
              <p class="exp-meta">
                <?= h(ucfirst($exp['experience_type'])) ?> ·
                <?= h($exp['date_debut'] ?? '') ?> –
                <?= !empty($exp['date_fin']) ? h($exp['date_fin']) : 'Present' ?>
              </p>
              <?php if (!empty($exp['description'])): ?>
                <p class="exp-desc"><?= h($exp['description']) ?></p>
              <?php endif; ?>
              <?php if (!empty($exp['lien'])): ?>
                <a href="<?= h($exp['lien']) ?>" class="exp-link" target="_blank">
                  <i class="fas fa-external-link-alt"></i> Voir le lien
                </a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-muted">No experience added yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Projects -->
  <div class="profile-section">
    <span class="section-title">Projects</span>
    <div class="projects-list">
      <?php if ($projects): ?>
        <?php foreach ($projects as $proj): ?>
          <div class="experience-card">
            <div class="exp-icon">📁</div>
            <div>
              <p class="exp-title"><?= h($proj['title']) ?></p>
              <p class="exp-meta">
                <?= h($proj['date_debut'] ?? '') ?>
                <?= !empty($proj['date_fin']) ? '– ' . h($proj['date_fin']) : '' ?>
                <?php if (!empty($proj['lien'])): ?>
                  · <a href="<?= h($proj['lien']) ?>" target="_blank">Voir le projet</a>
                <?php endif; ?>
              </p>
              <?php if (!empty($proj['description'])): ?>
                <p class="exp-desc"><?= h($proj['description']) ?></p>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-muted">No projects added yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Achievements -->
  <div class="profile-section">
    <span class="section-title">Achievements</span>
    <?php if ($achievements): ?>
      <?php foreach ($achievements as $ach): ?>
        <div class="achievement-card">
          <span class="achievement-icon">🏆</span>
          <div>
            <p class="achievement-title"><?= h($ach['title']) ?></p>
            <p class="achievement-meta">
              <?= h($ach['issuer'] ?? '') ?>
              <?= !empty($ach['date_obtained']) ? '· ' . h($ach['date_obtained']) : '' ?>
            </p>
            <?php if (!empty($ach['description'])): ?>
              <p class="achievement-desc"><?= h($ach['description']) ?></p>
            <?php endif; ?>
            <?php if (!empty($ach['lien'])): ?>
              <a href="<?= h($ach['lien']) ?>" class="exp-link" target="_blank">
                <i class="fas fa-external-link-alt"></i> Voir le certificat
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted">No achievements yet.</p>
    <?php endif; ?>
  </div>

  <!-- Posts récents -->
  <div class="profile-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <span class="section-title" style="margin-bottom:0; border:none;">Posts récents</span>
      <?php if ($isOwner): ?>
        <a href="/frontend/pages/createPost.html" class="btn btn-sm"
           style="background:#65171e; color:#fff; border-radius:8px;">
          + Publier
        </a>
      <?php endif; ?>
    </div>
    <?php if (!empty($posts)): ?>
      <?php foreach ($posts as $post): ?>
        <div class="review-card" style="margin-bottom:12px;">
          <div class="review-time"><?= h($post['created_at']) ?></div>
          <div class="review-text"><?= h($post['content']) ?></div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted">No posts yet.</p>
    <?php endif; ?>
  </div>

  <!-- Recommendations -->
  <div class="profile-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <span class="section-title" style="margin-bottom:0; border:none;">Recommendations</span>
      <?php if (!$isOwner): ?>
        <button class="btn btn-sm"
                style="background:#65171e; color:#fff; border-radius:8px;"
                data-bs-toggle="modal" data-bs-target="#recommendModal">
          + Recommander
        </button>
      <?php endif; ?>
    </div>
    <?php if ($recommendations): ?>
      <?php foreach ($recommendations as $rec): ?>
        <div class="review-card">
          <div class="review-header">
            <img src="<?= h($rec['author_avatar'] ?: '/frontend/assets/images/icon-7797704_1280.png') ?>"
                 alt="avatar">
            <div>
              <div class="review-name"><?= h($rec['author_prenom'] . ' ' . $rec['author_nom']) ?></div>
              <div class="review-time"><?= h($rec['created_at']) ?></div>
            </div>
          </div>
          <div class="review-text"><?= h($rec['texte']) ?></div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted">No recommendations yet.</p>
    <?php endif; ?>
  </div>

</div>

<!-- Modal Recommandation (uniquement si pas le propriétaire) -->
<?php if (!$isOwner): ?>
<div class="modal fade" id="recommendModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">✍️ Write a Recommendation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="profil.php?id=<?= $targetId ?>">
        <div class="modal-body">
          <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= h($error) ?></div>
          <?php endif; ?>
          <textarea name="texte" class="form-control" rows="4"
                    placeholder="Share your experience with this person..."></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"
                  style="background:#65171e; border-color:#65171e;">Send</button>
        </div>
      </form>
    </div>
  </div>
<<<<<<< HEAD
</div>
<?php endif; ?>
=======
<!-- Posts -->
        <div class="profile-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="section-title" style="margin-bottom:0; border:none;">Posts récents</span>
                <button id="addPostBtn"
                        class="btn btn-sm"
                        style="background:#65171e; color:#fff; border-radius:8px; display:none;"
                        onclick="window.location.href='/frontend/pages/createPost.php'">
                    + Publier
                </button>
            </div>
            <div id="postsContainer"></div>
        </div>

<!-- Recommendations -->
        <div class="profile-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="section-title" style="margin-bottom:0; border:none;">Recommendations</span>
                <button class="btn btn-sm"
                        style="background:#65171e; color:#fff; border-radius:8px;"
                        data-bs-toggle="modal"
                        data-bs-target="#recommendModal">
                    + Recommander
                </button>
            </div>
            <div id="recommendationsContainer"></div>
        </div>
 
    </div>

<!-- Recommendation Modal -->
    <div class="modal fade" id="recommendModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Write Recommendation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <textarea id="recommendationText" class="form-control" rows="4"
                        placeholder="Share your experience working with this person..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitRecommendation()">Send</button>
                </div>
            </div>
        </div>
    </div>
>>>>>>> 15f971717fb73f5145aa78a5ccea03e08145af33

<?php if (!$isEmbed): ?>
  <div class="footer" id="footer"></div>
  <script src="/frontend/assets/js/bootstrap.bundle.min.js"></script>
  <script src="/frontend/assets/js/root.js"></script>
  <script>
    loadComponent("navbar", "/frontend/components/navbar.php", function () {
      initTheme();
      setActiveNav();
    });
    loadComponent("footer", "/frontend/components/footer.php");
<<<<<<< HEAD
  </script>
</body>
</html>
<?php endif; ?>
=======
</script>
</html>
>>>>>>> 15f971717fb73f5145aa78a5ccea03e08145af33
