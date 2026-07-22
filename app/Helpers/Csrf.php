<?php
declare(strict_types=1);
namespace SellSoft\Helpers;
class Csrf
{
    private const SESSION_KEY = '_csrf_token';
    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }
    public static function field(): string { return '<input type="hidden" name="_csrf" value="' . self::token() . '">'; }
    public static function verify(): bool
    {
        $submitted = isset($_POST['_csrf']) ? $_POST['_csrf'] : '';
        if (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) { $submitted = $_SERVER['HTTP_X_CSRF_TOKEN']; }
        $stored = isset($_SESSION[self::SESSION_KEY]) ? $_SESSION[self::SESSION_KEY] : '';
        if (empty($submitted) || empty($stored)) return false;
        return hash_equals($stored, $submitted);
    }
    public static function validateOrFail(): void
    {
        if (!self::verify()) {
            http_response_code(419);
            die(json_encode(['error' => 'Invalid security token. Please reload the page.']));
        }
    }
    public static function rotate(): void { $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32)); }
}
