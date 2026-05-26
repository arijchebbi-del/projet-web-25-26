<?php
$envPath = __DIR__ . '/../.env';
$vars = is_readable($envPath) ? (parse_ini_file($envPath, false, INI_SCANNER_RAW) ?: []) : [];
foreach ($vars as $k => $v) {
    if (getenv($k) === false) {
        putenv($k . '=' . $v);
        $_ENV[$k] = $v;
    }
}

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME') ?: (getenv('DB_DATABASE') ?: 'webdb');
$username = getenv('DB_USER') ?: (getenv('DB_USERNAME') ?: 'root');
$password = getenv('DB_PASS') ?: (getenv('DB_PASSWORD') ?: '');

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur DB: " . $e->getMessage());
}
?>
