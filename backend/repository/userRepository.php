<?php

require_once __DIR__ . '/../config/ConnexionDB.php';
class userRepository {

    private $pdo;

    public function __construct() {
        $this->pdo = ConnexionDB::getInstance();
    }

    public function findAllProfiles() {

        $stmt = $this->pdo->prepare("
            SELECT users.*, insatien.nom, insatien.prenom
            FROM users
            JOIN insatien ON users.insatien_id = insatien.id
            ORDER BY users.created_at DESC
            LIMIT 10
        ");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function findByEmail(string $email): array|false {
        $stmt = $this->pdo->prepare("
            SELECT id, email
            FROM users
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    //recuperer le profil a afficher  elih bech nebniou alih khedma we sessions 
    public function findById($userId) : array|false{
        $stmt=$this->pdo->prepare("
            SELECT
                u.id,u.email,u.bio,u.tagline,u.avatar_url,u.github_link,
                u.linkedin_link,u.profile_link,i.nom,i.prenom,i.promo_year,
                i.id AS insatien_id, p.id AS parcours_id, p.name AS parcours,
                f.id AS filiere_id, f.name AS filiere
            FROM users u
            JOIN insatien i ON i.id = u.insatien_id
            LEFT JOIN parcours p ON p.id = i.parcours_id
            LEFT JOIN filieres f ON f.id = p.filiere_id
            WHERE u.id = ? 
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }
    //recuperer les skills d'un user pour page user
    public function findskills(int $userId): array|false {
        $stmt=$this->pdo->prepare("
          SELECT s.id, s.name
            FROM skills s
            JOIN user_skills us ON us.skill_id = s.id
            WHERE us.user_id = ?
            ORDER BY s.name
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    }
    //recuperer experiences d'un user 
    public function findExperiences (int $userId): array|false{
        $stmt=$this->pdo->prepare("
         SELECT id, entreprise, experience_type,
          date_debut, date_fin, description, lien
         FROM experience
         WHERE user_id = ?
         ORDER BY date_debut DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    //recuperer les projets d'un user 
    public function findProjects(int $userId): array|false {
        $stmt = $this->pdo->prepare("
            SELECT id, title, description, lien, date_debut, date_fin
            FROM projects
            WHERE user_id = ?
            ORDER BY date_debut DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
     //recuperer les achievements  d'un user 
    public function findAchievements(int $userId): array|false {
        $stmt = $this->pdo->prepare("
            SELECT id, title, issuer, achievement_type, date_obtained, description, lien
            FROM achievements
            WHERE user_id = ?
            ORDER BY date_obtained DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // recupere les recommendations sur un user 
    public function findRecommendations(int $userId): array|false {
        $stmt = $this->pdo->prepare("
            SELECT r.id,r.texte,r.created_at,
                i.nom       AS author_nom,
                i.prenom    AS author_prenom,
                u.avatar_url AS author_avatar,
                u.id        AS author_id
            FROM recommandations r
            JOIN users u  ON u.id = r.from_user
            JOIN insatien i ON i.id = u.insatien_id
            WHERE r.to_user = ?
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // update skills( remplacer les skills)
    public function syncSkills(int $userId, array $skillNames): void {
        // Supprimer les anciens
        $this->pdo->prepare("DELETE FROM user_skills WHERE user_id = ?")
                 ->execute([$userId]);
 
        foreach ($skillNames as $name) {
            $name = trim($name);
            if (!$name) continue;
 
            // Inserer le skill s'il n'existe pas encore
            $this->pdo->prepare("INSERT IGNORE INTO skills (name) VALUES (?)")
                     ->execute([$name]);
 
            // Récupérer son id
            $stmt = $this->pdo->prepare("SELECT id FROM skills WHERE name = ?");
            $stmt->execute([$name]);
            $skillId = $stmt->fetchColumn();
 
            // Lier au user
            $this->pdo->prepare("INSERT INTO user_skills (user_id, skill_id) VALUES (?, ?)")
                     ->execute([$userId, $skillId]);
        }
    }

    public function syncExperiences(int $userId, array $experiences): void {
        $this->pdo->prepare("DELETE FROM experience WHERE user_id = ?")
                 ->execute([$userId]);
 
        $stmt = $this->pdo->prepare("
            INSERT INTO experience (user_id, entreprise, experience_type, date_debut, date_fin, description, lien)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
 
        foreach ($experiences as $exp) {
            $type = $exp['experience_type'] ?? 'job';
            $type = in_array($type, ['skill', 'job', 'certification'], true) ? $type : 'job';
            $stmt->execute([
                $userId,
                $exp['entreprise']      ?? null,
                $type,
                $exp['date_debut']      ?: null,
                $exp['date_fin']        ?: null,
                $exp['description']     ?? null,
                $exp['lien']            ?? null,
            ]);
        }
    }

    public function syncProjects(int $userId, array $projects): void {
        $this->pdo->prepare("DELETE FROM projects WHERE user_id = ?")
                 ->execute([$userId]);
 
        $stmt = $this->pdo->prepare("
            INSERT INTO projects (user_id, title, description, lien, date_debut, date_fin)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
 
        foreach ($projects as $proj) {
            if (empty($proj['title'])) continue;
            $stmt->execute([
                $userId,
                $proj['title']       ?? '',
                $proj['description'] ?? null,
                $proj['lien']        ?? null,
                $proj['date_debut']  ?: null,
                $proj['date_fin']    ?: null,
            ]);
        }
    }

     public function syncAchievements(int $userId, array $achievements): void {
        $this->pdo->prepare("DELETE FROM achievements WHERE user_id = ?")
                 ->execute([$userId]);
 
        $stmt = $this->pdo->prepare("
            INSERT INTO achievements (user_id, title, issuer, achievement_type, date_obtained, description, lien)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
 
        foreach ($achievements as $ach) {
            if (empty($ach['title'])) continue;
            $stmt->execute([
                $userId,
                $ach['title']            ?? '',
                $ach['issuer']           ?? null,
                $ach['achievement_type'] ?? 'other',
                $ach['date_obtained']    ?: null,
                $ach['description']      ?? null,
                $ach['lien']             ?? null,
            ]);
        }
    }

    // ajouter recommendation 
    public function addRecommendation(int $fromUser, int $toUser, string $texte): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO recommandations (from_user, to_user, texte)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$fromUser, $toUser, $texte]);
    }

    // update user 
     public function updateUser(int $userId, array $data): void {
        $stmt = $this->pdo->prepare("
            UPDATE users
            SET bio           = ?,tagline       = ?,github_link   = ?,
            linkedin_link = ?,profile_link  = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data['bio']          ?? null,
            $data['tagline']      ?? null,
            $data['github_link']  ?? null,
            $data['linkedin_link']?? null,
            $data['profile_link'] ?? null,
            $userId,
        ]);
    }
    //update table insatien 
    public function updateInsatien(int $userId, array $data): void {
        $stmt = $this->pdo->prepare("
            UPDATE insatien i
            JOIN users u ON u.insatien_id = i.id
            SET i.nom        = ?,
                i.prenom     = ?,
                i.promo_year = ?,
                i.parcours_id = ?
            WHERE u.id = ?
        ");
        $stmt->execute([
            $data['nom']        ?? '',
            $data['prenom']     ?? '',
            $data['promo_year'] ?? null,
            $data['parcours_id'] ?? null,
            $userId,
        ]);
    }
    //update avatar 
    public function updateAvatar(int $userId, string $avatarUrl): void {
        $this->pdo->prepare("UPDATE users SET avatar_url = ? WHERE id = ?")
                 ->execute([$avatarUrl, $userId]);
    }
    
    public function findPosts(int $userId): array {
    $stmt = $this->pdo->prepare("
        SELECT id, content, created_at
        FROM posts
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
}
