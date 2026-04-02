<?php

declare(strict_types=1);

use App\Config\App;
use App\Controllers\AuthController;
use App\Controllers\JobsController;
use App\Controllers\ProfileController;
use App\Controllers\UsersController;
use App\Http\Response;
use App\Http\SessionAuth;

if (PHP_SAPI === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (is_string($path)) {
        $fullPath = __DIR__ . $path;
        if (is_file($fullPath)) {
            return false;
        }
    }
}

require_once __DIR__ . '/../src/config/App.php';
require_once __DIR__ . '/../src/config/Database.php';
require_once __DIR__ . '/../src/http/Request.php';
require_once __DIR__ . '/../src/http/Response.php';
require_once __DIR__ . '/../src/http/SessionAuth.php';
require_once __DIR__ . '/../src/middleware/AuthRequired.php';
require_once __DIR__ . '/../src/controllers/AuthController.php';
require_once __DIR__ . '/../src/controllers/ProfileController.php';
require_once __DIR__ . '/../src/controllers/JobsController.php';
require_once __DIR__ . '/../src/controllers/UsersController.php';

App::loadEnv(dirname(__DIR__));
SessionAuth::start();

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigins = App::origins();
if ($origin !== '' && in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Vary: Origin');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = '/' . ltrim((string) $uriPath, '/');
$method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));

try {
    if ($method === 'POST' && $path === '/api/auth/register') {
        AuthController::register();
        exit;
    }

    if ($method === 'POST' && $path === '/api/auth/login') {
        AuthController::login();
        exit;
    }

    if ($method === 'POST' && $path === '/api/auth/logout') {
        AuthController::logout();
        exit;
    }

    if ($method === 'GET' && $path === '/api/auth/me') {
        AuthController::me();
        exit;
    }

    if ($method === 'POST' && $path === '/api/jobs') {
        JobsController::create();
        exit;
    }

    if ($method === 'GET' && $path === '/api/jobs/datatable') {
        JobsController::datatable();
        exit;
    }

    if ($method === 'GET' && preg_match('#^/api/jobs/(\d+)$#', $path, $matches) === 1) {
        JobsController::show((int) $matches[1]);
        exit;
    }

    if ($method === 'GET' && $path === '/api/users/datatable') {
        UsersController::datatable();
        exit;
    }

    if ($method === 'GET' && $path === '/api/profile/me') {
        ProfileController::me();
        exit;
    }

    if ($method === 'PUT' && $path === '/api/profile/me') {
        ProfileController::updateMe();
        exit;
    }

    if ($method === 'GET' && preg_match('#^/api/profile/(\d+)$#', $path, $matches) === 1) {
        ProfileController::show((int) $matches[1]);
        exit;
    }

    if ($method === 'POST' && preg_match('#^/api/profile/(\d+)/recommend$#', $path, $matches) === 1) {
        ProfileController::recommend((int) $matches[1]);
        exit;
    }

    Response::json([
        'ok' => false,
        'error' => 'NOT_FOUND',
        'message' => 'Route not found.',
        'path' => $path,
        'method' => $method,
    ], 404);
} catch (Throwable $exception) {
    Response::json([
        'ok' => false,
        'error' => 'SERVER_ERROR',
        'message' => $exception->getMessage(),
    ], 500);
}
