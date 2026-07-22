<?php
declare(strict_types=1);
namespace SellSoft\Models;
class Setting extends Model
{
    protected $table = 'configuracion';
    private static $cache = [];
    public function get(string $key, $default = null)
    {
        if (!array_key_exists($key, self::$cache)) {
            $row = $this->db->fetchOne('SELECT valor FROM configuracion WHERE clave = ?', [$key]);
            self::$cache[$key] = ($row !== null) ? $row['valor'] : null;
        }
        return self::$cache[$key] !== null ? self::$cache[$key] : $default;
    }
    public function set(string $key, $value, string $group = 'general'): void
    {
        unset(self::$cache[$key]);
        $existing = $this->db->fetchOne('SELECT id FROM configuracion WHERE clave = ?', [$key]);
        if ($existing) {
            $this->db->execute('UPDATE configuracion SET valor = ? WHERE clave = ?', [$value, $key]);
        } else {
            $this->db->insert('INSERT INTO configuracion (clave, valor, grupo) VALUES (?, ?, ?)', [$key, $value, $group]);
        }
    }
    public function getGroup(string $group): array
    {
        $rows = $this->db->fetchAll('SELECT clave, valor FROM configuracion WHERE grupo = ?', [$group]);
        return array_column($rows, 'valor', 'clave');
    }
    public function preload(): void
    {
        $all = $this->db->fetchAll('SELECT clave, valor FROM configuracion');
        foreach ($all as $item) { self::$cache[$item['clave']] = $item['valor']; }
    }
}
