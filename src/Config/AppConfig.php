<?php

namespace App\Config;

class AppConfig
{
    private static array $settings = [];

    public static function load(): void
    {
        $envPath = dirname(__DIR__, 2) . '/.env';
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (str_starts_with(trim($line), '#')) continue;
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    self::$settings[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
                }
            }
        }
    }

    public static function get(string $key, $default = null)
    {
        return self::$settings[$key] ?? $default;
    }
}