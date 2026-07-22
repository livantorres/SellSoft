<?php
declare(strict_types=1);
namespace SellSoft\Helpers;
class Session
{
    public static function set(string $key, $value): void { $_SESSION[$key] = $value; }
    public static function get(string $key, $default = null) { return isset($_SESSION[$key]) ? $_SESSION[$key] : $default; }
    public static function has(string $key): bool { return isset($_SESSION[$key]); }
    public static function delete(string $key): void { unset($_SESSION[$key]); }
    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
    public static function regenerate(): void { session_regenerate_id(true); }
    public static function all(): array { return $_SESSION; }
    public static function isAuthenticated(): bool { return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']); }
    public static function userId(): ?int { return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null; }
    public static function warehouseId(): ?int { return isset($_SESSION['warehouse_id']) ? (int)$_SESSION['warehouse_id'] : null; }
    public static function role(): ?string { return isset($_SESSION['role_slug']) ? $_SESSION['role_slug'] : null; }
    public static function hasRole(string $roleSlug): bool { $roles = isset($_SESSION['roles']) ? $_SESSION['roles'] : []; return in_array($roleSlug, $roles, true); }
    public static function hasPermission(string $module, string $action): bool { $perms = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : []; return in_array("{$module}.{$action}", $perms, true); }
}
