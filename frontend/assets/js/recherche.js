function configureSkills(...skills) {
    if (!skills.length) return '';
    let acc = "";
    skills.forEach(skill => {
        acc += `<span class="badge skill-badge">${skill}</span>`;
    });
    return acc;
}

function avatarMarkup(photo, name) {
    return photo
        ? `<img class="avatar" src="${photo}" alt="${name}">`
        : `<div class="avatar-fallback">${name.charAt(0).toUpperCase()}</div>`;
}

function createUserCard(result) {
    const name = result.title || `${result.nom} ${result.prenom}`.trim();
    const promo = result.promo_year ?? '—';
    const skills = result.skills ? result.skills.split(',') : [];
    const id = result.result_id ?? result.id;

    return `
        <a href="/frontend/pages/profil.php?id=${id}"
           class="result-card card border rounded-3 p-3 mb-2 w-100 d-block text-decoration-none">
            <div class="d-flex align-items-center gap-3">
                ${avatarMarkup(result.avatar_url, name)}
                <div class="flex-grow-1">
                    <p class="fw-medium mb-0">${name}</p>
                    <p class="text-muted mb-1">Promo ${promo}</p>
                    <div class="d-flex flex-wrap gap-1">
                        ${configureSkills(...skills)}
                    </div>
                </div>
                <span class="badge bg-light text-dark">User</span>
                <span class="arrow-icon ms-auto">›</span>
            </div>
        </a>
    `;
}

function createJobCard(result) {
    const title = result.title || 'Job';
    const subtitle = result.subtitle || 'Company';

    return `
        <a href="/frontend/pages/post.php?id=${result.result_id}"
           class="result-card card border rounded-3 p-3 mb-2 w-100 d-block text-decoration-none">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-fallback">J</div>
                <div class="flex-grow-1">
                    <p class="fw-medium mb-0">${title}</p>
                    <p class="text-muted mb-1">${subtitle}</p>
                </div>
                <span class="badge bg-primary">Job</span>
                <span class="arrow-icon ms-auto">›</span>
            </div>
        </a>
    `;
}

function createPostCard(result) {
    const title = result.title || 'Post';
    const subtitle = result.subtitle || '';

    return `
        <a href="/frontend/pages/feed.php"
           class="result-card card border rounded-3 p-3 mb-2 w-100 d-block text-decoration-none">
            <div class="d-flex align-items-center gap-3">
                ${avatarMarkup(result.avatar_url, title)}
                <div class="flex-grow-1">
                    <p class="fw-medium mb-0">${title}</p>
                    <p class="text-muted mb-1">${subtitle}</p>
                </div>
                <span class="badge bg-secondary">Post</span>
                <span class="arrow-icon ms-auto">›</span>
            </div>
        </a>
    `;
}

function createResultCard(result) {
    const type = result.result_type || 'user';
    if (type === 'job') return createJobCard(result);
    if (type === 'post') return createPostCard(result);
    return createUserCard(result);
}

function addCardToPage(result) {
    const container = document.getElementById("search_results");
    const card = createResultCard(result);
    if (container.querySelector("p.text-muted")) {
        container.innerHTML = card;
    } else {
        container.innerHTML += card;
    }
}
