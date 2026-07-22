<?php
declare(strict_types=1);
namespace SellSoft\Models;
class User extends Model
{
    protected $table = 'usuarios';
    public function findByEmail(string $email): ?array
    {
        return $this->db->fetchOne(
            'SELECT u.*, b.nombre AS bodega_nombre FROM usuarios u LEFT JOIN bodegas b ON b.id = u.bodega_id WHERE u.correo = ? AND u.activo = 1',
            [strtolower(trim($email))]
        );
    }
    public function getRolesAndPermissions(int $userId): array
    {
        $roles = $this->db->fetchAll(
            'SELECT r.slug FROM roles r INNER JOIN usuario_roles ur ON ur.rol_id = r.id WHERE ur.usuario_id = ?',
            [$userId]
        );
        $permissions = $this->db->fetchAll(
            'SELECT DISTINCT p.modulo, p.accion FROM permisos p INNER JOIN rol_permisos rp ON rp.permiso_id = p.id INNER JOIN usuario_roles ur ON ur.rol_id = rp.rol_id WHERE ur.usuario_id = ?',
            [$userId]
        );
        return [
            'roles'       => array_column($roles, 'slug'),
            'permissions' => array_map(function ($p) { return "{$p['modulo']}.{$p['accion']}"; }, $permissions),
        ];
    }
    public function updateLastLogin(int $userId): void { $this->db->execute('UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?', [$userId]); }
}
