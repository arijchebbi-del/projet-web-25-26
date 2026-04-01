<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\Database;
use App\Http\Request;
use App\Http\Response;
use PDO;

final class UsersController
{
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

        $q = trim((string) Request::query('q', ''));
        $filterBy = trim((string) Request::query('filterBy', ''));
        $filterValue = trim((string) Request::query('filterValue', ''));

        $where = [];
        $params = [];

        if ($searchValue !== '') {
            $where[] = '(i.prenom LIKE :search OR i.nom LIKE :search OR u.email LIKE :search)';
            $params['search'] = '%' . $searchValue . '%';
        }

        if ($q !== '') {
            $where[] = '(i.prenom LIKE :q OR i.nom LIKE :q OR u.email LIKE :q)';
            $params['q'] = '%' . $q . '%';
        }

        if ($filterBy !== '' && $filterValue !== '') {
            if ($filterBy === 'promo') {
                $where[] = 'CAST(i.promo_year AS CHAR) = :filter_value';
                $params['filter_value'] = $filterValue;
            } elseif ($filterBy === 'filiere') {
                $where[] = 'f.name LIKE :filter_value';
                $params['filter_value'] = '%' . $filterValue . '%';
            } elseif ($filterBy === 'parcours') {
                $where[] = 'p.name LIKE :filter_value';
                $params['filter_value'] = '%' . $filterValue . '%';
            } elseif ($filterBy === 'skills') {
                $where[] = 'EXISTS (
                    SELECT 1
                    FROM user_skills us2
                    INNER JOIN skills s2 ON s2.id = us2.skill_id
                    WHERE us2.user_id = u.id AND s2.name LIKE :filter_value
                )';
                $params['filter_value'] = '%' . $filterValue . '%';
            }
        }

        $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);

        $totalSql = 'SELECT COUNT(*) FROM users';
        $recordsTotal = (int) $pdo->query($totalSql)->fetchColumn();

        $countSql = 'SELECT COUNT(DISTINCT u.id)
                     FROM users u
                     INNER JOIN insatien i ON i.id = u.insatien_id
                     LEFT JOIN filieres f ON f.id = i.filiere_id
                     LEFT JOIN parcours p ON p.id = i.parcours_id'
                    . $whereSql;

        $countStmt = $pdo->prepare($countSql);
        foreach ($params as $name => $value) {
            $countStmt->bindValue(':' . $name, $value);
        }
        $countStmt->execute();
        $recordsFiltered = (int) $countStmt->fetchColumn();

        $order = Request::query('order', []);
        $columnMap = [
            0 => 'i.prenom',
            1 => 'i.nom',
            2 => 'i.promo_year',
            3 => 'f.name',
            4 => 'p.name',
            5 => 'u.email',
        ];
        $orderColumn = 'i.prenom';
        $orderDir = 'ASC';

        if (is_array($order) && isset($order[0]) && is_array($order[0])) {
            $index = (int) ($order[0]['column'] ?? 0);
            $dir = strtolower((string) ($order[0]['dir'] ?? 'asc'));
            if (isset($columnMap[$index])) {
                $orderColumn = $columnMap[$index];
            }
            if (in_array($dir, ['asc', 'desc'], true)) {
                $orderDir = strtoupper($dir);
            }
        }

        $dataSql = 'SELECT u.id,
                           i.prenom,
                           i.nom,
                           i.promo_year,
                           f.name AS filiere,
                           p.name AS parcours,
                           u.email,
                           u.avatar_url,
                           COALESCE(GROUP_CONCAT(DISTINCT s.name ORDER BY s.name SEPARATOR ", "), "") AS skills
                    FROM users u
                    INNER JOIN insatien i ON i.id = u.insatien_id
                    LEFT JOIN filieres f ON f.id = i.filiere_id
                    LEFT JOIN parcours p ON p.id = i.parcours_id
                    LEFT JOIN user_skills us ON us.user_id = u.id
                    LEFT JOIN skills s ON s.id = us.skill_id'
                    . $whereSql .
                    ' GROUP BY u.id, i.prenom, i.nom, i.promo_year, f.name, p.name, u.email, u.avatar_url
                      ORDER BY ' . $orderColumn . ' ' . $orderDir . '
                      LIMIT :start, :length';

        $stmt = $pdo->prepare($dataSql);
        foreach ($params as $name => $value) {
            $stmt->bindValue(':' . $name, $value);
        }
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':length', $length, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll();

        $data = array_map(static function (array $row): array {
            $skills = [];
            if (!empty($row['skills'])) {
                $skills = array_values(array_filter(array_map('trim', explode(',', (string) $row['skills']))));
            }

            return [
                'id' => (int) $row['id'],
                'firstName' => $row['prenom'],
                'lastName' => $row['nom'],
                'promoYear' => $row['promo_year'] !== null ? (int) $row['promo_year'] : null,
                'filiere' => $row['filiere'],
                'parcours' => $row['parcours'],
                'email' => $row['email'],
                'avatarUrl' => $row['avatar_url'],
                'skills' => $skills,
            ];
        }, $rows);

        Response::json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }
}
