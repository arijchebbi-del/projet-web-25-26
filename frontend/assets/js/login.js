const container = document.getElementById("authcontainer");
const registerBtn = document.getElementById("registerBtn");
const loginBtn = document.getElementById("loginBtn");
const signInForm = document.getElementById("signIn");
const signUpForm = document.getElementById("signUp");
const signInSubmit = document.getElementById("signInSubmit");
const signUpSubmit = document.getElementById("signUpSubmit");

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

function getPostLoginTarget() {
    const next = params.get("next");
    if (!next) return "/frontend/pages/feed.html";

    try {
        return decodeURIComponent(next);
    } catch (error) {
        return "/frontend/pages/feed.html";
    }
}

function completeAuth(formElement) {
    if (formElement && typeof formElement.reportValidity === "function" && !formElement.reportValidity()) {
        return;
    }

    if (typeof setSessionId === "function") {
        setSessionId();
    } else {
        localStorage.setItem("sessionId", "sess_" + Date.now());
    }

    window.location.href = getPostLoginTarget();
}

if (signInSubmit) {
    signInSubmit.addEventListener("click", function () {
        completeAuth(signInForm);
    });
}

if (signUpSubmit) {
    signUpSubmit.addEventListener("click", function () {
        completeAuth(signUpForm);
    });
}