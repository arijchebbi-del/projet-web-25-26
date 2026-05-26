/* ── Description character counter ──────────────────────────────────── */
const textarea  = document.querySelector('#postContent');
const charCount = document.querySelector('#charCount');

textarea?.addEventListener('input', () => {
    const len = textarea.value.length;
    if (charCount) charCount.textContent = len;

    charCount?.closest('.cp-char-count')
              ?.classList
              .toggle('text-danger', len > 5800);
});


/* ── Submit: show loader, disable button ─────────────────────────────── */

const form        = document.querySelector('#createPostForm');
const submitBtn   = document.querySelector('#submitPostBtn');
const submitText  = document.querySelector('.cp-submit-text');
const loader      = document.querySelector('#postLoader');

form?.addEventListener('submit', () => {
    if (submitBtn)  submitBtn.disabled = true;
    submitText?.classList.add('d-none');
    loader?.classList.remove('d-none');
});


/* ── Success state (when PHP redirects back with ?success=1) ─────────── */

(function handleSuccessRedirect() {
    const params  = new URLSearchParams(window.location.search);
    if (!params.has('success')) return;

    const formCard = document.querySelector('.cp-form-card');
    const createForm  = document.querySelector('#createPostForm');
    const successDiv  = document.querySelector('#postSuccess');

    if (createForm)  createForm.classList.add('d-none');
    if (successDiv)  successDiv.classList.remove('d-none');

    /* clean the URL so a refresh does not re-trigger this */
    window.history.replaceState({}, '', window.location.pathname);
})();


/* ── "Post Another" resets the form and hides the success panel ───────── */

function resetForm() {
    const createForm = document.querySelector('#createPostForm');
    const successDiv = document.querySelector('#postSuccess');

    if (createForm) {
        createForm.reset();
        createForm.classList.remove('d-none');
        if (charCount) charCount.textContent = '0';
    }

    successDiv?.classList.add('d-none');
}


/* ── Display server-side errors stored in PHP session ────────────────── */
/* 
   If you add a small PHP snippet at the top of createPost.php to echo
   the errors as a JS variable, this block will surface them:

   PHP snippet (add just before </head>):
   <?php if (!empty($_SESSION['form_errors'])): ?>
   <script>window.__formErrors = <?= json_encode($_SESSION['form_errors']) ?>;</script>
   <?php unset($_SESSION['form_errors']); endif; ?>
*/

(function displayServerErrors() {
    if (!window.__formErrors?.length) return;

    const alert = document.createElement('div');
    alert.className = 'alert alert-danger alert-dismissible fade show mt-3';
    alert.role = 'alert';
    alert.innerHTML =
        '<ul class="mb-0">' +
        window.__formErrors.map(e => `<li>${e}</li>`).join('') +
        '</ul>' +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';

    const formCard = document.querySelector('.cp-form-card');
    formCard?.insertAdjacentElement('afterbegin', alert);
})();