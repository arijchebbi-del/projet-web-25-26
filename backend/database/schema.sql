
CREATE DATABASE IF NOT EXISTS webdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE webdb;

CREATE TABLE IF NOT EXISTS filieres (
    id   INT          AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS parcours (
    id         INT          AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120) NOT NULL UNIQUE,
    filiere_id INT          NULL,
    FOREIGN KEY (filiere_id) REFERENCES filieres(id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS countries (
    id   INT          AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS cities (
    id         INT          AUTO_INCREMENT PRIMARY KEY,
    country_id INT          NOT NULL,
    name       VARCHAR(120) NOT NULL,
    UNIQUE KEY unique_city_country (country_id, name),
    FOREIGN KEY (country_id) REFERENCES countries(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS insatien (
    id          INT          AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100) NOT NULL,
    prenom      VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    promo_year  INT          NULL,
    parcours_id INT          NULL,
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parcours_id) REFERENCES parcours(id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS users (
    id           INT          AUTO_INCREMENT PRIMARY KEY,
    email        VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    profile_link TEXT         NULL,
    github_link TEXT         NULL,
    linkedin_link TEXT         NULL,

    tagline      VARCHAR(255) NULL,
    bio          TEXT         NULL,
    avatar_url   TEXT         NULL,
    insatien_id  INT          NOT NULL UNIQUE,
    created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (insatien_id) REFERENCES insatien(id)
        ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS jobs (
    id               INT            AUTO_INCREMENT PRIMARY KEY,
    titre            VARCHAR(255)   NOT NULL,
    entreprise       VARCHAR(255)   NULL,
    job_type         ENUM('part-time','full-time','internship') NOT NULL,
    job_mode         ENUM('remote','onsite','hybrid') NOT NULL,
    description      TEXT           NULL,
    application_link TEXT           NULL,
    company_link     TEXT           NULL,
    contact_email    VARCHAR(150)   NOT NULL UNIQUE,
    requirements     TEXT           NULL,
    responsibilities TEXT           NULL,
    salary_min       DECIMAL(10,2)  NULL,
    salary_max       DECIMAL(10,2)  NULL,
    currency         CHAR(3)        NOT NULL DEFAULT 'TND',
    req_experience   INT            NULL,
    country_id       INT            NULL,
    city_id          INT            NULL,
    date_publication TIMESTAMP      DEFAULT CURRENT_TIMESTAMP,
    deadline         TIMESTAMP NULL DEFAULT NULL,
    created_by       INT            NULL,

    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE SET NULL,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE SET NULL,

    INDEX idx_jobs_type       (job_type),
    INDEX idx_jobs_mode       (job_mode),
    INDEX idx_jobs_salary     (salary_min, salary_max),
    INDEX idx_jobs_experience (req_experience)
);


CREATE TABLE IF NOT EXISTS recommandations (
    id         INT       AUTO_INCREMENT PRIMARY KEY,
    from_user  INT       NOT NULL,
    to_user    INT       NOT NULL,
    texte      TEXT      NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_no_self_recommandation CHECK (from_user <> to_user),
    FOREIGN KEY (from_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (to_user)   REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS experience (
    id              INT          AUTO_INCREMENT PRIMARY KEY,
    user_id         INT          NOT NULL,
    date_debut      DATE         NULL,
    date_fin        DATE         NULL,      -- NULL = current position
    entreprise      VARCHAR(255) NULL,
    experience_type ENUM('skill','job','certification') NOT NULL,
    lien            TEXT         NULL,
    description     TEXT         NULL,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_experience_user (user_id),
    INDEX idx_experience_type (experience_type)
);

-- Skills (normalised tag table + pivot)

CREATE TABLE IF NOT EXISTS skills (
    id   INT          AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS user_skills (
    user_id  INT NOT NULL,
    skill_id INT NOT NULL,
    PRIMARY KEY (user_id, skill_id),
    FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS projects (
    id          INT          AUTO_INCREMENT PRIMARY KEY,
    user_id     INT          NOT NULL,
    title       VARCHAR(255) NOT NULL,
    description TEXT         NULL,
    lien        TEXT         NULL,           -- repo / live URL
    date_debut  DATE         NULL,
    date_fin    DATE         NULL,           -- NULL = ongoing
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_projects_user (user_id)
);

CREATE TABLE IF NOT EXISTS project_skills (
    project_id INT NOT NULL,
    skill_id   INT NOT NULL,
    PRIMARY KEY (project_id, skill_id),
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id)   REFERENCES skills(id)   ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS achievements (
    id               INT          AUTO_INCREMENT PRIMARY KEY,
    user_id          INT          NOT NULL,
    title            VARCHAR(255) NOT NULL,
    issuer           VARCHAR(255) NULL,      -- organisation / institution
    achievement_type ENUM('award','honour','publication','competition','other') NOT NULL DEFAULT 'other',
    date_obtained    DATE         NULL,
    lien             TEXT         NULL,
    description      TEXT         NULL,
    created_at       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_achievements_user (user_id),
    INDEX idx_achievements_type (achievement_type)
);

CREATE TABLE IF NOT EXISTS posts (
    id         INT       AUTO_INCREMENT PRIMARY KEY,
    user_id    INT       NOT NULL,
    content    TEXT      NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_posts_user (user_id),
    INDEX idx_posts_created_at (created_at)
);

CREATE TABLE IF NOT EXISTS contact_messages (
    id         INT          AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name  VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    topic      VARCHAR(120) NULL,
    message    TEXT         NOT NULL,
    created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_contact_email      (email),
    INDEX idx_contact_created_at (created_at)
);

