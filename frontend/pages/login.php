<?php
session_start();

require_once '../../backend/config/ConnexionDB.php';

if (isset($_SESSION['email'])) {
    header("Location: /frontend/pages/feed.php");
    exit();
}

$authError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['auth_action'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $conn = ConnexionDB::getInstance();

        if ($action === 'signin') {
            $stmt = $conn->prepare("SELECT email, password_hash FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['email'] = $user['email'];
                header("Location: /frontend/pages/feed.php");
                exit();
            }

            $authError = 'Invalid email or password.';
        }

        if ($action === 'signup') {
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');

            if ($firstName === '' || $lastName === '' || $email === '' || $password === '') {
                $authError = 'Please fill all required fields.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $authError = 'Please enter a valid email address.';
            } elseif (strlen($password) < 6) {
                $authError = 'Password must contain at least 6 characters.';
            } else {
                $exists = $conn->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
                $exists->execute([':email' => $email]);

                if ($exists->fetch()) {
                    $authError = 'An account already exists for this email.';
                } else {
                    $conn->beginTransaction();

                    $insatien = $conn->prepare(
                        "INSERT INTO insatien (nom, prenom, email) VALUES (:nom, :prenom, :email)"
                    );
                    $insatien->execute([
                        ':nom' => $lastName,
                        ':prenom' => $firstName,
                        ':email' => $email,
                    ]);

                    $insatienId = $conn->lastInsertId();
                    $user = $conn->prepare(
                        "INSERT INTO users (email, password_hash, insatien_id) VALUES (:email, :password_hash, :insatien_id)"
                    );
                    $user->execute([
                        ':email' => $email,
                        ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
                        ':insatien_id' => $insatienId,
                    ]);

                    $conn->commit();
                    $_SESSION['email'] = $email;
                    header("Location: /frontend/pages/feed.php");
                    exit();
                }
            }
        }
    } catch (PDOException $e) {
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }
        $authError = 'Authentication failed. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <script src="/frontend/assets/js/bootstrap.bundle.min.js"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="/frontend/assets/css/login.css">
</head>

<body>
    <!--container principal-->
    <div class="container" id ="authcontainer">
        <?php if ($authError !== ''): ?>
            <div class="alert alert-danger auth-alert">
                <?= htmlspecialchars($authError) ?>
            </div>
        <?php endif; ?>
        <div class="form-box login">
        <!--formulaire sign in-->
        <form class="signinform" id="signIn" method="POST" action="/frontend/pages/login.php">
            <input type="hidden" name="auth_action" value="signin">
            <h1 class="formtitle"> Reconnect with your Alumini story </h1>
            <p class="form-subtitle">
                Access your circle. Build your future.</p>
            <!--email-->
            <div class="input-field"> 
                        <input type="email" class="form-control" id="signInEmail" name="email" placeholder="Email" required>
            <i class="bi bi-envelope-fill"></i> 
            </div>
            <!--password-->
            <div class="input-field">
             <input type="password"
              class="form-control"
                            id="signInPassword" 
                            name="password"
              placeholder="Password" required>
              <i class="bi bi-lock"></i>
            </div>
            <div class="forgetpass-link">
                <a href="#">Password Forgotten?</a>
            </div>
            <!--submit button-->
             <button type="submit" class="submitbtn" id="signInSubmit">Sign in</button>
             <!--social media-->
             <p>Enter with social Platforms </p>
            <div class="socialmedia-icons">
            <a href="#" class="social-icon">
             <i class="bi bi-google"></i>
            </a>
            <a href="#" class="social-icon">
             <i class="bi bi-linkedin"></i>
            </a>
            <a href="#" class="social-icon">
             <i class="bi bi-github"></i>
            </a> </div>

            

        </form>
        </div>
        <div class="form-box register">
        <!--formulaire sign up-->
        <form class="signupform" id="signUp" method="POST" action="/frontend/pages/login.php">
            <input type="hidden" name="auth_action" value="signup">
            <h1 class="formtitle">Start your Alumini journey</h1>
            <p class="form-subtitle">
                A few details, a lifetime of connections</p> 
           <div class="name-row">
           <!-- First name -->
           <div class="input-field">
                    <input type="text" class="form-control" id="signUpFirstName" name="first_name" placeholder="First name" required>
               <i class="bi bi-person-fill"></i>
            </div>

            <!-- Last name -->
            <div class="input-field">
                    <input type="text" class="form-control" id="signUpLastName" name="last_name" placeholder="Last name" required>
               <i class="bi bi-person-fill"></i>
            </div>
            </div>

            
            <!--email-->
            <div class="input-field"> 
                        <input type="email" class="form-control" id="signUpEmail" name="email" placeholder="Email" required>
            <i class="bi bi-envelope-fill"></i> 
            </div>
            <!--password-->
            <div class="input-field">
             <input type="password"
              class="form-control"
                            id="signUpPassword" 
                            name="password"
              placeholder="Password" required>
              <i class="bi bi-lock"></i>
            </div>
            <!--submit button-->
             <button type="submit" class="submitbtn" id="signUpSubmit">Sign up</button>
             <!--social media-->
             <p>Sign up with social Platforms </p>
            <div class="socialmedia-icons">
            <a href="#" class="social-icon">
             <i class="bi bi-google"></i>
            </a>
            <a href="#" class="social-icon">
             <i class="bi bi-linkedin"></i>
            </a>
            <a href="#" class="social-icon">
             <i class="bi bi-github"></i>
            </a> </div>

            

        </form>
        </div>
        <!-- toggle panel -->
        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Already part of the story?</h1>
                <p>Sign in and reconnect with your community</p>
                <button class="toggle-btn" id="loginBtn">Sign In</button>
            </div>
            <div class="toggle-panel toggle-right">
                <h1>New here? Let’s build something meaningful</h1>
                <p>Join alumini, mentors, and opportunities that move you forward</p>
                <button class="toggle-btn" id="registerBtn">Sign Up</button>
            </div>
        </div>
    </div>
        
<script src="/frontend/assets/js/login.js"></script>
</body>

</html>
