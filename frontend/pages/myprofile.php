<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/frontend/assets/js/auth.js"></script>
    <script>requireAuth();</script>
    <link rel="stylesheet" href="/frontend/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/frontend/assets/css/profil.css">
    <link rel="stylesheet" href="/frontend/assets/css/myprofile.css">
    <link rel="stylesheet" href="/frontend/assets/css/footer_navbar.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <title>My Profile</title>
</head>
<body>

    <div id="navbar"></div>
    <div id="profil"></div>

    <!-- ══════════════ Edit Profile Modal ══════════════ -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:680px;">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">✏️ Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <!-- Avatar + Name -->
                    <div class="modal-avatar-row">
                        <div class="modal-avatar">
                            <img src="/frontend/assets/images/icon-7797704_1280.png" class="profile-pic" id="modalAvatarImg">
                            <div class="avatar-edit-btn" title="Change photo" onclick="document.getElementById('avatarUpload').click()">
                                <input type="file" id="avatarUpload" style="display:none" accept="image/*" onchange="uploadAvatarImage(event)">
                                <i class="bi bi-camera-fill"></i>
                            </div>
                        </div>
                        <div class="modal-avatar-info">
                            <div class="name" id="modalDisplayName">Loading...</div>
                            <div class="role" id="modalDisplayTagline">Add a tagline</div>
                        </div>
                    </div>

                    <!-- Identity -->
                    <div class="modal-section">
                        <div class="modal-section-title"><i class="bi bi-person"></i> Identity</div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">First Name</label>
                                <input type="text" id="firstName" class="form-control" placeholder="First name">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" id="lastName" class="form-control" placeholder="Last name">
                            </div>
                            <div class="col-6">
                                <label class="form-label">INSAT ID</label>
                                <input type="text" id="insatienId" class="form-control readonly-field" readonly>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Promo Year</label>
                                <input type="text" id="promoYear" class="form-control" placeholder="e.g. 2028">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Tagline</label>
                                <input type="text" id="editTagline" class="form-control" placeholder="e.g. Software Engineer at INSAT">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Bio</label>
                                <textarea id="editBio" class="form-control" rows="3" placeholder="Tell us about yourself..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Links -->
                    <div class="modal-section">
                        <div class="modal-section-title"><i class="bi bi-link-45deg"></i> Links</div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label"><i class="bi bi-github"></i> GitHub</label>
                                <input type="text" id="githubLink" class="form-control" placeholder="https://github.com/username">
                            </div>
                            <div class="col-12">
                                <label class="form-label"><i class="bi bi-linkedin"></i> LinkedIn</label>
                                <input type="text" id="linkedinLink" class="form-control" placeholder="https://linkedin.com/in/username">
                            </div>
                            <div class="col-12">
                                <label class="form-label"><i class="bi bi-globe"></i> Personal Link</label>
                                <input type="text" id="editProfileLink" class="form-control" placeholder="Portfolio, website...">
                            </div>
                        </div>
                    </div>

                    <!-- Skills -->
                    <div class="modal-section">
                        <div class="modal-section-title"><i class="bi bi-bag-check"></i> Skills</div>
                        <div class="skills-chips" id="skills-chips"></div>
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
                        <div id="experience-list"></div>
                        <button type="button" class="btn-add" onclick="addExperience()">+ Add Experience</button>
                    </div>

                    <!-- Projects -->
                    <div class="modal-section">
                        <div class="modal-section-title"><i class="bi bi-folder"></i> Projects</div>
                        <div id="projects-list"></div>
                        <button type="button" class="btn-add" onclick="addProject()">+ Add Project</button>
                    </div>

                    <!-- Achievements -->
                    <div class="modal-section">
                        <div class="modal-section-title"><i class="bi bi-award"></i> Achievements</div>
                        <div id="achievements-list"></div>
                        <button type="button" class="btn-add" onclick="addAchievement()">+ Add Achievement</button>
                    </div>

                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-cancel" id="cancelBtn">Cancel</button>
                    <button type="button" class="btn btn-save" id="saveBtn" onclick="saveProfile()">Save Changes</button>
                </div>

            </div>
        </div>
    </div>

    <div class="footer" id="footer"></div>

    <script src="/frontend/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/frontend/assets/js/root.js"></script>
    <script src="/frontend/assets/js/myprofile.js"></script>
    <script>
        loadComponent("navbar", "/frontend/components/navbar.php", function() {
            initTheme();
            setActiveNav();
        });
        loadComponent("footer", "/frontend/components/footer.php");
        loadProfileSection("/frontend/pages/profil.php");
    </script>

</body>
</html>