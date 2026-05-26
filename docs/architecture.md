# System Architecture

This project is a PHP-first web app that renders pages on the server and uses vanilla JavaScript for interactivity. It uses PHP sessions + MySQL as the primary data store.

## Local run flow

1. MySQL provides the schema and seed data ([backend/database/schema.sql](backend/database/schema.sql#L1-L170), [backend/database/seed.sql](backend/database/seed.sql#L1-L120)).
2. PHP pages use ConnexionDB to load environment config and create a PDO instance ([backend/config/ConnexionDB.php](backend/config/ConnexionDB.php#L1-L51)).
3. Frontend pages render HTML directly and then load shared UI components + JS helpers with root.js ([frontend/assets/js/root.js](frontend/assets/js/root.js#L1-L70)).

## Core UI components

- Navbar + global search + navigation links are in [frontend/components/navbar.php](frontend/components/navbar.php#L1-L90).
- Footer content is in [frontend/components/footer.php](frontend/components/footer.php#L1-L5).
- Shared helpers (loadComponent, theme toggle, active nav) live in [frontend/assets/js/root.js](frontend/assets/js/root.js#L1-L70).

## Landing page

- The landing page layout is in [frontend/pages/main.php](frontend/pages/main.php#L12-L210).
- The hero background swaps on an interval, preloads slide images, and fades between them in [frontend/assets/js/main.js](frontend/assets/js/main.js#L1-L123).
	The slide list references images under the promo images folder (see the promoSlides list in [frontend/assets/js/main.js](frontend/assets/js/main.js#L15-L31)).

## Authentication and sessions

- Login and signup logic is handled in [frontend/pages/login.php](frontend/pages/login.php#L1-L150).
- UI toggling between login/signup panels is in [frontend/assets/js/login.js](frontend/assets/js/login.js#L1-L22).
- Session cleanup + redirect to the home page happens in [frontend/pages/logout.php](frontend/pages/logout.php#L1-L13).

## Feed and discovery

- The feed page pulls profiles, jobs, and internships with repositories and renders cards server-side in [frontend/pages/feed.php](frontend/pages/feed.php#L1-L150).
- There is also an API-driven feed renderer in [frontend/assets/js/feed.js](frontend/assets/js/feed.js#L1-L200).

## Jobs and opportunities

- The job list page with filters is in [frontend/pages/job.php](frontend/pages/job.php#L1-L170).
- Job filtering + queries are in [backend/repository/jobRepository.php](backend/repository/jobRepository.php#L41-L84).
- The DataTables-based jobs table and filter wiring lives in [frontend/assets/js/job.js](frontend/assets/js/job.js#L1-L155).
- Job details (salary, country/city names, responsibilities) render in [frontend/pages/post.php](frontend/pages/post.php#L1-L220), backed by the joined lookup in [backend/repository/jobRepository.php](backend/repository/jobRepository.php#L19-L29).
- Job creation (full form + insert) is in [frontend/pages/old%20createpost.php](frontend/pages/old%20createpost.php#L1-L230).
- The job form interactivity (skills, chip toggles, country reveal, submit state) lives in [frontend/assets/js/createPost.js](frontend/assets/js/createPost.js#L1-L190).

## Text posts

- Text post creation is handled by [frontend/pages/createPost.php](frontend/pages/createPost.php#L1-L170).
- The text-post client behavior (char counter, loader, success state) is in [frontend/assets/js/textPost.js](frontend/assets/js/textPost.js#L1-L90).

## Search (global)

- The navbar search submits to the global search page [frontend/components/navbar.php](frontend/components/navbar.php#L60-L64).
- The global search query handler (users + jobs + posts) is in [frontend/pages/recherche.php](frontend/pages/recherche.php#L1-L170).
- Result cards by type (user/job/post) render in [frontend/assets/js/recherche.js](frontend/assets/js/recherche.js#L1-L120).

## Profiles and recommendations

- Self-profile edit view, skill/experience/project/achievement lists, and modal edit flow are in [frontend/pages/myprofile.php](frontend/pages/myprofile.php#L1-L210).
- Public profile display + recommendation submission is in [frontend/pages/profil.php](frontend/pages/profil.php#L1-L210).
- Profile aggregation and save logic live in [backend/service/profileService.php](backend/service/profileService.php#L1-L96).
- User data access and sync helpers are in [backend/repository/userRepository.php](backend/repository/userRepository.php#L1-L200).
- Client-side modal population and dynamic rows for profile editing are in [frontend/assets/js/myprofile.js](frontend/assets/js/myprofile.js#L1-L200).
- Client-side profile API rendering and recommendation/post actions are in [frontend/assets/js/profil.js](frontend/assets/js/profil.js#L1-L200).

## Contact and support

- Contact form UI + Ajax POST lives in [frontend/pages/contact.php](frontend/pages/contact.php#L1-L200).
- Contact service validation and persistence are in [backend/service/contactService.php](backend/service/contactService.php#L1-L30) and [backend/repository/contactRepository.php](backend/repository/contactRepository.php#L1-L28).
- Help page content is in [frontend/pages/help.php](frontend/pages/help.php#L1-L120).

## Uploads

- Avatar upload endpoint and file validation are in [api/upload_avatar.php](api/upload_avatar.php#L1-L120).

## Database schema

- Core schema (users, jobs, posts, skills, recommendations, contact) is defined in [backend/database/schema.sql](backend/database/schema.sql#L1-L200).
- Seed data (filieres, parcours, countries, cities, demo users/jobs) is defined in [backend/database/seed.sql](backend/database/seed.sql#L1-L160).
