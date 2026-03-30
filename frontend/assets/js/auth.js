(function () {
    const SESSION_KEY = "sessionId";

    function getSessionId() {
        try {
            return localStorage.getItem(SESSION_KEY);
        } catch (error) {
            console.error("Unable to read sessionId from storage", error);
            return null;
        }
    }

    function setSessionId(value) {
        try {
            const id = value || createClientSessionId();
            localStorage.setItem(SESSION_KEY, id);
            return id;
        } catch (error) {
            console.error("Unable to persist sessionId", error);
            return null;
        }
    }

    function clearSessionId() {
        try {
            localStorage.removeItem(SESSION_KEY);
        } catch (error) {
            console.error("Unable to clear sessionId", error);
        }
    }

    function createClientSessionId() {
        const entropy = Math.random().toString(36).slice(2, 10);
        return "sess_" + Date.now() + "_" + entropy;
    }

    function requireAuth(options) {
        const settings = options || {};
        const loginPath = settings.loginPath || "/frontend/pages/login.html";
        const next = encodeURIComponent(window.location.pathname + window.location.search);

        if (getSessionId()) {
            return true;
        }

        window.location.replace(loginPath + "?next=" + next);
        return false;
    }

    function redirectIfAuthed(targetPath) {
        const target = targetPath || "/frontend/pages/feed.html";
        if (getSessionId()) {
            window.location.replace(target);
            return true;
        }
        return false;
    }

    window.getSessionId = getSessionId;
    window.setSessionId = setSessionId;
    window.clearSessionId = clearSessionId;
    window.createClientSessionId = createClientSessionId;
    window.requireAuth = requireAuth;
    window.redirectIfAuthed = redirectIfAuthed;
})();
