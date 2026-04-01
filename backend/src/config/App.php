<?php

declare(strict_types=1);

namespace App\Config;

final class App
{
    private static bool $loaded = false;
    /** @var array<string, string> */
    private static array $env = [];

    public static function loadEnv(string $basePath): void
    {
        if (self::$loaded) {
            return;
        }

        $envPath = $basePath . DIRECTORY_SEPARATOR . '.env';
        $examplePath = $basePath . DIRECTORY_SEPARATOR . '.env.example';
        $source = is_file($envPath) ? $envPath : $examplePath;

        if (is_file($source)) {
            $lines = file($source, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (is_array($lines)) {
                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                        continue;
                    }

                    $parts = explode('=', $trimmed, 2);
                    if (count($parts) !== 2) {
                        continue;
                    }

                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
                    self::$env[$key] = $value;
                }
            }
        }

        self::$loaded = true;
    }

    public static function get(string $key, string $default = ''): string
    {
        if (array_key_exists($key, self::$env)) {
            return self::$env[$key];
        }

        $fromSystem = getenv($key);
        return $fromSystem !== false ? (string) $fromSystem : $default;
    }

    /**
     * @return list<string>
     */
    public static function origins(): array
    {
        $raw = self::get('APP_ORIGINS', 'http://127.0.0.1:5500,http://localhost:5500');
        $origins = array_map('trim', explode(',', $raw));
        return array_values(array_filter($origins, static fn (string $o): bool => $o !== ''));
    }
}
