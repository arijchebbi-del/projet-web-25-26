const container = document.getElementById("authcontainer");
const registerBtn = document.getElementById("registerBtn");
const loginBtn = document.getElementById("loginBtn");

function showSignUp() {
    if (container) container.classList.add("active");
}

function showSignIn() {
    if (container) container.classList.remove("active");
}

if (registerBtn) {
    registerBtn.addEventListener("click", showSignUp);
}

if (loginBtn) {
    loginBtn.addEventListener("click", showSignIn);
}

const params = new URLSearchParams(window.location.search);
const mode = (params.get("mode") || "").toLowerCase();

if (mode === "signup") {
    showSignUp();
}

