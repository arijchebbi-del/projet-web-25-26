<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: /frontend/pages/login.php");
    exit();
}

require_once '../../backend/config/ConnexionDB.php';
require_once '../../backend/service/profileService.php';

// Récupérer l'user_id depuis l'email en session
$conn   = ConnexionDB::getInstance();
$stmt   = $conn->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $_SESSION['email']]);
$me     = $stmt->fetch(PDO::FETCH_ASSOC);
$userId = (int)($me['id'] ?? 0);

if (!$userId) {
    header("Location: /frontend/pages/login.php");
    exit();
}

$service = new profileService();

// Traitement POST (sauvegarde du profil)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $service->saveProfile($userId, $_POST);
        header("Location: myprofile.php?saved=1");
        exit();
    } catch (Exception $e) {
        $saveError = $e->getMessage();
    }
}

// Données pour pré-remplir le modal
$data            = $service->getFullProfile($userId);
$user            = $data['user'];
$skills          = $data['skills'];
$experiences     = $data['experiences'];
$projects        = $data['projects'];
$achievements    = $data['achievements'];
$recommendations = $data['recommendations'];
$posts           = $data['posts'];

function h(?string $val): string {
    return htmlspecialchars($val ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile | Alumini</title>
  <link rel="stylesheet" href="/frontend/assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="/frontend/assets/css/profil.css">
  <link rel="stylesheet" href="/frontend/assets/css/myprofile.css">
  <link rel="stylesheet" href="/frontend/assets/css/footer_navbar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>

<div id="navbar"></div>

<?php if (isset($_GET['saved'])): ?>
  <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
    ✅ Profil sauvegardé avec succès !
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<?php if (!empty($saveError)): ?>
  <div class="alert alert-danger m-3"><?= h($saveError) ?></div>
<?php endif; ?>

<!-- Profil affiché (même rendu que profil.php) -->
<div class="container">
  <div class="profile-main">

    <img src="<?= h($user['avatar_url'] ?: '/frontend/assets/images/icon-7797704_1280.png') ?>"
         class="profile-pic" id="pageAvatarImg" alt="Photo de profil">

    <div class="profile-info">

      <div class="profile-name-row">
        <h1 class="nom"><?= h($user['prenom'] . ' ' . $user['nom']) ?></h1>
        <button type="button" class="btn-edit"
                data-bs-toggle="modal" data-bs-target="#editProfileModal">
          ✏️ Modifier
        </button>
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

      <p class="profile-bio"><?= h($user['bio'] ?: 'No bio provided.') ?></p>

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
            <i class="fas fa-globe"></i> Portfolio
          </a>
        <?php endif; ?>
      </div>

    </div>
  </div>
</div>

<!-- Sections -->
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
        <p class="text-muted">No skills yet. Click "Modifier" to add some.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Experience -->
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
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-muted">No experience yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Projects -->
  <div class="profile-section">
    <span class="section-title">Projects</span>
    <?php if ($projects): ?>
      <?php foreach ($projects as $proj): ?>
        <div class="experience-card">
          <div class="exp-icon">📁</div>
          <div>
            <p class="exp-title"><?= h($proj['title']) ?></p>
            <?php if (!empty($proj['description'])): ?>
              <p class="exp-desc"><?= h($proj['description']) ?></p>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted">No projects yet.</p>
    <?php endif; ?>
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
            <p class="achievement-meta"><?= h($ach['issuer'] ?? '') ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted">No achievements yet.</p>
    <?php endif; ?>
  </div>

  <!-- Posts -->
  <div class="profile-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <span class="section-title" style="margin-bottom:0; border:none;">Posts récents</span>
      <a href="/frontend/pages/createPost.php" class="btn btn-sm"
         style="background:#65171e; color:#fff; border-radius:8px;">+ Publier</a>
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

  <!-- Recommendations reçues -->
  <div class="profile-section">
    <span class="section-title">Recommendations reçues</span>
    <?php if ($recommendations): ?>
      <?php foreach ($recommendations as $rec): ?>
        <div class="review-card">
          <div class="review-header">
            <img src="<?= h($rec['author_avatar'] ?: '/frontend/assets/images/icon-7797704_1280.png') ?>"
                 alt="avatar" style="width:40px;height:40px;border-radius:50%;object-fit:cover;margin-right:10px;">
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

<!-- ======== MODAL EDIT PROFIL ======== -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:680px;">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">✏️ Edit Profile</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- Avatar -->
        <div class="modal-avatar-row">
          <div class="modal-avatar">
            <img src="<?= h($user['avatar_url'] ?: '/frontend/assets/images/icon-7797704_1280.png') ?>"
                 class="profile-pic" id="modalAvatarImg">
            <div class="avatar-edit-btn" title="Change photo"
                 onclick="document.getElementById('avatarUpload').click()">
              <input type="file" id="avatarUpload" style="display:none" accept="image/*"
                     onchange="uploadAvatarImage(event)">
              <i class="bi bi-camera-fill"></i>
            </div>
          </div>
          <div class="modal-avatar-info">
            <div class="name" id="modalDisplayName">
              <?= h($user['prenom'] . ' ' . $user['nom']) ?>
            </div>
            <div class="role" id="modalDisplayTagline">
              <?= h($user['tagline'] ?: 'Add a tagline') ?>
            </div>
          </div>
        </div>

        <!-- Identité -->
        <div class="modal-section">
          <div class="modal-section-title"><i class="bi bi-person"></i> Identity</div>
          <div class="row g-3">
            <div class="col-6">
              <label class="form-label">First Name</label>
              <input type="text" id="firstName" class="form-control"
                     value="<?= h($user['prenom']) ?>">
            </div>
            <div class="col-6">
              <label class="form-label">Last Name</label>
              <input type="text" id="lastName" class="form-control"
                     value="<?= h($user['nom']) ?>">
            </div>
            <div class="col-6">
              <label class="form-label">INSAT ID</label>
              <input type="text" id="insatienId" class="form-control readonly-field"
                     value="<?= h($user['insatien_id']) ?>" readonly>
            </div>
            <div class="col-6">
              <label class="form-label">Promo Year</label>
              <input type="text" id="promoYear" class="form-control"
                     value="<?= h($user['promo_year'] ?? '') ?>">
            </div>
            <div class="col-12">
              <label class="form-label">Tagline</label>
              <input type="text" id="editTagline" class="form-control"
                     value="<?= h($user['tagline'] ?? '') ?>">
            </div>
            <div class="col-12">
              <label class="form-label">Bio</label>
              <textarea id="editBio" class="form-control" rows="3"><?= h($user['bio'] ?? '') ?></textarea>
            </div>
          </div>
        </div>

        <!-- Links -->
        <div class="modal-section">
          <div class="modal-section-title"><i class="bi bi-link-45deg"></i> Links</div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label"><i class="bi bi-github"></i> GitHub</label>
              <input type="text" id="githubLink" class="form-control"
                     value="<?= h($user['github_link'] ?? '') ?>">
            </div>
            <div class="col-12">
              <label class="form-label"><i class="bi bi-linkedin"></i> LinkedIn</label>
              <input type="text" id="linkedinLink" class="form-control"
                     value="<?= h($user['linkedin_link'] ?? '') ?>">
            </div>
            <div class="col-12">
              <label class="form-label"><i class="bi bi-globe"></i> Portfolio / CV</label>
              <input type="text" id="editProfileLink" class="form-control"
                     value="<?= h($user['profile_link'] ?? '') ?>">
            </div>
          </div>
        </div>

        <!-- Skills -->
        <div class="modal-section">
          <div class="modal-section-title"><i class="bi bi-bag-check"></i> Skills</div>
          <div class="skills-chips" id="skills-chips"
               data-skills="<?= h(json_encode(array_column($skills ?: [], 'name'))) ?>">
          </div>
          <div class="skill-input-row">
            <input type="text" id="skillInput" class="form-control"
                   placeholder="Add a skill (e.g. Java, React...)"
                   onkeydown="if(event.key==='Enter'){event.preventDefault();addSkillChip();}">
            <button type="button" class="btn-add btn-add-inline" onclick="addSkillChip()">+ Add</button>
          </div>
        </div>

        <!-- Experience -->
        <div class="modal-section">
          <div class="modal-section-title"><i class="bi bi-briefcase"></i> Experience</div>
          <div id="experience-list"
               data-experiences="<?= h(json_encode($experiences ?: [])) ?>">
          </div>
          <button type="button" class="btn-add" onclick="addExperience()">+ Add Experience</button>
        </div>

        <!-- Projects -->
        <div class="modal-section">
          <div class="modal-section-title"><i class="bi bi-folder"></i> Projects</div>
          <div id="projects-list"
               data-projects="<?= h(json_encode($projects ?: [])) ?>">
          </div>
          <button type="button" class="btn-add" onclick="addProject()">+ Add Project</button>
        </div>

        <!-- Achievements -->
        <div class="modal-section">
          <div class="modal-section-title"><i class="bi bi-award"></i> Achievements</div>
          <div id="achievements-list"
               data-achievements="<?= h(json_encode($achievements ?: [])) ?>">
          </div>
          <button type="button" class="btn-add" onclick="addAchievement()">+ Add Achievement</button>
        </div>

      </div>

      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-save" id="saveBtn"
                onclick="saveProfile()">Save Changes</button>
      </div>

    </div>
  </div>
</div>

<div id="footer"></div>

<script src="/frontend/assets/js/bootstrap.bundle.min.js"></script>
<script src="/frontend/assets/js/root.js"></script>
<script src="/frontend/assets/js/myprofile.js"></script>
<script>
  loadComponent("navbar", "/frontend/components/navbar.php", function () {
    initTheme();
    setActiveNav();
  });
  loadComponent("footer", "/frontend/components/footer.php");
</script>

</body>
</html>