# Backend Setup (PHP + MySQL)

This backend provides the first MVP API for:
- auth (register, login, logout, me)
- jobs datatable + job details
- users datatable (research)
- profile me/show/update

## 1) Configure environment

Copy `backend/.env.example` to `backend/.env` and update DB credentials.

## 2) Create schema and seed data

Run in MySQL:

```sql
SOURCE backend/database/schema.sql;
SOURCE backend/database/seed.sql;
```

## 3) Start development server

From repository root:

```bash
php -S 127.0.0.1:8000 -t backend/public backend/public/index.php
```

API base URL becomes:

- `http://127.0.0.1:8000/api`

## 4) Available endpoints

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout`
- `GET /api/auth/me`
- `GET /api/jobs/datatable`
- `GET /api/jobs/{id}`
- `GET /api/users/datatable`
- `GET /api/profile/me`
- `PUT /api/profile/me`
- `GET /api/profile/{id}`

## 5) Frontend credentials and sessions

Frontend requests must use `credentials: "include"` because auth uses PHP sessions and httpOnly cookies.

## 6) Seeded accounts

The seed file creates users with password `password`:

- `arij@insat.ucar.com`
- `alaa@insat.ucar.com`
- `talel@insat.ucar.com`
