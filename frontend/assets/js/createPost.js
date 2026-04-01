
const countrySelect = document.getElementById('postCountry');
const countryOther  = document.getElementById('postCountryOther');

countrySelect.addEventListener('change', () => {
    const isOther = countrySelect.value === 'other';
    countryOther.style.display = isOther ? 'block' : 'none';
    countryOther.required = isOther;
    if (isOther) countryOther.focus();
});


const typeBtns = document.querySelectorAll('.cp-type-btn');

typeBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        typeBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    });
});


document.querySelectorAll('.cp-chip-group').forEach(group => {
    group.querySelectorAll('.cp-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            group.querySelectorAll('.cp-chip').forEach(c => c.classList.remove('active'));
            chip.classList.add('active');
        });
    });
});


const skillInput  = document.getElementById('skillInput');
const skillTags   = document.getElementById('skillTags');
const addSkillBtn = document.getElementById('addSkillBtn');
const skills      = [];

function addSkill() {
    const val = skillInput.value.trim();
    if (!val || skills.includes(val)) {
        skillInput.value = '';
        return;
    }
    skills.push(val);

    const tag = document.createElement('span');
    tag.className = 'cp-skill-tag';
    tag.innerHTML = `${val}<button type="button" onclick="removeSkill('${val}', this)"><i class="bi bi-x"></i></button>`;
    skillTags.appendChild(tag);
    skillInput.value = '';
}

function removeSkill(val, btn) {
    const idx = skills.indexOf(val);
    if (idx > -1) skills.splice(idx, 1);
    btn.parentElement.remove();
}

addSkillBtn.addEventListener('click', addSkill);
skillInput.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
        e.preventDefault();
        addSkill();
    }
});


const desc      = document.getElementById('postDescription');
const charCount = document.getElementById('charCount');

desc.addEventListener('input', () => {
    charCount.textContent = desc.value.length;
    if (desc.value.length > 1000) {
        desc.value = desc.value.slice(0, 1000);
    }
});


const form        = document.getElementById('createPostForm');
const submitBtn   = document.getElementById('submitPostBtn');
const postSuccess = document.getElementById('postSuccess');

form.addEventListener('submit', function (e) {
    e.preventDefault();
    if (!validatePost()) return;

    
    submitBtn.querySelector('.cp-submit-text').classList.add('d-none');
    submitBtn.querySelector('.cp-submit-loader').classList.remove('d-none');
    submitBtn.disabled = true;

    
    const post = {
        id:          Date.now(),
        type:        document.querySelector('input[name="postType"]:checked').value,
        title:       document.getElementById('postTitle').value.trim(),
        company:     document.getElementById('postCompany').value.trim(),
        country:     countrySelect.value === 'other' ? countryOther.value.trim() : countrySelect.value,
        city:        document.getElementById('postCity').value.trim(),
        jobType:     document.querySelector('#jobTypeChips .cp-chip.active')?.dataset.value || 'Full-time',
        workMode:    document.querySelector('#workModeChips .cp-chip.active')?.dataset.value || 'On-site',
        experience:  document.getElementById('postExperience').value,
        salary:      document.getElementById('postSalary').value.trim(),
        skills:      [...skills],
        description: document.getElementById('postDescription').value.trim(),
        link:        document.getElementById('postLink').value.trim(),
        companyWebsite: document.getElementById('postCompanyWebsite').value.trim(),
        deadline:    document.getElementById('postDeadline').value,
        contact:     document.getElementById('postContact').value.trim(),
        postedAt:    new Date().toISOString()
    };

    
    const existing = JSON.parse(localStorage.getItem('aluminiPosts') || '[]');
    existing.unshift(post);
    localStorage.setItem('aluminiPosts', JSON.stringify(existing));

    setTimeout(() => {
        form.classList.add('d-none');
        postSuccess.classList.remove('d-none');
    }, 1200);
});


function validatePost() {
    let valid = true;
    ['postTitle', 'postCompany', 'postDescription','postLink'].forEach(id => {
        const el = document.getElementById(id);
        el.classList.remove('cp-invalid');
        if (!el.value.trim()) {
            el.classList.add('cp-invalid');
            valid = false;
        }
    });
    return valid;
}

['postTitle', 'postCompany', 'postDescription'].forEach(id => {
    document.getElementById(id).addEventListener('input', function () {
        this.classList.remove('cp-invalid');
    });
});


function resetForm() {
    form.reset();
    skills.length = 0;
    skillTags.innerHTML = '';
    charCount.textContent = '0';

    form.classList.remove('d-none');
    postSuccess.classList.add('d-none');

    submitBtn.querySelector('.cp-submit-text').classList.remove('d-none');
    submitBtn.querySelector('.cp-submit-loader').classList.add('d-none');
    submitBtn.disabled = false;

    countryOther.style.display = 'none';
    countryOther.required = false;
    countryOther.value = '';
    
    typeBtns.forEach((b, i) => b.classList.toggle('active', i === 0));

    document.querySelectorAll('.cp-chip-group').forEach(group => {
        group.querySelectorAll('.cp-chip').forEach((c, i) => c.classList.toggle('active', i === 0));
    });
}