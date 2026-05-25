<!doctype html>
<html lang="en">
  <head>
    <title>Alumini | Profile</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="/frontend/assets/js/auth.js"></script>
    
    <link rel="stylesheet" href="/frontend/assets/css/profil.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
     <link rel="stylesheet" href="/frontend/assets/css/footer_navbar.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  </head>
  <body>
    <!-- Navbar will be injected here -->
    <div id="navbar"></div>
   <div class="container">
      <div class="profile-main">

    <!-- Photo -->
    <img src="/frontend/assets/images/icon-7797704_1280.png" 
         class="profile-pic" 
         alt="Photo de profil">

    <!-- Infos -->
    <div class="profile-info">

        <!-- Ligne 1 : Nom + bouton Edit -->
        <div class="profile-name-row">
            <h1 class="nom">Nom Prénom</h1>
            <button id="editBtn" 
                    type="button" 
                    class="btn-edit invisible"
                    data-bs-toggle="modal" 
                    data-bs-target="#editProfileModal">
                ✏️ Modifier
            </button>
        </div>

        <!-- Ligne 2 : Meta (filière, promo, id) -->
        <div class="profile-meta">
            <span class="meta-badge">GL · Génie Logiciel</span>
            <span class="meta-sep">·</span>
            <span class="meta-badge">Promo <span class="num-id">2028</span></span>
            <span class="meta-sep">·</span>
            <span class="meta-badge">ID <span class="num-id">2500123</span></span>
        </div>

        <!-- Ligne 3 : Bio -->
        <p class="profile-bio info-text">
            Hi! I'm a passionate developer who loves building modern web applications.
        </p>

        <!-- Ligne 4 : Liens sociaux -->
        <div class="profile-links">
            <a href="#" class="profile-link-btn">
                <i class="fab fa-github"></i> GitHub
            </a>
            <a href="#" class="profile-link-btn">
                <i class="fab fa-linkedin"></i> LinkedIn
            </a>
            <a href="#" class="profile-link-btn">
                <i class="fas fa-envelope"></i> Gmail
            </a>
            <a href="#" class="profile-link-btn">
                <i class="fas fa-file-alt"></i> CV
            </a>
        </div>

    </div>
</div>

</div>
   <div class="container" style="max-width:900px; margin: 0 auto; padding: 0 20px 60px;">

  <!-- Skills -->
  <div class="profile-section">
    <span class="section-title">Skills</span>
    <div class="skills-grid" id="skillsContainer">
      <span class="skill-badge">Java</span>
      <span class="skill-badge">SQL</span>
    </div>
  </div>

  <!-- Expérience -->
  <div class="profile-section">
    <span class="section-title">Experience</span>
    <div class="experience-list" id="experienceContainer">

      <div class="experience-card">
        <div class="exp-icon">🏢</div>
        <div>
          <p class="exp-title">Software Intern</p>
          <p class="exp-meta">Vermeg · Juin 2023 – Août 2023</p>
          <p class="exp-desc">Worked on Spring Boot microservices.</p>
        </div>
      </div>

    </div>
  </div>

  <!-- Projects -->
  <div class="profile-section">
    <span class="section-title">Projects</span>
    <div class="projects-grid" id="projectsContainer">

      <div class="project-card">
        <p class="project-title">Smart Campus</p>
        <p class="project-desc">Web platform for university management.</p>
        <div class="project-skills">
          <span class="project-skill-tag">Spring Boot</span>
          <span class="project-skill-tag">SQL</span>
          <span class="project-skill-tag">Docker</span>
        </div>
        <a href="#" class="project-link">Voir le projet →</a>
      </div>

    </div>
  </div>

  <!-- Achievements -->
  <div class="profile-section">
    <span class="section-title">Achievements</span>
    <div id="achievementsContainer">

      <div class="achievement-card">
        <span class="achievement-icon">🏆</span>
        <div>
          <p class="achievement-title">Hackathon Winner</p>
          <p class="achievement-meta">INSAT · Avril 2024</p>
          <p class="achievement-desc">Won first place in INSAT Hackathon.</p>
        </div>
      </div>

    </div>
  </div>
<!-- Posts -->
        <div class="profile-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="section-title" style="margin-bottom:0; border:none;">Posts récents</span>
                <button id="addPostBtn"
                        class="btn btn-sm"
                        style="background:#65171e; color:#fff; border-radius:8px; display:none;"
                        onclick="window.location.href='/frontend/pages/createPost.html'">
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

  <div class="footer" id="footer"></div>
  </body>
  <script src="/frontend/assets/js/bootstrap.bundle.min.js"></script>
<script src="/frontend/assets/js/root.js"></script>
<script src="/frontend/assets/js/profil.js"></script>
<script>
    loadComponent("navbar", "/frontend/components/navbar.php", function() {
        initTheme();
        setActiveNav();
    });
    loadComponent("footer", "/frontend/components/footer.php");
</script>
</html>