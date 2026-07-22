<?php

namespace SellSoft\Models;

use SellSoft\Core\Database;
use PDO;

class Provider
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM proveedores ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM proveedores WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO proveedores (nombre, contacto, telefono, correo, direccion, nit, activo) VALUES (:nombre, :contacto, :telefono, :correo, :direccion, :nit, :activo)");
        return $stmt->execute([
            'nombre' => $data['nombre'] ?? '',
            'contacto' => $data['contacto'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'email' => $data['email'] ?? null,
            'direccion' => $data['direccion'] ?? null,
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE proveedores SET nombre = :nombre, contacto = :contacto, telefono = :telefono, correo = :correo, direccion = :direccion, nit = :nit, activo = :activo WHERE id = :id");
        return $stmt->execute([
            ':nombre' => $data['nombre'] ?? '',
            ':contacto' => $data['contacto'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':correo' => $data['correo'] ?? null,
            ':direccion' => $data['direccion'] ?? null,
            ':nit' => $data['nit'] ?? null,
            ':activo' => $data['activo'] ?? 1,
            ':id' => $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM proveedores WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
