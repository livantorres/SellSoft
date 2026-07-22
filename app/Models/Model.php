<?php
declare(strict_types=1);
namespace SellSoft\Models;
use SellSoft\Core\Database;
abstract class Model
{
    protected $db;
    protected $table = '';
    protected $primaryKey = 'id';
    public function __construct() { $this->db = Database::getInstance(); }
    public function findById(int $id): ?array { return $this->db->fetchOne("SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?", [$id]); }
    public function all(string $order = 'id DESC'): array { return $this->db->fetchAll("SELECT * FROM `{$this->table}` ORDER BY {$order}"); }
    public function create(array $data): int
    {
        $columns      = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        return $this->db->insert("INSERT INTO `{$this->table}` (`{$columns}`) VALUES ({$placeholders})", array_values($data));
    }
    public function update(int $id, array $data): int
    {
        $assignments = implode(' = ?, ', array_keys($data)) . ' = ?';
        $values      = array_values($data);
        $values[]    = $id;
        return $this->db->execute("UPDATE `{$this->table}` SET {$assignments} WHERE `{$this->primaryKey}` = ?", $values);
    }
    public function delete(int $id): int { return $this->db->execute("DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?", [$id]); }
    public function count(string $condition = '', array $params = []): int
    {
        $result = $this->db->fetchOne("SELECT COUNT(*) as total FROM `{$this->table}` {$condition}", $params);
        return (int)($result['total'] ?? 0);
    }
    public function paginate(int $page, int $perPage = 15, string $condition = '', array $params = []): array
    {
        $offset  = ($page - 1) * $perPage;
        $total   = $this->count($condition, $params);
        $records = $this->db->fetchAll("SELECT * FROM `{$this->table}` {$condition} LIMIT ? OFFSET ?", array_merge($params, [$perPage, $offset]));
        return ['records' => $records, 'total' => $total, 'page' => $page, 'per_page' => $perPage, 'total_pages' => (int)ceil($total / $perPage)];
    }
}
