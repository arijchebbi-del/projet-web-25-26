/* Skills: dynamic inputs */
function addSkill(numberSkill) {
    const lastRow = document.querySelector(`#skillRow${numberSkill - 1}`)
        ?? document.querySelector('.cp-skill-input-row');

    if (!lastRow) return;

    const wrapper = document.createElement('div');
    wrapper.classList.add('cp-skill-input-row');
    wrapper.id = `skillRow${numberSkill}`;

    const input = document.createElement('input');
    input.type = 'text';
    input.classList.add('cp-input');
    input.id = `skillInput${numberSkill}`;
    input.name = 'skills[]';
    input.placeholder = 'Type a skill...';

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.classList.add('cp-skill-add-btn');
    removeBtn.setAttribute('aria-label', 'Remove skill');
    removeBtn.innerHTML = '<i class="bi bi-dash-lg"></i>';
    removeBtn.addEventListener('click', () => {
        wrapper.remove();
    });

    wrapper.appendChild(input);
    wrapper.appendChild(removeBtn);

    lastRow.insertAdjacentElement('afterend', wrapper);
    input.focus();
}

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

/* Opportunity Type (Job / Internship) */
const typeButtons = document.querySelectorAll('.cp-type-btn');

typeButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        typeButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const radio = btn.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;
    });
});

/* Chip group helper */
function bindChipGroup(groupSelector) {
    const chips = document.querySelectorAll(`${groupSelector} .cp-chip`);

    chips.forEach(chip => {
        chip.addEventListener('click', () => {
            chips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
        });
    });
}

bindChipGroup('#jobTypeChips');
bindChipGroup('#workModeChips');

/* Country "Other" reveal */
const countrySelect = document.querySelector('#postCountry');
const otherInput = document.querySelector('#postCountryOther');

countrySelect?.addEventListener('change', () => {
    if (countrySelect.value === 'other') {
        otherInput.style.display = 'block';
        otherInput.required = true;
        otherInput.focus();
    } else {
        otherInput.style.display = 'none';
        otherInput.value = '';
        otherInput.required = false;
    }
});

/* Description character counter */
const textarea = document.querySelector('#postDescription');
const charCount = document.querySelector('#charCount');

textarea?.addEventListener('input', () => {
    const len = textarea.value.length;
    if (charCount) charCount.textContent = len;

    charCount?.closest('.cp-char-count')
        ?.classList
        .toggle('text-danger', len > 950);
});

/* Submit: show loader, disable button */
const form = document.querySelector('#createPostForm');
const submitBtn = document.querySelector('#submitPostBtn');
const submitText = document.querySelector('.cp-submit-text');
const loader = document.querySelector('#postLoader');

form?.addEventListener('submit', () => {
    const title = document.querySelector('#postTitle');
    const company = document.querySelector('#postCompany');
    const description = document.querySelector('#postDescription');
    const link = document.querySelector('#postLink');

    if (!title?.value.trim() || !company?.value.trim() ||
        !description?.value.trim() || !link?.value.trim()) {
        return;
    }

    if (submitBtn) submitBtn.disabled = true;
    submitText?.classList.add('d-none');
    loader?.classList.remove('d-none');
});

/* Success state (when PHP redirects back with ?success=1) */
(function handleSuccessRedirect() {
    const params = new URLSearchParams(window.location.search);
    if (!params.has('success')) return;

    const createForm = document.querySelector('#createPostForm');
    const successDiv = document.querySelector('#postSuccess');

    if (createForm) createForm.classList.add('d-none');
    if (successDiv) successDiv.classList.remove('d-none');

    window.history.replaceState({}, '', window.location.pathname);
})();

/* "Post Another" resets the form and hides the success panel */
function resetForm() {
    const createForm = document.querySelector('#createPostForm');
    const successDiv = document.querySelector('#postSuccess');

    if (createForm) {
        createForm.reset();
        createForm.classList.remove('d-none');

        document.querySelector('#jobTypeChips  .cp-chip')
            ?.classList.add('active');
        document.querySelector('#workModeChips .cp-chip')
            ?.classList.add('active');

        document.querySelectorAll('[id^="skillRow"]').forEach((row, i) => {
            if (i > 0) row.remove();
        });
        skillCount = 2;

        if (charCount) charCount.textContent = '0';
    }

    successDiv?.classList.add('d-none');
}

/* Display server-side errors stored in PHP session */
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