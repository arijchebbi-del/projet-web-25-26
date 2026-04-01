(function () {
    const DEFAULT_API_BASE = "http://127.0.0.1:8000/api";
    const API_BASE = window.API_BASE_URL || DEFAULT_API_BASE;

    let sessionUser = null;
    let sessionCheckedAt = 0;

    async function apiFetch(path, options) {
        const requestOptions = options || {};
        const headers = Object.assign({}, requestOptions.headers || {});

        if (!(requestOptions.body instanceof FormData) && !headers["Content-Type"]) {
            headers["Content-Type"] = "application/json";
        }

        return fetch(API_BASE + path, Object.assign({}, requestOptions, {
            credentials: "include",
            headers: headers,
        }));
    }

    async function getSessionUser(force) {
        const shouldForce = force === true;
        const now = Date.now();

        if (!shouldForce && sessionCheckedAt && now - sessionCheckedAt < 30000) {
            return sessionUser;
        }

        try {
            const response = await apiFetch("/auth/me", { method: "GET" });
            sessionCheckedAt = now;

            if (!response.ok) {
                sessionUser = null;
                return null;
            }

            const payload = await response.json();
            sessionUser = payload && payload.ok ? payload.data : null;
            return sessionUser;
        } catch (error) {
            console.error("Session validation failed", error);
            sessionCheckedAt = now;
            sessionUser = null;
            return null;
        }
    }

    async function requireAuth(options) {
        const settings = options || {};
        const loginPath = settings.loginPath || "/frontend/pages/login.html";
        const user = await getSessionUser(true);

        if (user) {
            return true;
        }

        const next = encodeURIComponent(window.location.pathname + window.location.search);
        window.location.replace(loginPath + "?next=" + next);
        return false;
    }

    async function redirectIfAuthed(targetPath) {
        const target = targetPath || "/frontend/pages/feed.html";
        const user = await getSessionUser(false);

        if (user) {
            window.location.replace(target);
            return true;
        }

        return false;
    }

    async function logoutSession() {
        try {
            await apiFetch("/auth/logout", { method: "POST" });
        } catch (error) {
            console.error("Logout request failed", error);
        }

        sessionUser = null;
        sessionCheckedAt = Date.now();

        try {
            localStorage.removeItem("sessionId");
        } catch (error) {
            console.error("Unable to clear legacy session key", error);
        }
    }

    window.authApiBase = API_BASE;
    window.authApiFetch = apiFetch;
    window.getSessionUser = getSessionUser;
    window.requireAuth = requireAuth;
    window.redirectIfAuthed = redirectIfAuthed;
    window.logoutSession = logoutSession;
})();
