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
        $stmt = $this->pdo->prepare("\
            SELECT jobs.*, countries.name AS country_name, cities.name AS city_name
            FROM jobs
            LEFT JOIN countries ON jobs.country_id = countries.id
            LEFT JOIN cities ON jobs.city_id = cities.id
            WHERE jobs.id = :id
        ");
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
}

    public function createJob(array $data): void {
        $currency = strtoupper(trim($data['currency'] ?? ''));
        $currency = $currency !== '' ? preg_replace('/[^A-Z]/', '', $currency) : '';
        $currency = $currency !== '' ? substr($currency, 0, 3) : 'TND';

        $stmt = $this->pdo->prepare("
            INSERT INTO jobs (
                titre, entreprise, job_type, job_mode,
                description, application_link, company_link,
                contact_email, requirements, responsibilities,
                salary_min, salary_max, currency,
                req_experience, country_id, city_id,
                deadline, created_by
            ) VALUES (
                :titre, :entreprise, :job_type, :job_mode,
                :description, :application_link, :company_link,
                :contact_email, :requirements, :responsibilities,
                :salary_min, :salary_max, :currency,
                :req_experience, :country_id, :city_id,
                :deadline, :created_by
            )
        ");

        $stmt->execute([
            ':titre' => $data['title'],
            ':entreprise' => $data['company'] ?: null,
            ':job_type' => $data['job_type'],
            ':job_mode' => $data['job_mode'],
            ':description' => $data['description'] ?: null,
            ':application_link' => $data['application_link'] ?: null,
            ':company_link' => $data['company_link'] ?: null,
            ':contact_email' => $data['contact_email'],
            ':requirements' => $data['requirements'] ?: null,
            ':responsibilities' => $data['responsibilities'] ?: null,
            ':salary_min' => $data['salary_min'] !== '' ? $data['salary_min'] : null,
            ':salary_max' => $data['salary_max'] !== '' ? $data['salary_max'] : null,
            ':currency' => $currency,
            ':req_experience' => $data['req_experience'] !== '' ? $data['req_experience'] : null,
            ':country_id' => $data['country_id'] !== '' ? $data['country_id'] : null,
            ':city_id' => $data['city_id'] !== '' ? $data['city_id'] : null,
            ':deadline' => $data['deadline'] ?: null,
            ':created_by' => $data['created_by'],
        ]);
    }
}


?>