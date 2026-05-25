function configureSkills(...skills) {
    if (!skills.length) return '';
    let acc = "";
    skills.forEach(skill => {
        acc += `<span class="badge skill-badge">${skill}</span>`;
    });
    return acc;
}

// Added `id` param so the card links to the real profile
function createCard(name, photo, promo, id, ...skills) {
    const imgHtml = photo
        ? `<img class="avatar" src="${photo}" alt="${name}">`
        : `<div class="avatar-fallback">${name.charAt(0).toUpperCase()}</div>`;

    return `
        <a href="/frontend/pages/profil.php?id=${id}"
           class="result-card card border rounded-3 p-3 mb-2 w-100 d-block text-decoration-none">
            <div class="d-flex align-items-center gap-3">
                ${imgHtml}
                <div class="flex-grow-1">
                    <p class="fw-medium mb-0">${name}</p>
                    <p class="text-muted mb-1">Promo ${promo ?? '—'}</p>
                    <div class="d-flex flex-wrap gap-1">
                        ${configureSkills(...skills)}
                    </div>
                </div>
                <span class="arrow-icon ms-auto">›</span>
            </div>
        </a>
    `;
}

// id is now a required param passed from recherche.php
function addCardToPage(name, photo, promo, id, ...skills) {
    const container = document.getElementById("search_results");
    const card      = createCard(name, photo, promo, id, ...skills);
    if (container.querySelector("p.text-muted")) {
        container.innerHTML = card;   // replace placeholder
    } else {
        container.innerHTML += card;
    }
}
