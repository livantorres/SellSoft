<?php

namespace SellSoft\Models;

use SellSoft\Core\Database;
use PDO;

class Category
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM categorias ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categorias WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO categorias (nombre, abreviatura, slug, descripcion, activo) VALUES (:nombre, :abreviatura, :slug, :descripcion, :activo)");
        return $stmt->execute([
            ':nombre' => $data['nombre'] ?? '',
            ':abreviatura' => $data['abreviatura'] ?? null,
            ':slug' => $data['slug'] ?? '',
            ':descripcion' => $data['descripcion'] ?? null,
            ':activo' => $data['activo'] ?? 1
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE categorias SET nombre = :nombre, abreviatura = :abreviatura, slug = :slug, descripcion = :descripcion, activo = :activo WHERE id = :id");
        return $stmt->execute([
            ':nombre' => $data['nombre'] ?? '',
            ':abreviatura' => $data['abreviatura'] ?? null,
            ':slug' => $data['slug'] ?? '',
            ':descripcion' => $data['descripcion'] ?? null,
            ':activo' => $data['activo'] ?? 1,
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM categorias WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
