<?php

namespace SellSoft\Models;

use SellSoft\Core\Database;
use PDO;

class Brand
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM marcas ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM marcas WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO marcas (nombre, descripcion, activo) VALUES (:nombre, :descripcion, :activo)");
        return $stmt->execute([
            ':nombre' => $data['nombre'] ?? '',
            ':descripcion' => $data['descripcion'] ?? null,
            ':activo' => $data['activo'] ?? 1
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE marcas SET nombre = :nombre, descripcion = :descripcion, activo = :activo WHERE id = :id");
        return $stmt->execute([
            ':nombre' => $data['nombre'] ?? '',
            ':descripcion' => $data['descripcion'] ?? null,
            ':activo' => $data['activo'] ?? 1,
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM marcas WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
