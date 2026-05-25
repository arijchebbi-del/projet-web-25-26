/* ═══════════════════════════════════════════════════════════════════════
   createPost.js
   ═══════════════════════════════════════════════════════════════════════ */

/* ── Skills ─────────────────────────────────────────────────────────────
   Each input gets name="skills[]" so PHP receives $_POST['skills'] as an
   array.  The first input (skillInput1) already has this name in the HTML.
   New rows are inserted after the previous row's wrapper div.
   A "–" button on each new row lets the user remove it.
   ─────────────────────────────────────────────────────────────────────── */

function addSkill(numberSkill) {
    const lastRow = document.querySelector(`#skillRow${numberSkill - 1}`)
                 ?? document.querySelector('.cp-skill-input-row');   // first row

    if (!lastRow) return;

    /* wrapper */
    const wrapper = document.createElement('div');
    wrapper.classList.add('cp-skill-input-row');
    wrapper.id = `skillRow${numberSkill}`;

    /* text input – name="skills[]" is mandatory for PHP $_POST collection */
    const input = document.createElement('input');
    input.type        = 'text';
    input.classList.add('cp-input');
    input.id          = `skillInput${numberSkill}`;
    input.name        = 'skills[]';
    input.placeholder = 'Type a skill…';

    /* remove button */
    const removeBtn = document.createElement('button');
    removeBtn.type      = 'button';
    removeBtn.classList.add('cp-skill-add-btn');
    removeBtn.setAttribute('aria-label', 'Remove skill');
    removeBtn.innerHTML = '<i class="bi bi-dash-lg"></i>';
    removeBtn.addEventListener('click', () => {
        wrapper.remove();
        /* keep the counter consistent so IDs stay unique */
    });

    wrapper.appendChild(input);
    wrapper.appendChild(removeBtn);

    lastRow.insertAdjacentElement('afterend', wrapper);

    /* give focus to the new field */
    input.focus();
}

/* make the first row identifiable for insertAdjacentElement targeting */
(function labelFirstSkillRow() {
    const firstRow = document.querySelector('.cp-skill-input-row');
    if (firstRow && !firstRow.id) firstRow.id = 'skillRow1';
})();

const addSkillBtn = document.querySelector('#addSkillBtn');
let skillCount = 2;

addSkillBtn?.addEventListener('click', () => {
    addSkill(skillCount);
    skillCount++;
});


/* ── Opportunity Type (Job / Internship) ─────────────────────────────── */

const typeButtons = document.querySelectorAll('.cp-type-btn');

typeButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        typeButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        /* The radio inside is hidden; check it programmatically */
        const radio = btn.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    });
});


/* ── Chip group helper ───────────────────────────────────────────────────
   Both "Job Type" and "Work Mode" chip groups use the same toggle pattern.
   Chips are <label> elements wrapping hidden <input type="radio">, so the
   browser already checks the radio on click; we only need to manage the
   visual `active` class.
   ─────────────────────────────────────────────────────────────────────── */

function bindChipGroup(groupSelector) {
    const chips = document.querySelectorAll(`${groupSelector} .cp-chip`);

    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
            /* radio is toggled automatically because chip is a <label> */
        });
    });
}

bindChipGroup('#jobTypeChips');
bindChipGroup('#workModeChips');


/* ── Country "Other" reveal ──────────────────────────────────────────── */

const countrySelect = document.querySelector('#postCountry');
const otherInput    = document.querySelector('#postCountryOther');

countrySelect?.addEventListener('change', () => {
    if (countrySelect.value === 'other') {
        otherInput.style.display = 'block';
        otherInput.required      = true;
        otherInput.focus();
    } else {
        otherInput.style.display = 'none';
        otherInput.value         = '';
        otherInput.required      = false;
    }
});


/* ── Description character counter ──────────────────────────────────── */

const textarea  = document.querySelector('#postDescription');
const charCount = document.querySelector('#charCount');

textarea?.addEventListener('input', () => {
    const len = textarea.value.length;
    if (charCount) charCount.textContent = len;

    /* visual warning when approaching the displayed 1 000-char soft limit */
    charCount?.closest('.cp-char-count')
              ?.classList
              .toggle('text-danger', len > 950);
});


/* ── Submit: show loader, disable button ─────────────────────────────── */

const form        = document.querySelector('#createPostForm');
const submitBtn   = document.querySelector('#submitPostBtn');
const submitText  = document.querySelector('.cp-submit-text');
const loader      = document.querySelector('#postLoader');

form?.addEventListener('submit', (e) => {
    /* Basic client-side guard: check required fields before the loader */
    const title       = document.querySelector('#postTitle');
    const company     = document.querySelector('#postCompany');
    const description = document.querySelector('#postDescription');
    const link        = document.querySelector('#postLink');

    if (!title?.value.trim() || !company?.value.trim() ||
        !description?.value.trim() || !link?.value.trim()) {
        /* Let the browser's built-in constraint UI handle it */
        return;
    }

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

        /* Reset chip active states to their defaults */
        document.querySelector('#jobTypeChips  .cp-chip')
               ?.classList.add('active');
        document.querySelector('#workModeChips .cp-chip')
               ?.classList.add('active');

        /* Remove all dynamically added skill rows */
        document.querySelectorAll('[id^="skillRow"]').forEach((row, i) => {
            if (i > 0) row.remove();
        });
        skillCount = 2;

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