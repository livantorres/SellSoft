<?php
declare(strict_types=1);
namespace SellSoft\Services;
use SellSoft\Models\User;
use SellSoft\Models\Setting;
use SellSoft\Helpers\Session;
class AuthService
{
    private const COOKIE_NAME = 'ss_remember';
    private const COOKIE_DAYS = 30;
    private $userModel;
    public function __construct() { $this->userModel = new User(); }
    public function login(string $email, string $password, bool $remember = false): array
    {
        $user = $this->userModel->findByEmail($email);
        if (!$user) return ['success' => false, 'message' => 'Email or password is incorrect.'];
        if (!(bool)$user['activo']) return ['success' => false, 'message' => 'Your account is disabled. Contact an administrator.'];
        if (!password_verify($password, $user['clave'])) return ['success' => false, 'message' => 'Email or password is incorrect.'];
        $access = $this->userModel->getRolesAndPermissions((int)$user['id']);
        Session::regenerate();
        Session::set('user_id',       (int)$user['id']);
        Session::set('user_name',     $user['nombre']);
        Session::set('user_email',    $user['correo']);
        Session::set('warehouse_id',  (int)($user['bodega_id'] ?? 1));
        Session::set('warehouse_name',$user['bodega_nombre'] ?? 'Sede Principal');
        Session::set('roles',         $access['roles']);
        Session::set('permissions',   $access['permissions']);
        Session::set('is_admin',      in_array('administrador', $access['roles']));
        $this->userModel->updateLastLogin((int)$user['id']);
        if ($remember) $this->setRememberCookie((int)$user['id']);
        return ['success' => true, 'message' => 'Welcome, ' . $user['nombre'] . '!'];
    }
    public function logout(): void { $this->deleteRememberCookie(); Session::destroy(); }
    public function hasPermission(string $module, string $action): bool { return Session::hasPermission($module, $action); }
    public function verifyPin(string $pin): bool
    {
        $setting = new Setting();
        $hash = $setting->get('pin_reversion', '');
        if (empty($hash)) return false;
        return password_verify($pin, $hash);
    }
    public function restoreFromCookie(): bool
    {
        if (!isset($_COOKIE[self::COOKIE_NAME])) return false;
        $token = $_COOKIE[self::COOKIE_NAME];
        $user  = $this->userModel->db->fetchOne(
            'SELECT u.*, b.nombre AS bodega_nombre FROM usuarios u LEFT JOIN bodegas b ON b.id = u.bodega_id WHERE u.recuerdo_token = ? AND u.activo = 1',
            [$token]
        );
        if (!$user) { $this->deleteRememberCookie(); return false; }
        $access = $this->userModel->getRolesAndPermissions((int)$user['id']);
        Session::set('user_id',       (int)$user['id']);
        Session::set('user_name',     $user['nombre']);
        Session::set('user_email',    $user['correo']);
        Session::set('warehouse_id',  (int)($user['bodega_id'] ?? 1));
        Session::set('warehouse_name',$user['bodega_nombre'] ?? 'Sede Principal');
        Session::set('roles',         $access['roles']);
        Session::set('permissions',   $access['permissions']);
        Session::set('is_admin',      in_array('administrador', $access['roles']));
        return true;
    }
    private function setRememberCookie(int $userId): void
    {
        $token   = bin2hex(random_bytes(32));
        $expires = time() + (self::COOKIE_DAYS * 86400);
        $this->userModel->update($userId, ['recuerdo_token' => $token]);
        setcookie(self::COOKIE_NAME, $token, ['expires' => $expires, 'path' => '/', 'secure' => isset($_SERVER['HTTPS']), 'httponly' => true, 'samesite' => 'Lax']);
    }
    private function deleteRememberCookie(): void
    {
        $userId = Session::userId();
        if ($userId) $this->userModel->update($userId, ['recuerdo_token' => null]);
        setcookie(self::COOKIE_NAME, '', time() - 3600, '/');
    }
}
