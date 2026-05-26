<?php
session_start();


if (!isset($_SESSION['email'])) {
    header('Location: /frontend/pages/login.php');
    exit();
}

require_once '../../backend/config/ConnexionDB.php';

$conn = ConnexionDB::getInstance();
$userStmt = $conn->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
$userStmt->execute([':email' => $_SESSION['email']]);
$currentUserId = (int) $userStmt->fetchColumn();

if (!$currentUserId) {
    header('Location: /frontend/pages/logout.php');
    exit();
}

$_SESSION['user_id'] = $currentUserId;

/*
   POST handler ; 
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');
// aexpliquer
    if ($content === '') {
        $_SESSION['form_errors'] = ['Post content is required.'];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    try {
        $stmt = $conn->prepare('
            INSERT INTO posts (user_id, content)
            VALUES (:user_id, :content)
        ');
        $stmt->execute([
            ':user_id' => $currentUserId,
            ':content' => $content,
        ]);
    } catch (PDOException $e) {
        $_SESSION['form_errors'] = ['Database error: ' . $e->getMessage()];
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
    exit();
}
/* 
   End of POST handler */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--<script src="/frontend/assets/js/auth.js"></script>--> 

    <title>Alumini | Create Post</title>
    <link rel="stylesheet" href="/frontend/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/frontend/assets/css/footer_navbar.css">
    <link rel="stylesheet" href="/frontend/assets/css/createPost.css">

    <?php if (!empty($_SESSION['form_errors'])): ?>
    <script>
        window.__formErrors = <?= json_encode($_SESSION['form_errors']) ?>;
        //explain 
    </script>
    <?php unset($_SESSION['form_errors']); endif; ?>
</head>
<body>
<div id="navbar"></div>

<div class="cp-hero">
    <div class="cp-hero-inner">
        <span class="cp-hero-tag">New Post</span>
        <h1 class="cp-hero-title">Share a <span>Post</span></h1>
        <p class="cp-hero-sub">Share an update with the INSAT community.</p>
    </div>
</div>

<div class="cp-wrapper">
    <section class="cp-form-col">
        <div class="cp-form-card">

            <form id="createPostForm" method="POST" action="" novalidate>

                <div class="cp-section">
                    <div class="cp-section-label">
                        <span class="cp-step">1</span>
                        Post Content <span class="cp-req">*</span>
                    </div>
                    <textarea class="cp-input cp-textarea" id="postContent"
                              name="content"
                              rows="6"
                              placeholder="Share an update, an achievement, or a question for the community…"
                              required
                              maxlength="6000"></textarea>
                    <div class="cp-char-count"><span id="charCount">0</span> / 6000</div>
                </div>

                <!-- Actions -->
                <div class="cp-actions">
                    <a href="/frontend/pages/feed.php" class="cp-cancel-btn">Cancel</a>
                    <button type="submit" class="cp-submit-btn" id="submitPostBtn">
                        <span class="cp-submit-text">
                            <i class="bi bi-send-fill me-2"></i>Publish
                        </span>
                        <span class="cp-submit-loader d-none" id="postLoader">
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Publishing…
                        </span>
                    </button>
                </div>

            </form>

            <!-- Success state -->
            <div class="cp-success d-none" id="postSuccess">
                <div class="cp-success-icon"> post created succesfully 😊</div>
                <h3>Published</h3>
                <p>Your post is now in the feed.</p>
                <div class="cp-success-actions">
                    <a href="/frontend/pages/feed.php" class="cp-submit-btn">← Back to Feed</a>
                    <button class="cp-cancel-btn" onclick="resetForm()">Post Another</button>
                </div>
            </div>

        </div>
    </section>
</div>

<div id="footer"></div>

<script src="/frontend/assets/js/bootstrap.bundle.min.js"></script>
<script src="/frontend/assets/js/root.js"></script>
<script src="/frontend/assets/js/textPost.js"></script>
<script>
    loadComponent("navbar", "/frontend/components/navbar.php", function () {
        initTheme();
        setActiveNav();
    });
    loadComponent("footer", "/frontend/components/footer.php");
</script>

</body>
</html>
