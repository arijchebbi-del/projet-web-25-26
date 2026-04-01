<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\Response;
use App\Http\SessionAuth;

final class AuthRequired
{
    public static function userId(): int
    {
        $userId = SessionAuth::userId();
        if ($userId === null) {
            Response::json([
                'ok' => false,
                'error' => 'AUTH_REQUIRED',
                'message' => 'Authentication required.',
            ], 401);
            exit;
        }

        return $userId;
    }
}
