const container = document.getElementById("authcontainer");
const registerBtn = document.getElementById("registerBtn");
const loginBtn = document.getElementById("loginBtn");
const signInForm = document.getElementById("signIn");
const signUpForm = document.getElementById("signUp");
const signInSubmit = document.getElementById("signInSubmit");
const signUpSubmit = document.getElementById("signUpSubmit");
const signInEmailInput = document.getElementById("signInEmail");
const signInPasswordInput = document.getElementById("signInPassword");
const signUpFirstNameInput = document.getElementById("signUpFirstName");
const signUpLastNameInput = document.getElementById("signUpLastName");
const signUpEmailInput = document.getElementById("signUpEmail");
const signUpPasswordInput = document.getElementById("signUpPassword");

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

function showAuthError(message) {
    window.alert(message || "Authentication failed. Please try again.");
}

function setLoading(button, isLoading) {
    if (!button) return;
    button.disabled = isLoading;
    button.textContent = isLoading ? "Please wait..." : button.getAttribute("data-label");
}

async function completeAuth(mode, formElement, buttonElement) {
    if (formElement && typeof formElement.reportValidity === "function" && !formElement.reportValidity()) {
        return;
    }

    const endpoint = mode === "signup" ? "/auth/register" : "/auth/login";
    const payload = mode === "signup"
        ? {
            firstName: signUpFirstNameInput ? signUpFirstNameInput.value.trim() : "",
            lastName: signUpLastNameInput ? signUpLastNameInput.value.trim() : "",
            email: signUpEmailInput ? signUpEmailInput.value.trim() : "",
            password: signUpPasswordInput ? signUpPasswordInput.value : "",
        }
        : {
            email: signInEmailInput ? signInEmailInput.value.trim() : "",
            password: signInPasswordInput ? signInPasswordInput.value : "",
        };

    setLoading(buttonElement, true);

    try {
        const response = await authApiFetch(endpoint, {
            method: "POST",
            body: JSON.stringify(payload),
        });

        const result = await response.json();

        if (!response.ok || !result.ok) {
            showAuthError(result.message || "Unable to authenticate.");
            return;
        }

        window.location.href = getPostLoginTarget();
    } catch (error) {
        console.error("Authentication request failed", error);
        showAuthError("Backend is unreachable. Start the PHP server and try again.");
    } finally {
        setLoading(buttonElement, false);
    }
}

if (signInSubmit) {
    signInSubmit.setAttribute("data-label", signInSubmit.textContent || "Sign in");
}

if (signUpSubmit) {
    signUpSubmit.setAttribute("data-label", signUpSubmit.textContent || "Sign up");
}

if (signInSubmit) {
    signInSubmit.addEventListener("click", function () {
        completeAuth("signin", signInForm, signInSubmit);
    });
}

if (signUpSubmit) {
    signUpSubmit.addEventListener("click", function () {
        completeAuth("signup", signUpForm, signUpSubmit);
    });
}