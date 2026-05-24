<?php  
    class jobRepository {
    private $pdo;

    public function __construct() {
        $this->pdo = ConnexionDB::getInstance();
    }

    public function findAllJobs() {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM jobs
            WHERE job_type IN ('full-time', 'part-time')
            ORDER BY date_publication DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function findInternships() {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM jobs
            WHERE job_type = 'internship'
            ORDER BY date_publication DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
?>