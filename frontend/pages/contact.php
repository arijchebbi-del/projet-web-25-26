<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: /frontend/pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    require_once '../../backend/config/ConnexionDB.php';
    require_once '../../backend/repository/contactRepository.php';
    require_once '../../backend/service/contactService.php';
    try {
        $service = new ContactService();
        $ok = $service->saveContactMessage($_POST);
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Message saved.' : 'Please fill all required fields.'
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumini | Contact</title>
    <link rel="stylesheet" href="/frontend/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/frontend/assets/css/footer_navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/frontend/assets/css/contact.css">
</head>
<body>

<div id="navbar"></div>

<!-- Hero -->
<div class="contact-hero">
    <h1 class="contact-hero-title">Get in <span>Touch</span></h1>
    <p class="contact-hero-sub">A question, an idea, or just want to reconnect with your college roots? We'd love to hear from you.</p>
</div>

<!-- Main content -->
<div class="contact-wrapper">

    <!-- Left: info cards -->
    <aside class="contact-info-col">

        <div class="contact-info-card">
            <div class="contact-info-icon">
                <i class="bi bi-envelope-fill"></i>
            </div>
            <div>
                <p class="contact-info-label">Email Us</p>
                <a href="mailto:alumiiicontact@gmail.com" class="contact-info-value">alumiiicontact@gmail.com</a>
                <p class="contact-info-hint">We reply within 24 hrs</p>
            </div>
        </div>

        <div class="contact-info-card">
            <div class="contact-info-icon">
                <i class="bi bi-telephone-fill"></i>
            </div>
            <div>
                <p class="contact-info-label">Call Us</p>
                <a href="tel:+21612345678" class="contact-info-value">+216 99 788 043</a>
                <p class="contact-info-hint">Office hours only</p>
            </div>
        </div>

        <div class="contact-info-card">
            <div class="contact-info-icon">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div>
                <p class="contact-info-label">Find Us</p>
                <span class="contact-info-value">INSAT, Centre Urbain Nord<br>Tunis, Tunisia</span>
                <p class="contact-info-hint">Mon–Fri, 9am–5pm</p>
            </div>
        </div>

        <hr class="contact-divider">

        <p class="contact-social-heading">Follow the Community</p>
        <div class="contact-socials">
            <a href="#" class="contact-social-pill"><i class="bi bi-linkedin me-1"></i>LinkedIn</a>
            <a href="#" class="contact-social-pill"><i class="bi bi-instagram me-1"></i>Instagram</a>
            <a href="#" class="contact-social-pill"><i class="bi bi-twitter-x me-1"></i>Twitter / X</a>
            <a href="#" class="contact-social-pill"><i class="bi bi-facebook me-1"></i>Facebook</a>
        </div>

    </aside>

    <!-- Right: form -->
    <section class="contact-form-col">
        <div class="contact-form-card">

            <div class="contact-form-header">
                <h2>Send us a message</h2>
                <p>Fill in the form below and we will get back to you shortly.</p>
            </div>

            <form id="contactForm" novalidate>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="firstName" class="form-label">First Name <span class="contact-req">*</span></label>
                        <input type="text" class="form-control contact-input" id="firstName" name="first_name" placeholder="Jane" required>
                    </div>
                    <div class="col-md-6">
                        <label for="lastName" class="form-label">Last Name <span class="contact-req">*</span></label>
                        <input type="text" class="form-control contact-input" id="lastName" name="last_name" placeholder="Doe" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address <span class="contact-req">*</span></label>
                    <input type="email" class="form-control contact-input" id="email" name="email" placeholder="jane.doe@insat.tn" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="gradYear" class="form-label">Graduation Year</label>
                        <select class="form-select contact-input" id="gradYear" name="gradYear">
                            <option value="" disabled selected>Select year</option>
                            <option>2031<option>2030<option>2029<option>2028<option>2027<option>2026<option>2025</option><option>2024</option><option>2023</option>
                            <option>2022</option><option>2021</option><option>2020</option>
                            <option>2019</option><option>2018</option><option>2017</option>
                            <option>Before 2017</option>
                            <option>Current Student</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="topic" class="form-label">Topic</label>
                        <select class="form-select contact-input" id="topic" name="topic">
                            <option value="" disabled selected>What's this about?</option>
                            <option>General Inquiry</option>
                            <option>Report a Bug</option>
                            <option>Partnership / Sponsorship</option>
                            <option>Suggestion</option>
                            <option>Profile / Account Help</option>
                            <option>Other</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Message <span class="contact-req">*</span></label>
                    <textarea class="form-control contact-input" id="message" name="message" rows="5" placeholder="Tell us what's on your mind…" required></textarea>
                </div>

                <div class="form-check mb-4 contact-checkbox-row">
                    <input class="form-check-input contact-check" type="checkbox" id="newsletter" name="newsletter">
                    <label class="form-check-label" for="newsletter">
                        Keep me updated on alumni news &amp; events
                    </label>
                </div>

                <button type="submit" class="btn contact-submit-btn w-100" id="submitBtn">
                    <span class="btn-text">Send Message</span>
                    <span class="btn-loader d-none" id="btnLoader">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Sending...
                    </span>
                </button>

            </form>

            <div class="alert alert-danger mt-3 d-none" id="contactError"></div>

            <!-- Success state -->
            <div class="contact-success d-none" id="successState">
                <div class="contact-success-icon">🎉</div>
                <h3>Message Received!</h3>
                <p>Thanks for reaching out. We'll get back to you within 24 hours. In the meantime, explore the alumni network!</p>
                <a href="/frontend/pages/feed.php" class="btn contact-submit-btn mt-2">← Back to Feed</a>
            </div>

        </div>
    </section>

</div>

<div id="footer"></div>

<script src="/frontend/assets/js/bootstrap.bundle.min.js"></script>
<script src="/frontend/assets/js/root.js"></script>
<script>
    const form = document.getElementById('contactForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = document.getElementById('btnLoader');
    const successState = document.getElementById('successState');
    const contactError = document.getElementById('contactError');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!validateForm()) return;

        contactError.classList.add('d-none');
        contactError.textContent = '';

        // Show loader
        btnText.classList.add('d-none');
        btnLoader.classList.remove('d-none');
        submitBtn.disabled = true;

        fetch('/frontend/pages/contact.php', {
            method: 'POST',
            body: new FormData(form)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                form.classList.add('d-none');
                successState.classList.remove('d-none');
                return;
            }

            contactError.textContent = data.message || 'Failed to send message.';
            contactError.classList.remove('d-none');
        })
        .catch(() => {
            contactError.textContent = 'Server error. Please try again.';
            contactError.classList.remove('d-none');
        })
        .finally(() => {
            btnText.classList.remove('d-none');
            btnLoader.classList.add('d-none');
            submitBtn.disabled = false;
        });
    });

    function validateForm() {
        let valid = true;
        form.querySelectorAll('[required]').forEach(el => {
            el.classList.remove('is-invalid');
            if (!el.value.trim()) {
                el.classList.add('is-invalid');
                valid = false;
            }
        });
        return valid;
    }

    // Remove invalid state on input
    form.querySelectorAll('[required]').forEach(el => {
        el.addEventListener('input', () => el.classList.remove('is-invalid'));
    });
</script>
<script>
        loadComponent("navbar", "/frontend/components/navbar.php", function() {
                initTheme();
                setActiveNav();
        });
        loadComponent("footer", "/frontend/components/footer.php");
</script>

</body>

</html>
