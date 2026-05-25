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
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM jobs WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
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
    public function findFiltered($title, $country, $city, $jobType, $remote, $salary,$onsite,$hybrid)
{
    $sql = "SELECT * FROM jobs WHERE 1=1";
    $params = [];

    if (!empty($title)) {
        $sql .= " AND titre LIKE :title";
        $params['title'] = "%$title%";
    }

    if (!empty($country)) {
    $sql .= " AND country_id = :country";
    $params['country'] = $country;
}

    if (!empty($city)) {
    $sql .= " AND city_id = :city";
    $params['city'] = $city;
}

    if (!empty($jobType)) {
        $sql .= " AND job_type = :job_type";
        $params['job_type'] = $jobType;
    }

    if ($remote === "1") {
        $sql .= " AND job_mode = 'remote'";
    }
    if ($onsite === "1") {
        $sql .= " AND job_mode = 'onsite'";
    }
    if ($hybrid === "1") {
        $sql .= " AND job_mode = 'hybrid'";
    }
    if (!empty($salary)) {
        $sql .= " AND salary_max >= :salary";
        $params['salary'] = $salary;
    }

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_OBJ);
}}


?>