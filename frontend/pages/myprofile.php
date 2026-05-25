<<<<<<< HEAD
﻿<?php
/**
 * myprofile.php — Page "Mon Profil" (utilisateur connecté).
 *
 * CORRECTIONS :
 *  - Suppression du include __DIR__ . '/profil.php?id=...' (impossible avec query string)
 *  - Le rendu du profil est délégué à loadProfileSection() en JS (fetch vers profil.php?embed=1)
 *  - Plus de double rendu : une seule approche (JS fetch)
 *  - session_start() protégé par session_status()
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../../backend/config/ConnexionDB.php';
require_once '../../backend/services/ProfileService.php';

$service = new ProfileService();
$userId  = (int) $_SESSION['user_id'];

// Traitement du formulaire POST (sauvegarde du profil)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $service->saveProfile($userId, $_POST);
        header('Location: myprofile.php');
        exit;
    } catch (\Exception $e) {
        $error = $e->getMessage();
    }
}

// Données pour pré-remplir le modal d'édition
$data            = $service->getFullProfile($userId);
$user            = $data['user'];
$skills          = $data['skills'];
$experiences     = $data['experiences'];
$projects        = $data['projects'];
$achievements    = $data['achievements'];
$recommendations = $data['recommendations'];

function h(?string $val): string
{
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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
=======
<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: /frontend/pages/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/frontend/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/frontend/assets/css/profil.css">
    <link rel="stylesheet" href="/frontend/assets/css/myprofile.css">
    <link rel="stylesheet" href="/frontend/assets/css/footer_navbar.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <title>My Profile</title>
>>>>>>> 15f971717fb73f5145aa78a5ccea03e08145af33
</head>
<body>

  <div id="navbar"></div>

  <!--
    CORRECTION : le profil est chargé via loadProfileSection() en JS (fetch AJAX).
    On ne fait plus de include PHP avec query string (impossible et source de bugs).
    La div #profile-section sert de conteneur cible pour l'injection HTML.
  -->
  <div id="profile-section">
    <div class="d-flex justify-content-center align-items-center" style="min-height:200px;">
      <div class="spinner-border text-danger" role="status">
        <span class="visually-hidden">Chargement…</span>
      </div>
    </div>
  </div>

  <!-- ══════════════ Modal : Edit Profile ══════════════ -->
  <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:680px;">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">✏️ Edit Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">

          <!-- Avatar + Nom -->
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

          <!-- Identity -->
          <div class="modal-section">
            <div class="modal-section-title"><i class="bi bi-person"></i> Identity</div>
            <div class="row g-3">
              <div class="col-6">
                <label class="form-label">First Name</label>
                <input type="text" id="firstName" class="form-control"
                       placeholder="First name" value="<?= h($user['prenom']) ?>">
              </div>
              <div class="col-6">
                <label class="form-label">Last Name</label>
                <input type="text" id="lastName" class="form-control"
                       placeholder="Last name" value="<?= h($user['nom']) ?>">
              </div>
              <div class="col-6">
                <label class="form-label">INSAT ID</label>
                <input type="text" id="insatienId" class="form-control readonly-field"
                       value="<?= h($user['insatien_id']) ?>" readonly>
              </div>
              <div class="col-6">
                <label class="form-label">Promo Year</label>
                <input type="text" id="promoYear" class="form-control"
                       placeholder="e.g. 2028" value="<?= h($user['promo_year'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label class="form-label">Tagline</label>
                <input type="text" id="editTagline" class="form-control"
                       placeholder="e.g. Software Engineer at INSAT"
                       value="<?= h($user['tagline'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label class="form-label">Bio</label>
                <textarea id="editBio" class="form-control" rows="3"
                          placeholder="Tell us about yourself..."><?= h($user['bio'] ?? '') ?></textarea>
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
                       placeholder="https://github.com/username"
                       value="<?= h($user['github_link'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label class="form-label"><i class="bi bi-linkedin"></i> LinkedIn</label>
                <input type="text" id="linkedinLink" class="form-control"
                       placeholder="https://linkedin.com/in/username"
                       value="<?= h($user['linkedin_link'] ?? '') ?>">
              </div>
              <div class="col-12">
                <label class="form-label"><i class="bi bi-globe"></i> Personal Link</label>
                <input type="text" id="editProfileLink" class="form-control"
                       placeholder="Portfolio, website..."
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
          <button type="button" class="btn btn-cancel" id="cancelBtn"
                  data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-save" id="saveBtn"
                  onclick="saveProfile()">Save Changes</button>
        </div>

      </div>
    </div>
  </div>

  <div class="footer" id="footer"></div>

  <script src="/frontend/assets/js/bootstrap.bundle.min.js"></script>
  <script src="/frontend/assets/js/root.js"></script>
  <script src="/frontend/assets/js/myprofile.js"></script>
  <script>
    const CURRENT_USER_ID = <?= $userId ?>;

    loadComponent("navbar", "/frontend/components/navbar.php", function () {
      initTheme();
      setActiveNav();
    });
    loadComponent("footer", "/frontend/components/footer.php");

    // CORRECTION : seul endroit où le profil est chargé (plus d'include PHP cassé).
    // loadProfileSection() injecte le HTML de profil.php?embed=1 dans #profile-section.
    loadProfileSection("/frontend/pages/profil.php?id=" + CURRENT_USER_ID + "&embed=1");
  </script>

</body>
</html>
