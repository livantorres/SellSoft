<?php
/**
 * SellSoft - Application Configuration
 * Regional: Colombia, COP, IVA 19%, America/Bogota
 */
declare(strict_types=1);

date_default_timezone_set('America/Bogota');

// PHP 7.x polyfills
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool {
        return strpos($haystack, $needle) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool {
        return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
    }
}

// Load .env
$dotEnvFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . '.env';
if (file_exists($dotEnvFile)) {
    $lines = file($dotEnvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;
        list($key, $value) = array_map('trim', explode('=', $line, 2));
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
}

function env(string $key, $default = null)
{
    return isset($_ENV[$key]) ? $_ENV[$key] : (getenv($key) ?: $default);
}

define('APP_ENV',    env('APP_ENV',    'development'));
define('APP_DEBUG',  filter_var(env('APP_DEBUG', true), FILTER_VALIDATE_BOOLEAN));
define('APP_URL',    rtrim(env('APP_URL', 'http://sellsoft.test'), '/'));
define('APP_NAME',   env('APP_NAME',   'SellSoft'));

define('BASE_PATH',      dirname(__DIR__));
define('APP_PATH',       BASE_PATH . '/app');
define('CORE_PATH',      BASE_PATH . '/core');
define('CONFIG_PATH',    BASE_PATH . '/config');
define('RESOURCES_PATH', BASE_PATH . '/resources');
define('VIEWS_PATH',     RESOURCES_PATH . '/views');
define('PUBLIC_PATH',    BASE_PATH . '/public');
define('STORAGE_PATH',   BASE_PATH . '/storage');
define('ASSETS_URL',     APP_URL . '/assets');

define('DB_HOST',    env('DB_HOST',    '127.0.0.1'));
define('DB_PORT',    (int) env('DB_PORT',    3306));
define('DB_NAME',    env('DB_NAME',    'sellsoft_db'));
define('DB_USER',    env('DB_USER',    'root'));
define('DB_PASS',    env('DB_PASS',    ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

define('SESSION_NAME', env('SESSION_NAME', 'sellsoft_sess'));
define('SECRET_KEY',   env('SECRET_KEY',   'sellsoft_secret_key_32_chars____'));

define('TIMEZONE',        'America/Bogota');
define('CURRENCY_CODE',   'COP');
define('CURRENCY_SYMBOL', '$');
define('TAX_RATE',        19.0);
define('LOCALE',          'es_CO');

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', STORAGE_PATH . '/logs/app.log');
}

ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_samesite', 'Lax');
session_name(SESSION_NAME);
