--- we create the dabase in phpmyadmin ---
CREATE DATABASE alumini; 
USE alumini; 
 
CREATE TABLE insatien ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    nom VARCHAR(100) NOT NULL, 
    prenom VARCHAR(100) NOT NULL, 
    email VARCHAR(150) UNIQUE NOT NULL, 
); 
CREATE TABLE users ( 
    id INT AUTO_INCREMENT PRIMARY KEY, (id teou ka user) 
    email VARCHAR(150) UNIQUE NOT NULL, 
    password VARCHAR(255) NOT NULL, 
    profile_link TEXT, 
    insatien_id INT UNIQUE,(id teou fl insat bch naamlou bih e lien) 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
 
    FOREIGN KEY (insatien_id) REFERENCES insatien(id) 
        ON DELETE CASCADE 
); 
CREATE TABLE jobs ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    titre VARCHAR(255) NOT NULL, 
    entreprise VARCHAR(255), 
    type ENUM('part-time', 'full-time’,’internship’') NOT NULL, 
    remote boolean, 
    description TEXT, 
    requirements TEXT, 
    responsibilities TEXT, 
    localisation VARCHAR(255), 
    salaire VARCHAR(100), 
    req_experience INT, 
    date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    created_by INT, 
    FOREIGN KEY (created_by) REFERENCES users(id) 
        ON DELETE SET NULL 
); 
CREATE TABLE recommandations ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    from_user INT, 
    to_user INT, 
    texte TEXT, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    FOREIGN KEY (from_user) REFERENCES users(id) 
        ON DELETE CASCADE, 
    FOREIGN KEY (to_user) REFERENCES users(id) 
        ON DELETE CASCADE 
); 
CREATE TABLE experience ( 
    id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT NOT NULL, 
    date_debut DATE, 
    date_fin DATE, 
    periode VARCHAR(100),  
    entreprise VARCHAR(255), 
    type ENUM('skill', 'job', 'certification') NOT NULL, 
    lien TEXT, 
    description TEXT, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
 
    FOREIGN KEY (user_id) REFERENCES users(id) 
        ON DELETE CASCADE 
);