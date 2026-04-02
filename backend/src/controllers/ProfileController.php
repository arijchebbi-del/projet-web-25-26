<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\Database;
use App\Http\Request;
use App\Http\Response;
use App\Middleware\AuthRequired;
use PDO;
use Throwable;

final class ProfileController
{
    public static function me(): void
    {
        $userId = AuthRequired::userId();
        $profile = self::loadProfile($userId);

        if ($profile === null) {
            Response::json([
                'ok' => false,
                'error' => 'PROFILE_NOT_FOUND',
                'message' => 'Profile not found.',
            ], 404);
            return;
        }

        Response::json([
            'ok' => true,
            'data' => $profile,
        ]);
    }

    public static function show(int $id): void
    {
        $profile = self::loadProfile($id);

        if ($profile === null) {
            Response::json([
                'ok' => false,
                'error' => 'PROFILE_NOT_FOUND',
                'message' => 'Profile not found.',
            ], 404);
            return;
        }

        Response::json([
            'ok' => true,
            'data' => $profile,
        ]);
    }

    public static function recommend(int $toUserId): void
    {
        $fromUserId = AuthRequired::userId();
        if ($fromUserId === $toUserId) {
            Response::json(['ok' => false, 'error' => 'SELF_RECOMMENDATION', 'message' => 'You cannot recommend yourself.'], 400);
            return;
        }

        $body = Request::json();
        $text = trim((string) ($body['text'] ?? ''));

        if ($text === '') {
            Response::json(['ok' => false, 'error' => 'VALIDATION_ERROR', 'message' => 'Recommendation text is required.'], 422);
            return;
        }

        $pdo = Database::connection();
        
        $check = $pdo->prepare('SELECT id FROM users WHERE id = :id');
        $check->execute(['id' => $toUserId]);
        if (!$check->fetch()) {
            Response::json(['ok' => false, 'error' => 'USER_NOT_FOUND', 'message' => 'User not found.'], 404);
            return;
        }

        $stmt = $pdo->prepare('INSERT INTO recommandations (from_user, to_user, texte) VALUES (:from_user, :to_user, :texte)');
        $stmt->execute([
            'from_user' => $fromUserId,
            'to_user' => $toUserId,
            'texte' => $text,
        ]);

        Response::json([
            'ok' => true,
            'message' => 'Recommendation added successfully.'
        ], 201);
    }

    public static function updateMe(): void
    {
        $userId = AuthRequired::userId();
        $body = Request::json();

        $firstName = trim((string) ($body['firstName'] ?? ''));
        $lastName = trim((string) ($body['lastName'] ?? ''));
        $bio = trim((string) ($body['bio'] ?? ''));
        $profileLink = trim((string) ($body['profileLink'] ?? ''));
        $avatarUrl = trim((string) ($body['avatarUrl'] ?? ''));
        $promoYear = isset($body['promoYear']) && is_numeric($body['promoYear']) ? (int) $body['promoYear'] : null;

        $pdo = Database::connection();

        try {
            $pdo->beginTransaction();

            $stmtInsatien = $pdo->prepare(
                'UPDATE insatien i
                 INNER JOIN users u ON u.insatien_id = i.id
                 SET i.prenom = COALESCE(NULLIF(:prenom, ""), i.prenom),
                     i.nom = COALESCE(NULLIF(:nom, ""), i.nom),
                     i.promo_year = COALESCE(:promo_year, i.promo_year)
                 WHERE u.id = :user_id'
            );

            $stmtInsatien->execute([
                'prenom' => $firstName,
                'nom' => $lastName,
                'promo_year' => $promoYear,
                'user_id' => $userId,
            ]);

            $stmtUser = $pdo->prepare(
                'UPDATE users
                 SET bio = :bio,
                     profile_link = :profile_link,
                     avatar_url = :avatar_url,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE id = :user_id'
            );

            $stmtUser->execute([
                'bio' => $bio !== '' ? $bio : null,
                'profile_link' => $profileLink !== '' ? $profileLink : null,
                'avatar_url' => $avatarUrl !== '' ? $avatarUrl : null,
                'user_id' => $userId,
            ]);

            if (isset($body['skills']) && is_array($body['skills'])) {
                self::syncSkills($pdo, $userId, $body['skills']);
            }

            $pdo->commit();

            Response::json([
                'ok' => true,
                'message' => 'Profile updated successfully.',
                'data' => self::loadProfile($userId),
            ]);
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            Response::json([
                'ok' => false,
                'error' => 'PROFILE_UPDATE_FAILED',
                'message' => 'Unable to update profile.',
                'details' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function loadProfile(int $userId): ?array
    {
        $pdo = Database::connection();

        $stmt = $pdo->prepare(
            'SELECT u.id, u.email, u.bio, u.profile_link, u.avatar_url,
                    i.id AS insatien_id, i.prenom, i.nom, i.promo_year,
                    f.name AS filiere_name,
                    p.name AS parcours_name
             FROM users u
             INNER JOIN insatien i ON i.id = u.insatien_id
             LEFT JOIN filieres f ON f.id = i.filiere_id
             LEFT JOIN parcours p ON p.id = i.parcours_id
             WHERE u.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        $skillsStmt = $pdo->prepare(
            'SELECT s.name
             FROM user_skills us
             INNER JOIN skills s ON s.id = us.skill_id
             WHERE us.user_id = :user_id
             ORDER BY s.name'
        );
        $skillsStmt->execute(['user_id' => $userId]);
        $skills = array_map(static fn (array $entry): string => (string) $entry['name'], $skillsStmt->fetchAll());

        $recStmt = $pdo->prepare(
            'SELECT r.id, r.texte, r.created_at, u.id AS author_id, i.prenom AS author_prenom, i.nom AS author_nom, u.avatar_url AS author_avatar
             FROM recommandations r
             INNER JOIN users u ON u.id = r.from_user
             INNER JOIN insatien i ON i.id = u.insatien_id
             WHERE r.to_user = :to_user
             ORDER BY r.created_at DESC'
        );
        $recStmt->execute(['to_user' => $userId]);
        $recommendations = array_map(static fn (array $entry) => [
            'id' => (int) $entry['id'],
            'text' => $entry['texte'],
            'createdAt' => $entry['created_at'],
            'author' => [
                'id' => (int) $entry['author_id'],
                'firstName' => $entry['author_prenom'],
                'lastName' => $entry['author_nom'],
                'avatarUrl' => $entry['author_avatar']
            ]
        ], $recStmt->fetchAll());

        return [
            'id' => (int) $row['id'],
            'email' => $row['email'],
            'firstName' => $row['prenom'],
            'lastName' => $row['nom'],
            'promoYear' => $row['promo_year'] !== null ? (int) $row['promo_year'] : null,
            'filiere' => $row['filiere_name'],
            'parcours' => $row['parcours_name'],
            'bio' => $row['bio'],
            'profileLink' => $row['profile_link'],
            'avatarUrl' => $row['avatar_url'],
            'skills' => $skills,
            'recommendations' => $recommendations,
            'insatienId' => (int) $row['insatien_id'],
        ];
    }

    /**
     * @param list<mixed> $skills
     */
    private static function syncSkills(PDO $pdo, int $userId, array $skills): void
    {
        $normalized = [];
        foreach ($skills as $skill) {
            $name = trim((string) $skill);
            if ($name !== '') {
                $normalized[] = substr($name, 0, 100);
            }
        }

        $normalized = array_values(array_unique($normalized));

        $pdo->prepare('DELETE FROM user_skills WHERE user_id = :user_id')
            ->execute(['user_id' => $userId]);

        if ($normalized === []) {
            return;
        }

        $insertSkill = $pdo->prepare('INSERT INTO skills (name) VALUES (:name) ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)');
        $insertLink = $pdo->prepare('INSERT INTO user_skills (user_id, skill_id) VALUES (:user_id, :skill_id)');

        foreach ($normalized as $skillName) {
            $insertSkill->execute(['name' => $skillName]);
            $skillId = (int) $pdo->lastInsertId();
            $insertLink->execute([
                'user_id' => $userId,
                'skill_id' => $skillId,
            ]);
        }
    }
}
