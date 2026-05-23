# System Architecture

This project is a PHP backend with a vanilla HTML/CSS/JavaScript frontend.

## What runs where

- `backend/public/index.php` is the single HTTP entry point for the API.
- `backend/src/config/App.php` loads `.env` values such as database credentials and allowed CORS origins.
- `backend/src/config/Database.php` builds the PDO connection to MySQL.
- `backend/src/controllers/*.php` implement the API behavior.
- `frontend/pages/*.html` render pages and load shared layout parts.
- `frontend/assets/js/*.js` call the API and update the DOM.

## Request flow

1. A user opens an HTML page in `frontend/pages`.
2. The page loads shared layout components with `frontend/assets/js/root.js`.
3. Frontend scripts call `authApiFetch()` from `frontend/assets/js/auth.js`.
4. `authApiFetch()` sends requests to `http://127.0.0.1:8000/api` with `credentials: "include"`.
5. `backend/public/index.php` routes the request to the correct controller method.
6. The controller reads or writes MySQL data through `Database::connection()`.
7. The controller returns JSON to the browser.
8. Frontend code renders the response into cards, forms, modals, and profile sections.

## API mapping

- `POST /api/auth/register` and `POST /api/auth/login` are handled by `AuthController`.
- `GET /api/auth/me` and `POST /api/auth/logout` are also handled by `AuthController`.
- `GET /api/users/datatable` is handled by `UsersController`.
- `GET /api/jobs/datatable`, `GET /api/jobs/{id}`, and `POST /api/jobs` are handled by `JobsController`.
- `GET /api/posts` and `POST /api/posts` are handled by `PostsController`.
- `GET /api/profile/me`, `PUT /api/profile/me`, `GET /api/profile/{id}`, and `POST /api/profile/{id}/recommend` are handled by `ProfileController`.

## Database structure verified against the live MySQL instance

The live database matches the repository schema structure for the core tables.

Verified tables:

- `filieres`
- `parcours`
- `countries`
- `cities`
- `insatien`
- `users`
- `jobs`
- `recommandations`
- `experience`
- `skills`
- `user_skills`
- `contact_messages`

Live row snapshot from the database connected through PHP:

- `filieres`: 3
- `parcours`: 3
- `countries`: 3
- `cities`: 5
- `insatien`: 10
- `users`: 10
- `jobs`: 8
- `recommandations`: 12
- `experience`: 0
- `skills`: 6
- `user_skills`: 7
- `contact_messages`: 0

Important schema note:

- The backend profile flow expects `users.profile_link`, `users.bio`, `users.avatar_url`, and `insatien.promo_year`.
- Those columns exist in the live database, so profile editing, promo updates, and avatar uploads are structurally supported.

## Frontend wiring

- `frontend/assets/js/auth.js` is the shared fetch wrapper and session helper.
- `frontend/assets/js/login.js` handles login and signup form submission.
- `frontend/assets/js/root.js` loads the navbar/footer components and theme behavior.
- `frontend/assets/js/feed.js` builds the feed from `/api/users/datatable`, `/api/jobs/datatable`, and `/api/posts`.
- `frontend/assets/js/profil.js` loads a user profile page and related recommendations/posts.

## How the pages connect to the API

- `frontend/pages/login.html` uses `login.js` and `auth.js` to authenticate users.
- `frontend/pages/feed.html` uses `feed.js` to fetch profiles, jobs, and posts.
- `frontend/pages/profil.html` and `frontend/pages/myprofile.html` display profile data and allow updates through the profile endpoints.
- Pages that require authentication call `requireAuth()` from `auth.js` before loading private data.

## Key design decisions

- The app uses PHP sessions instead of JWTs, so browser requests must include credentials.
- The backend is organized as a small router plus controller layer, not a full framework.
- The frontend stays framework-free and updates the DOM directly.
- MySQL is the system of record for users, jobs, posts, skills, and recommendations.

## Practical run order

1. Start MySQL.
2. Start the PHP server with `C:\tools\php85\php.exe -S 127.0.0.1:8000 -t backend/public backend/public/index.php`.
3. Open the frontend from a local web server on port `5500` or `5501`.
4. Log in with a seeded account.
5. The frontend fetches API data and renders it into the active page.
