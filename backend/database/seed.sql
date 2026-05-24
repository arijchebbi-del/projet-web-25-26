INSERT INTO filieres (name) VALUES
('GL'),
('RT'),
('IIA'),
('IMI'),
('BIO'),
('CH');


INSERT INTO parcours (name, filiere_id) VALUES
('Genie Logiciel', 1),
('Data Engineering', 1),
('Cyber Security', 2),
('Cloud & Networks', 2),
('Automation Industrielle', 3),
('Embedded Systems', 3),
('Mecatronique', 4),
('Instrumentation Biomedicale', 5),
('Chimie Industrielle', 6);

INSERT INTO countries (name) VALUES
('Tunisia'),
('France'),
('Canada'),
('Germany');

INSERT INTO cities (country_id, name) VALUES
(1, 'Tunis'),
(1, 'Sfax'),
(1, 'Sousse'),
(1, 'Nabeul'),
(1, 'Monastir'),
(1, 'Ariana'),
(2, 'Paris'),
(2, 'Lyon'),
(3, 'Montreal'),
(4, 'Berlin');


-- =========================
INSERT INTO insatien
(nom, prenom, email, promo_year, parcours_id)
VALUES
('Chebbi', 'Arij', 'arij.chebbi@insat.ucar.tn', 2022, 1),
('Klai', 'Loua', 'loua.klai@insat.ucar.tn', 2023, 2),
('Selmi', 'Wala', 'wala.selmi@insat.ucar.tn', 2021, 3),
('Laarif', 'Talel', 'talel.laarif@insat.ucar.tn', 2024, 1),
('Fadhel', 'Alaa', 'alaa.fadhel@insat.ucar.tn', 2020, 4),
('Ben Salem', 'Aya', 'aya.bensalem@insat.ucar.tn', 2022, 5),
('Chaabani', 'Walid', 'walid.chaabani@insat.ucar.tn', 2021, 6),
('Kefi', 'Rim', 'rim.kefi@insat.ucar.tn', 2023, 2),
('Abid', 'Hedi', 'hedi.abid@insat.ucar.tn', 2019, 7),
('Mejri', 'Mariem', 'mariem.mejri@insat.ucar.tn', 2024, 1);

INSERT INTO users
(email, password_hash, profile_link, github_link,
 linkedin_link, bio, avatar_url, insatien_id)
VALUES
(
 'arij.chebbi@gmail.com',
 '$2y$10$hash1',
 'https://portfolio-ahmed.dev',
 'https://github.com/ahmedbenamor',
 'https://linkedin.com/in/ahmedbenamor',
 'Software engineer passionate about AI and distributed systems.',
 'https://randomuser.me/api/portraits/men/1.jpg',
 1
),
(
 'loua.klai@gmail.com',
 '$2y$10$hash2',
 'https://yasmine-tech.dev',
 'https://github.com/yasminetr',
 'https://linkedin.com/in/yasminetr',
 'Data engineering student interested in big data and analytics.',
 'https://randomuser.me/api/portraits/women/2.jpg',
 2
),
(
 'wala.selmi@gmail.com',
 '$2y$10$hash3',
 NULL,
 'https://github.com/walaselmi',
 'https://linkedin.com/in/walaselmi',
 'Cybersecurity enthusiast and CTF player.',
 'https://randomuser.me/api/portraits/men/3.jpg',
 3
),
(
 'sarra.gharbi@gmail.com',
 '$2y$10$hash4',
 NULL,
 'https://github.com/sarragh',
 'https://linkedin.com/in/sarragh',
 'Full stack developer and open-source contributor.',
 'https://randomuser.me/api/portraits/women/4.jpg',
 4
),
(
 'mohamed.jlassi@gmail.com',
 '$2y$10$hash5',
 NULL,
 'https://github.com/mjlassi',
 'https://linkedin.com/in/mjlassi',
 'Cloud engineer working on scalable infrastructures.',
 'https://randomuser.me/api/portraits/men/5.jpg',
 5
);


INSERT INTO skills (name) VALUES
('Java'),
('Spring Boot'),
('Symfony'),
('React'),
('Angular'),
('Docker'),
('Kubernetes'),
('Python'),
('Machine Learning'),
('SQL'),
('Oracle'),
('Linux'),
('Cybersecurity'),
('Networking'),
('Node.js');


INSERT INTO user_skills (user_id, skill_id) VALUES
(1, 1),
(1, 2),
(1, 6),
(1, 10),

(2, 8),
(2, 9),
(2, 10),

(3, 13),
(3, 14),
(3, 12),

(4, 3),
(4, 4),
(4, 15),

(5, 6),
(5, 7),
(5, 12);

INSERT INTO jobs
(
 titre, entreprise, job_type, job_mode,
 description, application_link, company_link,
 contact_email, requirements, responsibilities,
 salary_min, salary_max, currency,
 req_experience, country_id, city_id,
 created_by
)
VALUES
(
 'Backend Java Developer',
 'Vermeg',
 'full-time',
 'hybrid',
 'Development of enterprise backend solutions.',
 'https://vermeg.com/careers',
 'https://vermeg.com',
 'hr@vermeg.com',
 'Java, Spring Boot, SQL',
 'Develop REST APIs and microservices',
 2500,
 4000,
 'TND',
 2,
 1,
 1,
 1
),
(
 'Data Engineering Intern',
 'Sopra HR',
 'internship',
 'onsite',
 'Work on ETL and analytics pipelines.',
 'https://soprahr.com/jobs',
 'https://soprahr.com',
 'internships@soprahr.com',
 'Python, SQL, Spark basics',
 'Build and maintain data workflows',
 800,
 1200,
 'TND',
 0,
 1,
 6,
 2
),
(
 'Cybersecurity Analyst',
 'Telnet',
 'full-time',
 'remote',
 'Monitor and secure enterprise systems.',
 'https://groupe-telnet.com',
 'https://groupe-telnet.com',
 'jobs@telnet.com',
 'Networking, Linux, Security',
 'Incident response and vulnerability analysis', 3000,5000,'TND',3,1,1,3);


INSERT INTO experience
(user_id, date_debut, date_fin,
 entreprise, experience_type,
 lien, description)
VALUES
(
 1,
 '2023-06-01',
 '2023-08-31',
 'Vermeg',
 'internship',
 'https://vermeg.com',
 'Worked on Spring Boot microservices.'
),
(
 2,
 '2024-01-15',
 NULL,
 'Freelance',
 'freelance',
 NULL,
 'Created dashboards and analytics reports.'
),
(
 3,
 '2022-07-01',
 '2022-09-01',
 'Tunisie Telecom',
 'internship',
 NULL,
 'Network monitoring and security audits.'
);

INSERT INTO projects
(user_id, title, description,
 lien, date_debut, date_fin)
VALUES
(
 1,
 'Smart Campus',
 'Web platform for university management.',
 'https://github.com/ahmedbenamor/smart-campus',
 '2024-01-01',
 '2024-05-01'
),
(
 2,
 'Big Data Analytics',
 'Pipeline for processing student data.',
 'https://github.com/yasminetr/bigdata-project',
 '2023-09-01',
 '2024-01-15'
),
(
 4,
 'Pharma Management App',
 'Pharmacy stock and order management system.',
 'https://github.com/sarragh/pharma-app',
 '2025-01-01',
 NULL
);


INSERT INTO project_skills (project_id, skill_id) VALUES
(1, 2),
(1, 10),
(1, 6),

(2, 8),
(2, 9),
(2, 10),

(3, 3),
(3, 4),
(3, 10);


INSERT INTO achievements
(user_id, title, issuer,
 achievement_type, date_obtained,
 lien, description)
VALUES
(
 1,
 'Hackathon Winner',
 'INSAT',
 'competition',
 '2024-04-15',
 NULL,
 'Won first place in INSAT Hackathon.'
),
(
 2,
 'NVIDIA Computer Vision Certification',
 'NVIDIA',
 'award',
 '2025-02-16',
 NULL,
 'Certification in Computer Vision for Industrial Inspection.'
),
(
 3,
 'Top CTF Player',
 'Tunisia CyberCup',
 'competition',
 '2023-11-10',
 NULL,
 'Ranked in top 5 nationally.'
);


INSERT INTO recommandations
(from_user, to_user, texte)
VALUES
(
 1,
 2,
 'Yasmine is extremely skilled in data engineering and teamwork.'
),
(
 2,
 1,
 'Ahmed is a reliable backend developer with strong problem-solving skills.'
),
(
 4,
 3,
 'Omar has excellent cybersecurity knowledge and analytical thinking.'
);

INSERT INTO contact_messages
(first_name, last_name, email,
 topic, message)
VALUES
(
 'Ali',
 'Ben Salah',
 'ali.bensalah@gmail.com',
 'Internship',
 'Hello, I would like to know if internship offers are still available.'
),
(
 'Meriem',
 'Khalfallah',
 'meriem.kh@gmail.com',
 'Bug Report',
 'There is an issue when uploading profile pictures.'
),
(
 'Hichem',
 'Tlili',
 'hichem.tlili@gmail.com',
 'Partnership',
 'We are interested in collaborating with INSAT alumni.'
);