# Project Recommendations and Migration Plan

## 1) Current State Summary

The project is already working as a classic frontend + PHP backend application.

- The frontend is plain HTML, CSS, and JavaScript inside `frontend/`.
- The backend is a PHP API in `backend/`.
- The current backend routing is centralized in `backend/public/index.php`.
- The backend uses session-based auth through `SessionAuth`.
- The frontend calls the backend with `fetch()` and includes credentials for session cookies.

This is a good base if the professor expects a simple structure now and a later migration to Symfony after validation.

## 2) What Is Working Well

The project logic is already clean enough for a student project:

- HTML pages are separated by feature.
- CSS is separated by page or component.
- JavaScript is separated by page or feature.
- PHP controllers already group backend logic by domain: auth, jobs, profile, posts, and users.
- The frontend already consumes JSON APIs, so later Symfony migration will be easier.

The main thing that still looks "centralized" is routing, not the whole application design.

## 3) Main Recommendation

Keep the frontend simple and stable now.

Do not over-engineer the UI or rewrite everything before validation.

Recommended direction:

- Keep frontend as plain HTML, CSS, JS.
- Keep PHP backend lightweight.
- Split routing into clearer PHP route files or resource-level dispatch files.
- Keep controllers focused on business logic only.
- After validation, migrate the backend to Symfony in a controlled way.

This gives you the best balance between simplicity for the professor and a maintainable path forward.

## 4) Recommended Architecture for the Current Stage

### Frontend

The frontend should stay static and easy to understand:

- one HTML file per page
- one CSS file per page when needed
- one JS file per page or feature
- shared header/footer/navbar as reusable components

Avoid adding a frontend framework unless the professor explicitly asks for one.

### Backend

The backend should stay PHP-based, but routing should be cleaner than one huge `if` chain.

Recommended structure:

- a shared bootstrap file for env/session/CORS setup
- resource-based route files for `auth`, `jobs`, `profile`, `posts`, and `users`
- existing controllers reused as-is
- JSON responses kept consistent

This keeps the code understandable and prepares the project for Symfony later.

## 5) How To Migrate Routing Step by Step

### Phase 1: Freeze the current behavior

Before changing anything, document the current API contract:

- endpoint path
- HTTP method
- input payload
- auth requirement
- response structure
- error codes

This prevents accidental regressions.

### Phase 2: Extract shared bootstrap

Move common backend setup out of the main router:

- environment loading
- session start
- CORS headers
- OPTIONS preflight handling
- shared error response wrapper

The goal is to avoid duplicating these rules in every file.

### Phase 3: Split routing by resource

Create route handlers per feature:

- auth routes
- jobs routes
- profile routes
- posts routes
- users routes

Each route file should only care about its own paths and methods.

### Phase 4: Keep controllers unchanged at first

Do not refactor controller logic immediately.

First, move routing only.

Later, if needed, clean up controller internals.

### Phase 5: Verify route parity

After every migration step, test:

- login and register
- profile view and update
- jobs list and job detail
- posts creation and listing
- unauthorized access behavior

### Phase 6: Prepare Symfony later

Once validation is complete, Symfony migration becomes easier because:

- routes are already separated logically
- controllers already exist by domain
- frontend already uses API calls
- session and JSON conventions are already in place

## 6) My Recommendation for the Professor-Friendly Version

If the goal is to present a clean and simple student project, the best visible version is:

- static frontend pages
- modular CSS and JS
- simple PHP API backend
- clearer PHP routing by resource
- no framework complexity before validation

That is the safest version to defend in front of a professor.

## 7) Validation Checklist Before Any Symfony Migration

Use this checklist before moving to Symfony:

- all current routes behave the same
- sessions work correctly
- CORS works from the frontend origin
- forms still submit correctly
- Datatable/job/profile calls still work
- unknown routes return a consistent error
- responses remain JSON
- no page depends on hidden global state

If these pass, the project is ready for the next migration stage.

## 8) How To Parallelize the Work Across 4 People

### Person 1: Frontend structure and cleanup

Focus on HTML, CSS, and JS organization.

Tasks:

- review each page for unnecessary duplication
- keep HTML semantic and simple
- clean shared navbar/footer usage
- check JS files for repeated logic
- make sure page scripts are easy to read

Deliverable:

- a stable frontend that is easy to explain and demo

### Person 2: Backend routing and bootstrap

Focus on the PHP request flow.

Tasks:

- document current routes
- split centralized routing into resource-based route files
- keep shared bootstrap logic in one place
- preserve current API URLs
- make sure preflight and session handling still work

Deliverable:

- a cleaner PHP backend routing structure without changing features

### Person 3: Controllers and API behavior

Focus on backend business logic and response consistency.

Tasks:

- review controller methods for validation consistency
- check response payloads and status codes
- make auth/session behavior consistent
- reduce duplicated response patterns where possible
- verify database calls and error handling

Deliverable:

- stable controller behavior that will transfer easily to Symfony later

### Person 4: Testing, documentation, and migration prep

Focus on verification and project documentation.

Tasks:

- test each endpoint manually
- document the API contract
- write the migration notes
- prepare Symfony-ready documentation
- track regressions during routing changes

Deliverable:

- documented, validated project state with a clear next-step plan

## 9) Suggested Work Split Order

The best parallel order is:

1. Person 4 documents the current route contract.
2. Person 2 starts routing/bootstrap cleanup.
3. Person 3 checks controller consistency.
4. Person 1 cleans the frontend only after the route contract is stable.

That order reduces conflict and avoids frontend work depending on unstable backend changes.

## 10) Risks To Avoid

- Do not change URLs during the first routing migration.
- Do not rewrite controllers and frontend at the same time.
- Do not add a framework before the current version is validated.
- Do not create one-off fixes that bypass the shared response/session rules.
- Do not let route changes break the existing login/session flow.

## 11) Final Recommendation

For the current project stage, the strongest strategy is:

- keep the frontend simple
- clean the PHP routing structure
- preserve behavior exactly
- validate everything
- then move to Symfony with confidence

This is the lowest-risk path and the easiest to explain to a professor.