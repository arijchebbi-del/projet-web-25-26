# Backend Setup (PHP + MySQL)

This app is primarily server-rendered PHP pages backed by MySQL. Repositories and services handle data access and validation, and the PHP pages render HTML directly.

## 1) Configure environment

Copy backend/.env.example to backend/.env and update DB credentials (used by ConnexionDB).

## 2) Create schema and seed data

Run in MySQL:

```sql
SOURCE backend/database/schema.sql;
SOURCE backend/database/seed.sql;
```

## 3) Start development server

From the repository root (so /frontend and /backend are available):

```bash
php -S 127.0.0.1:8000 -t .
```

Then open:

- http://127.0.0.1:8000/frontend/pages/main.php

## 4) Uploads

Avatar uploads are handled by api/upload_avatar.php and are written to frontend/assets/images/uploads.

## 5) Architecture reference

See [docs/architecture.md](../docs/architecture.md) for the full module map and line references.
