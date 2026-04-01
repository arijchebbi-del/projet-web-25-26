<?php

declare(strict_types=1);

namespace App\Http;

use App\Config\App;

final class SessionAuth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $cookieName = App::get('SESSION_COOKIE_NAME', 'metiers_sid');
        $lifeTime = (int) App::get('SESSION_LIFETIME', '7200');

        session_name($cookieName);
        session_set_cookie_params([
            'lifetime' => $lifeTime,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }

    public static function login(int $userId): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
    }

    public static function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
        }

        session_destroy();
    }

    public static function userId(): ?int
    {
        $raw = $_SESSION['user_id'] ?? null;
        return is_numeric($raw) ? (int) $raw : null;
    }
}
