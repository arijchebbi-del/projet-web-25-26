<?php
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
}