<?php  
    class postRepository {
    private $pdo;

    public function __construct() {
        $this->pdo = ConnexionDB::getInstance();
    }

    public function findAllPosts() {

    $stmt = $this->pdo->prepare("
        SELECT 
            posts.*,
            users.id AS userId,
            insatien.nom,
            insatien.prenom,
            users.avatar_url
        FROM posts
        JOIN users ON posts.user_id = users.id
        JOIN insatien ON users.insatien_id = insatien.id
        ORDER BY posts.created_at DESC
    ");

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

        
}


?>