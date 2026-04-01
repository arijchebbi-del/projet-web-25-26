USE webdb;

INSERT IGNORE INTO filieres (id, name) VALUES
    (1, 'IIA'),
    (2, 'GL'),
    (3, 'RT');

INSERT IGNORE INTO parcours (id, name, filiere_id) VALUES
    (1, 'AI Engineering', 1),
    (2, 'Software Engineering', 2),
    (3, 'Networks and Systems', 3);

INSERT IGNORE INTO countries (id, name) VALUES
    (1, 'Tunisia'),
    (2, 'Morocco'),
    (3, 'France');

INSERT IGNORE INTO cities (id, country_id, name) VALUES
    (1, 1, 'Tunis'),
    (2, 1, 'Sfax'),
    (3, 1, 'Sousse'),
    (4, 2, 'Casablanca'),
    (5, 3, 'Paris');

INSERT IGNORE INTO insatien (id, nom, prenom, email, promo_year, filiere_id, parcours_id) VALUES
    (1, 'Chebbi', 'Arij', 'arij@insat.ucar.com', 2026, 2, 2),
    (2, 'Fadhel', 'Alaa', 'alaa@example.insat.ucar.tn', 2026, 1, 1),
    (3, 'Laarif', 'Talel', 'talel@example.ucar.tn', 2025, 2, 2);

INSERT IGNORE INTO users (id, email, password_hash, profile_link, bio, insatien_id) VALUES
    (1, 'arij@insat.ucar.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'https://linkedin.com/in/arij', 'Software engineer focused on web systems.', 1),
    (2, 'alaa@insat.ucar.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'https://linkedin.com/in/alaa', 'AI and data enthusiast.', 2),
    (3, 'talel@insat.ucar.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'https://linkedin.com/in/talel', 'Backend and architecture interest.', 3);

INSERT IGNORE INTO skills (id, name) VALUES
    (1, 'JavaScript'),
    (2, 'PHP'),
    (3, 'MySQL'),
    (4, 'Python'),
    (5, 'Machine Learning');

INSERT IGNORE INTO user_skills (user_id, skill_id) VALUES
    (1, 1), (1, 2), (1, 3),
    (2, 4), (2, 5),
    (3, 2), (3, 3);

INSERT IGNORE INTO jobs (
    id, titre, entreprise, type, remote, description, requirements, responsibilities,
    localisation, salary_min, salary_max, currency, req_experience, country_id, city_id, created_by
) VALUES
    (1, 'Software Engineer', 'Pixelz Studio', 'full-time', 1,
     'Build and maintain modern web applications.',
     'Strong foundation in JS/PHP and SQL.',
     'Design features, review code, and ship reliable releases.',
     'Tunis', 1800, 2800, 'TND', 2, 1, 1, 1),
    (2, 'Data Analyst Intern', 'Insight Lab', 'internship', 0,
     'Support data dashboards and reporting.',
     'Python, SQL, and basic statistics.',
     'Clean datasets and prepare insights.',
     'Sfax', 600, 900, 'TND', 0, 1, 2, 2),
    (3, 'UI/UX Designer', 'Creative Hub', 'part-time', 1,
     'Create user-centered design systems.',
     'Portfolio with Figma or Adobe XD projects.',
     'Prototype interfaces and run user tests.',
     'Paris', 1200, 1800, 'EUR', 1, 3, 5, 3);

INSERT IGNORE INTO recommandations (id, from_user, to_user, texte) VALUES
    (1, 2, 1, 'Strong collaborator with excellent delivery quality.'),
    (2, 3, 1, 'Reliable teammate with clean coding practices.'),
    (3, 1, 2, 'Great analytical mindset and fast learner.');
