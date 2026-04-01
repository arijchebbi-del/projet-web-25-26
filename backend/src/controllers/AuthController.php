<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\Database;
use App\Http\Request;
use App\Http\Response;
use App\Http\SessionAuth;
use PDO;
use Throwable;

final class AuthController
{
    public static function register(): void
    {
        $body = Request::json();

        $firstName = trim((string) ($body['firstName'] ?? ''));
        $lastName = trim((string) ($body['lastName'] ?? ''));
        $email = trim((string) ($body['email'] ?? ''));
        $password = (string) ($body['password'] ?? '');
        $promoYear = isset($body['promoYear']) && is_numeric($body['promoYear']) ? (int) $body['promoYear'] : null;

        if ($firstName === '' || $lastName === '' || $email === '' || $password === '') {
            Response::json([
                'ok' => false,
                'error' => 'VALIDATION_ERROR',
                'message' => 'First name, last name, email, and password are required.',
            ], 422);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::json([
                'ok' => false,
                'error' => 'INVALID_EMAIL',
                'message' => 'Email format is invalid.',
            ], 422);
            return;
        }

        if (strlen($password) < 6) {
            Response::json([
                'ok' => false,
                'error' => 'WEAK_PASSWORD',
                'message' => 'Password must contain at least 6 characters.',
            ], 422);
            return;
        }

        $pdo = Database::connection();

        $exists = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $exists->execute(['email' => $email]);
        if ($exists->fetch()) {
            Response::json([
                'ok' => false,
                'error' => 'EMAIL_EXISTS',
                'message' => 'An account already exists for this email.',
            ], 409);
            return;
        }

        try {
            $pdo->beginTransaction();

            $insertInsatien = $pdo->prepare(
                'INSERT INTO insatien (nom, prenom, email, promo_year) VALUES (:nom, :prenom, :email, :promo_year)'
            );
            $insertInsatien->execute([
                'nom' => $lastName,
                'prenom' => $firstName,
                'email' => $email,
                'promo_year' => $promoYear,
            ]);

            $insatienId = (int) $pdo->lastInsertId();
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $insertUser = $pdo->prepare(
                'INSERT INTO users (email, password_hash, insatien_id) VALUES (:email, :password_hash, :insatien_id)'
            );
            $insertUser->execute([
                'email' => $email,
                'password_hash' => $hash,
                'insatien_id' => $insatienId,
            ]);

            $userId = (int) $pdo->lastInsertId();
            $pdo->commit();

            SessionAuth::login($userId);

            Response::json([
                'ok' => true,
                'message' => 'Account created successfully.',
                'data' => self::loadUserPayload($pdo, $userId),
            ], 201);
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            Response::json([
                'ok' => false,
                'error' => 'REGISTER_FAILED',
                'message' => 'Unable to create account.',
                'details' => $exception->getMessage(),
            ], 500);
        }
    }

    public static function login(): void
    {
        $body = Request::json();
        $email = trim((string) ($body['email'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        if ($email === '' || $password === '') {
            Response::json([
                'ok' => false,
                'error' => 'VALIDATION_ERROR',
                'message' => 'Email and password are required.',
            ], 422);
            return;
        }

        $pdo = Database::connection();
        $stmt = $pdo->prepare(
            'SELECT id, email, password_hash FROM users WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, (string) $user['password_hash'])) {
            Response::json([
                'ok' => false,
                'error' => 'INVALID_CREDENTIALS',
                'message' => 'Invalid email or password.',
            ], 401);
            return;
        }

        $userId = (int) $user['id'];
        SessionAuth::login($userId);

        Response::json([
            'ok' => true,
            'message' => 'Logged in successfully.',
            'data' => self::loadUserPayload($pdo, $userId),
        ]);
    }

    public static function me(): void
    {
        $userId = SessionAuth::userId();
        if ($userId === null) {
            Response::json([
                'ok' => false,
                'error' => 'AUTH_REQUIRED',
                'message' => 'No active session.',
            ], 401);
            return;
        }

        $pdo = Database::connection();
        $user = self::loadUserPayload($pdo, $userId);

        if ($user === null) {
            Response::json([
                'ok' => false,
                'error' => 'USER_NOT_FOUND',
                'message' => 'User not found for active session.',
            ], 404);
            return;
        }

        Response::json([
            'ok' => true,
            'data' => $user,
        ]);
    }

    public static function logout(): void
    {
        SessionAuth::logout();

        Response::json([
            'ok' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function loadUserPayload(PDO $pdo, int $userId): ?array
    {
        $stmt = $pdo->prepare(
            'SELECT u.id, u.email, u.profile_link, u.bio, u.avatar_url,
                    i.id AS insatien_id, i.nom, i.prenom, i.promo_year
             FROM users u
             INNER JOIN insatien i ON i.id = u.insatien_id
             WHERE u.id = :id
             LIMIT 1'
        );

        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }

        return [
            'id' => (int) $row['id'],
            'email' => $row['email'],
            'firstName' => $row['prenom'],
            'lastName' => $row['nom'],
            'promoYear' => $row['promo_year'] !== null ? (int) $row['promo_year'] : null,
            'profileLink' => $row['profile_link'],
            'bio' => $row['bio'],
            'avatarUrl' => $row['avatar_url'],
            'insatienId' => (int) $row['insatien_id'],
        ];
    }
}
