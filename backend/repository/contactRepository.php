<?php
    class contactRepository {

    private PDO $pdo;

    public function __construct() {
        require_once __DIR__ . '/../../backend/config/ConnexionDB.php';
        require_once __DIR__ . '/../../backend/service/contactService.php';
        $this ->pdo = ConnexionDB::getInstance();;
    }

    public function saveData(array $data): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO contact_messages (first_name, last_name, email, topic, message)
            VALUES (:first_name, :last_name, :email, :topic, :message)
        ");

        return $stmt->execute([
            ':first_name' => $data['first_name'],
            ':last_name'  => $data['last_name'],
            ':email'      => $data['email'],
            ':topic'      => $data['topic'] ?? null,
            ':message'    => $data['message'],
        ]);
    }
}
?>-.*