<?php

class ConnexionDB
{
    private static ?PDO $instance = null;
    private static bool $envLoaded = false;

    private static function loadEnv(): void
    {
        if (self::$envLoaded) return;
        $envPath = __DIR__ . '/../.env';
        $vars = is_readable($envPath) ? (parse_ini_file($envPath, false, INI_SCANNER_RAW) ?: []) : [];
        foreach ($vars as $k => $v) {
            if (getenv($k) === false) {
                putenv($k . '=' . $v);
                $_ENV[$k] = $v;
            }
        }
        self::$envLoaded = true;
    }

    private static function env(string $key, string $default = ''): string
    {
        $val = getenv($key);
        return $val !== false ? $val : $default;
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {

            self::loadEnv();

            $host = self::env('DB_HOST', 'localhost');
            $port = self::env('DB_PORT', '3306');
            $dbname = self::env('DB_NAME', self::env('DB_DATABASE', 'webdb'));
            $username = self::env('DB_USER', self::env('DB_USERNAME', 'root'));
            $password = self::env('DB_PASS', self::env('DB_PASSWORD', ''));

            self::$instance = new PDO(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8",
                $username,
                $password
            );

            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$instance;
    }
}