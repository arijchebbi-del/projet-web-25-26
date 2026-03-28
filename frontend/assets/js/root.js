// ===== Load Component =====
function loadComponent(id, file, callback) {
    fetch(file)
        .then(res => res.text())
        .then(data => {
            document.getElementById(id).innerHTML = data;
            if (callback) callback();
        })
        .catch(err => console.error("Error loading:", file, err));
}

// ===== Theme =====
function initTheme() {
    const btn = document.getElementById("themeBtn");
    const saved = localStorage.getItem("theme");
    if (saved === "dark") {
        document.documentElement.setAttribute("data-theme", "dark");
        if (btn) btn.innerHTML = '<i class="fa-solid fa-moon"></i>';
    }
    if (btn) {
        btn.onclick = toggleTheme;
    }
}

function toggleTheme() {
    const root = document.documentElement;
    const btn = document.getElementById("themeBtn");
    if (root.getAttribute("data-theme") === "dark") {
        root.removeAttribute("data-theme");
        localStorage.setItem("theme", "light");
        if (btn) btn.innerHTML = '<i class="fa-regular fa-moon"></i>';
    } else {
        root.setAttribute("data-theme", "dark");
        localStorage.setItem("theme", "dark");
        if (btn) btn.innerHTML = '<i class="fa-solid fa-moon"></i>';
    }
}

// ===== Active Nav =====
function setActiveNav() {
    const currentFile = window.location.pathname.split('/').pop().toLowerCase();
    document.querySelectorAll('.navbar .nav-link').forEach(link => {
        link.classList.remove('active');
        const href = link.getAttribute('href');
        if (!href || href === '#') return;
        const linkFile = href.split('/').pop().toLowerCase();
        if (linkFile === currentFile) {
            link.classList.add('active');
        }
    });
}