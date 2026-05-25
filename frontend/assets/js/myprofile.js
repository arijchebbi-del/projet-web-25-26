/* ══════════════════════════════════════════
   SKILLS CHIPS
══════════════════════════════════════════ */
function addSkillChipValue(name) {
    if (!name || !name.trim()) return;
    const chips = document.getElementById('skills-chips');
    if (!chips) return;

    // Évite les doublons
    const existing = [...chips.querySelectorAll('.skill-chip')]
        .map(c => c.dataset.skill.toLowerCase());
    if (existing.includes(name.trim().toLowerCase())) return;

    const chip = document.createElement('span');
    chip.className = 'skill-chip';
    chip.dataset.skill = name.trim();
    chip.innerHTML = `${name.trim()} 
        <button type="button" onclick="this.parentElement.remove()" aria-label="Remove skill">
            <i class="bi bi-x"></i>
        </button>`;
    chips.appendChild(chip);
}

function addSkillChip() {
    const input = document.getElementById('skillInput');
    if (!input) return;
    const val = input.value.trim();
    if (!val) return;
    addSkillChipValue(val);
    input.value = '';
    input.focus();
}

/* ══════════════════════════════════════════
   EXPERIENCE ROWS (nouvelles lignes vides)
══════════════════════════════════════════ */
function addExperience() {
    const list = document.getElementById('experience-list');
    if (!list) return;
    const div = document.createElement('div');
    div.className = 'item-row';
    div.innerHTML = `
        <div class="item-row-fields">
            <div class="row-inline">
                <input type="text" class="form-control exp-company" placeholder="Company">
                <select class="type-select exp-type">
                    <option value="job">Job</option>
                    <option value="internship">Internship</option>
                    <option value="freelance">Freelance</option>
                    <option value="certification">Certification</option>
                </select>
            </div>
            <div class="row-inline">
                <input type="date" class="form-control exp-start">
                <input type="date" class="form-control exp-end">
            </div>
            <input type="text" class="form-control exp-desc" placeholder="Description">
            <input type="text" class="form-control exp-link" placeholder="Link (optional)">
        </div>
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">
            <i class="bi bi-trash3"></i>
        </button>`;
    list.appendChild(div);
}

/* ══════════════════════════════════════════
   PROJECT ROWS
══════════════════════════════════════════ */
function addProject() {
    const list = document.getElementById('projects-list');
    if (!list) return;
    const div = document.createElement('div');
    div.className = 'item-row';
    div.innerHTML = `
        <div class="item-row-fields">
            <input type="text" class="form-control proj-title" placeholder="Project title">
            <input type="text" class="form-control proj-desc"  placeholder="Description">
            <div class="row-inline">
                <input type="date" class="form-control proj-start">
                <input type="date" class="form-control proj-end">
            </div>
            <input type="text" class="form-control proj-link" placeholder="Project link (optional)">
        </div>
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">
            <i class="bi bi-trash3"></i>
        </button>`;
    list.appendChild(div);
}

/* ══════════════════════════════════════════
   ACHIEVEMENT ROWS
══════════════════════════════════════════ */
function addAchievement() {
    const list = document.getElementById('achievements-list');
    if (!list) return;
    const div = document.createElement('div');
    div.className = 'item-row';
    div.innerHTML = `
        <div class="item-row-fields">
            <input type="text" class="form-control ach-title" placeholder="Title (e.g. Hackathon Winner)">
            <div class="row-inline">
                <input type="text" class="form-control ach-issuer" placeholder="Issuer / Organization">
                <input type="date" class="form-control ach-date">
            </div>
            <select class="type-select ach-type">
                <option value="award">Award</option>
                <option value="honour">Honour</option>
                <option value="publication">Publication</option>
                <option value="competition">Competition</option>
                <option value="other">Other</option>
            </select>
            <input type="text" class="form-control ach-desc" placeholder="Description">
            <input type="text" class="form-control ach-link" placeholder="Link (optional)">
        </div>
        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">
            <i class="bi bi-trash3"></i>
        </button>`;
    list.appendChild(div);
}

/* ══════════════════════════════════════════
   AVATAR UPLOAD
══════════════════════════════════════════ */
async function uploadAvatarImage(event) {
    const file = event.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('avatar', file);

    try {
        const res = await fetch('/api/upload_avatar.php', {
            method: 'POST',
            credentials: 'include',
            body: formData
            // pas de Content-Type header : le navigateur le gère automatiquement pour FormData
        });
        const data = await res.json();

        if (data.ok && data.avatarUrl) {
            // Mettre à jour l'aperçu dans le modal et sur la page
            const modalImg = document.getElementById('modalAvatarImg');
            if (modalImg) modalImg.src = data.avatarUrl;

            const pageImg = document.getElementById('profilePicDisplay');
            if (pageImg) pageImg.src = data.avatarUrl;
        } else {
            alert('Upload échoué : ' + (data.message || 'Erreur inconnue'));
        }
    } catch (e) {
        console.error(e);
        alert('Erreur réseau lors de l\'upload.');
    }
}

/* ══════════════════════════════════════════
   SAVE PROFILE
══════════════════════════════════════════ */
async function saveProfile() {
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    // ── Collecte des skills ───────────────────────────────────────────────────
    const skills = [];
    document.querySelectorAll('#skills-chips .skill-chip').forEach(chip => {
        if (chip.dataset.skill) skills.push(chip.dataset.skill);
    });

    // ── Collecte des experiences ──────────────────────────────────────────────
    const experiences = [];
    document.querySelectorAll('#experience-list .item-row').forEach(row => {
        experiences.push({
            entreprise:      row.querySelector('.exp-company')?.value.trim() || '',
            experience_type: row.querySelector('.exp-type')?.value           || 'job',
            date_debut:      row.querySelector('.exp-start')?.value          || null,
            date_fin:        row.querySelector('.exp-end')?.value            || null,
            description:     row.querySelector('.exp-desc')?.value.trim()   || '',
            lien:            row.querySelector('.exp-link')?.value.trim()    || ''
        });
    });

    // ── Collecte des projects ─────────────────────────────────────────────────
    const projects = [];
    document.querySelectorAll('#projects-list .item-row').forEach(row => {
        projects.push({
            title:       row.querySelector('.proj-title')?.value.trim() || '',
            description: row.querySelector('.proj-desc')?.value.trim()  || '',
            date_debut:  row.querySelector('.proj-start')?.value        || null,
            date_fin:    row.querySelector('.proj-end')?.value          || null,
            lien:        row.querySelector('.proj-link')?.value.trim()  || ''
        });
    });

    // ── Collecte des achievements ─────────────────────────────────────────────
    const achievements = [];
    document.querySelectorAll('#achievements-list .item-row').forEach(row => {
        achievements.push({
            title:            row.querySelector('.ach-title')?.value.trim()  || '',
            issuer:           row.querySelector('.ach-issuer')?.value.trim() || '',
            date_obtained:    row.querySelector('.ach-date')?.value          || null,
            achievement_type: row.querySelector('.ach-type')?.value          || 'other',
            description:      row.querySelector('.ach-desc')?.value.trim()   || '',
            lien:             row.querySelector('.ach-link')?.value.trim()   || ''
        });
    });

    // ── Payload final ─────────────────────────────────────────────────────────
    const payload = {
        firstName:    document.getElementById('firstName')?.value.trim()       || '',
        lastName:     document.getElementById('lastName')?.value.trim()        || '',
        bio:          document.getElementById('editBio')?.value.trim()         || '',
        promoYear:    document.getElementById('promoYear')?.value.trim()       || '',
        tagline:      document.getElementById('editTagline')?.value.trim()     || '',
        githubLink:   document.getElementById('githubLink')?.value.trim()      || '',
        linkedinLink: document.getElementById('linkedinLink')?.value.trim()    || '',
        profileLink:  document.getElementById('editProfileLink')?.value.trim() || '',
        skills,
        experiences,
        projects,
        achievements
    };

    try {
        const res = await fetch('/api/save_profile.php', {
            method: 'POST',
            credentials: 'include',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.ok) {
            // Fermer le modal et recharger pour afficher les nouvelles données
            const modalEl = document.getElementById('editProfileModal');
            bootstrap.Modal.getInstance(modalEl)?.hide();
            window.location.reload();
        } else {
            alert('Erreur : ' + (data.message || 'Erreur inconnue'));
        }
    } catch (e) {
        console.error(e);
        alert('Erreur réseau.');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Save Changes';
    }
}