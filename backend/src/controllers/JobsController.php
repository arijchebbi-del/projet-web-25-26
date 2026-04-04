<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\Database;
use App\Http\Request;
use App\Http\Response;
use App\Http\SessionAuth;
use PDO;

final class JobsController
{
    public static function create(): void
    {
        $userId = SessionAuth::userId();
        if ($userId === null) {
            Response::json(['ok' => false, 'error' => 'AUTH_REQUIRED', 'message' => 'No active session.'], 401);
            return;
        }

        $body = Request::json();
        $title = substr(trim((string) ($body['title'] ?? '')), 0, 255);
        $company = substr(trim((string) ($body['company'] ?? '')), 0, 255);
        $type = trim((string) ($body['type'] ?? 'full-time'));
        $remote = (bool) ($body['remote'] ?? false);
        $location = substr(trim((string) ($body['location'] ?? '')), 0, 255);
        $salaryMin = isset($body['salaryMin']) ? (float) $body['salaryMin'] : null;
        $salaryMax = isset($body['salaryMax']) ? (float) $body['salaryMax'] : null;
        $currency = trim((string) ($body['currency'] ?? 'TND'));
        $experience = isset($body['experienceYears']) ? (int) $body['experienceYears'] : null;
        $description = substr(trim((string) ($body['description'] ?? '')), 0, 60000);

        if ($title === '') {
            Response::json(['ok' => false, 'error' => 'VALIDATION_ERROR', 'message' => 'Title is required.'], 422);
            return;
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'INSERT INTO jobs (titre, entreprise, type, remote, localisation, salary_min, salary_max, currency, req_experience, description, created_by)
             VALUES (:title, :company, :type, :remote, :location, :salary_min, :salary_max, :currency, :req_experience, :description, :created_by)'
        );
        $stmt->execute([
            'title' => $title,
            'company' => $company,
            'type' => $type,
            'remote' => $remote ? 1 : 0,
            'location' => $location,
            'salary_min' => $salaryMin,
            'salary_max' => $salaryMax,
            'currency' => $currency ?: 'TND',
            'req_experience' => $experience,
            'description' => $description,
            'created_by' => $userId,
        ]);

        Response::json([
            'ok' => true,
            'message' => 'Job created successfully.',
            'id' => (int) $pdo->lastInsertId(),
        ], 201);
    }

    public static function datatable(): void
    {
        $pdo = Database::connection();

        $draw = (int) (Request::query('draw', 1));
        $start = max((int) (Request::query('start', 0)), 0);
        $length = (int) (Request::query('length', 10));
        $length = $length > 0 ? min($length, 100) : 10;

        $search = Request::query('search', []);
        $searchValue = '';
        if (is_array($search) && isset($search['value'])) {
            $searchValue = trim((string) $search['value']);
        }

        $where = [];
        $params = [];

        if ($searchValue !== '') {
            $where[] = '(j.titre LIKE :search OR j.entreprise LIKE :search OR j.localisation LIKE :search)';
            $params['search'] = '%' . $searchValue . '%';
        }

        $title = trim((string) Request::query('title', ''));
        if ($title !== '') {
            $where[] = 'j.titre LIKE :title';
            $params['title'] = '%' . $title . '%';
        }

        $jobType = trim((string) Request::query('jobType', ''));
        if ($jobType !== '') {
            $types = array_values(array_filter(array_map('trim', explode(',', $jobType))));
            if ($types !== []) {
                $placeholders = [];
                foreach ($types as $index => $type) {
                    $key = 'job_type_' . $index;
                    $placeholders[] = ':' . $key;
                    $params[$key] = $type;
                }
                $where[] = 'j.type IN (' . implode(', ', $placeholders) . ')';
            }
        }

        $remote = Request::query('remote', '');
        if ($remote === 'true' || $remote === '1') {
            $where[] = 'j.remote = 1';
        }

        $maxSalary = Request::query('maxSalary', null);
        if (is_numeric($maxSalary)) {
            $where[] = '(j.salary_min <= :max_salary OR j.salary_min IS NULL)';
            $params['max_salary'] = (float) $maxSalary;
        }

        $country = Request::query('country', null);
        if (is_numeric($country)) {
            $where[] = 'j.country_id = :country_id';
            $params['country_id'] = (int) $country;
        }

        $city = Request::query('city', null);
        if (is_numeric($city)) {
            $where[] = 'j.city_id = :city_id';
            $params['city_id'] = (int) $city;
        }

        $experience = trim((string) Request::query('experience', ''));
        if ($experience !== '') {
            $levels = array_values(array_filter(array_map('trim', explode(',', $experience))));
            $expWhere = [];
            foreach ($levels as $level) {
                if ($level === 'exp1') {
                    $expWhere[] = 'j.req_experience < 1';
                } elseif ($level === 'exp2') {
                    $expWhere[] = '(j.req_experience >= 1 AND j.req_experience < 3)';
                } elseif ($level === 'exp3') {
                    $expWhere[] = '(j.req_experience >= 3 AND j.req_experience < 5)';
                } elseif ($level === 'exp4') {
                    $expWhere[] = 'j.req_experience >= 5';
                }
            }

            if ($expWhere !== []) {
                $where[] = '(' . implode(' OR ', $expWhere) . ')';
            }
        }

        $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);

        $recordsTotal = (int) $pdo->query('SELECT COUNT(*) FROM jobs')->fetchColumn();

        $countSql = 'SELECT COUNT(*) FROM jobs j' . $whereSql;
        $countStmt = $pdo->prepare($countSql);
        foreach ($params as $name => $value) {
            $countStmt->bindValue(':' . $name, $value);
        }
        $countStmt->execute();
        $recordsFiltered = (int) $countStmt->fetchColumn();

        $order = Request::query('order', []);
        $orderColumn = 'j.date_publication';
        $orderDir = 'DESC';
        $columnMap = [
            0 => 'j.titre',
            1 => 'j.entreprise',
            2 => 'j.type',
            3 => 'j.remote',
            4 => 'j.localisation',
            5 => 'j.salary_min',
            6 => 'j.req_experience',
            7 => 'j.date_publication',
        ];

        if (is_array($order) && isset($order[0]) && is_array($order[0])) {
            $index = (int) ($order[0]['column'] ?? 7);
            $dir = strtolower((string) ($order[0]['dir'] ?? 'desc'));
            if (isset($columnMap[$index])) {
                $orderColumn = $columnMap[$index];
            }
            if (in_array($dir, ['asc', 'desc'], true)) {
                $orderDir = strtoupper($dir);
            }
        }

        $sql = 'SELECT j.id, j.titre, j.entreprise, j.type, j.remote, j.localisation,
                       j.salary_min, j.salary_max, j.currency, j.req_experience, j.date_publication
                FROM jobs j'
            . $whereSql
            . " ORDER BY {$orderColumn} {$orderDir} LIMIT :start, :length";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $name => $value) {
            $stmt->bindValue(':' . $name, $value);
        }
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':length', $length, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();

        $data = array_map(static function (array $row): array {
            return [
                'id' => (int) $row['id'],
                'title' => $row['titre'],
                'company' => $row['entreprise'],
                'type' => $row['type'],
                'remote' => (bool) $row['remote'],
                'location' => $row['localisation'],
                'salaryMin' => $row['salary_min'] !== null ? (float) $row['salary_min'] : null,
                'salaryMax' => $row['salary_max'] !== null ? (float) $row['salary_max'] : null,
                'currency' => $row['currency'],
                'experienceYears' => $row['req_experience'] !== null ? (int) $row['req_experience'] : null,
                'publishedAt' => $row['date_publication'],
            ];
        }, $rows);

        Response::json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public static function show(int $jobId): void
    {
        $pdo = Database::connection();

        $stmt = $pdo->prepare(
            'SELECT j.id, j.titre, j.entreprise, j.type, j.remote, j.localisation,
                    j.salary_min, j.salary_max, j.currency, j.req_experience,
                    j.description, j.requirements, j.responsibilities,
                    j.date_publication,
                    c.name AS country_name,
                    ci.name AS city_name
             FROM jobs j
             LEFT JOIN countries c ON c.id = j.country_id
             LEFT JOIN cities ci ON ci.id = j.city_id
             WHERE j.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $jobId]);
        $row = $stmt->fetch();

        if (!$row) {
            Response::json([
                'ok' => false,
                'error' => 'JOB_NOT_FOUND',
                'message' => 'Job not found.',
            ], 404);
            return;
        }

        Response::json([
            'ok' => true,
            'data' => [
                'id' => (int) $row['id'],
                'title' => $row['titre'],
                'company' => $row['entreprise'],
                'type' => $row['type'],
                'remote' => (bool) $row['remote'],
                'location' => $row['localisation'],
                'country' => $row['country_name'],
                'city' => $row['city_name'],
                'salaryMin' => $row['salary_min'] !== null ? (float) $row['salary_min'] : null,
                'salaryMax' => $row['salary_max'] !== null ? (float) $row['salary_max'] : null,
                'currency' => $row['currency'],
                'experienceYears' => $row['req_experience'] !== null ? (int) $row['req_experience'] : null,
                'description' => $row['description'],
                'requirements' => $row['requirements'],
                'responsibilities' => $row['responsibilities'],
                'publishedAt' => $row['date_publication'],
            ],
        ]);
    }
}


