<?php

class JobService {

    public function buildQuery($filters) {

        $sql = "SELECT * FROM jobs WHERE 1=1";
        $params = [];

        // Titre
        if (!empty($filters['title'])) {
            $sql .= " AND titre LIKE ?";
            $params[] = "%" . $filters['title'] . "%";
        }

        // Country
        if (!empty($filters['country'])) {
            $sql .= " AND country = ?";
            $params[] = $filters['country'];
        }

        // City
        if (!empty($filters['city'])) {
            $sql .= " AND city = ?";
            $params[] = $filters['city'];
        }

        // TYPE (checkbox)
        if (!empty($filters['type'])) {
            $placeholders = implode(',', array_fill(0, count($filters['type']), '?'));
            $sql .= " AND job_type IN ($placeholders)";
            $params = array_merge($params, $filters['type']);
        }

        // REMOTE
        if (!empty($filters['remote'])) {
            $sql .= " AND mode = 'remote'";
        }

        // EXPERIENCE
        if (!empty($filters['experience'])) {
            $placeholders = implode(',', array_fill(0, count($filters['experience']), '?'));
            $sql .= " AND experience IN ($placeholders)";
            $params = array_merge($params, $filters['experience']);
        }

        // SALARY
        if (!empty($filters['salary'])) {
            $sql .= " AND salary <= ?";
            $params[] = $filters['salary'];
        }

        $sql .= " ORDER BY date_publication DESC";

        return [$sql, $params];
    }
}