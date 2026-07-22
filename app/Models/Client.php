<?php

namespace SellSoft\Models;

use SellSoft\Core\Database;
use PDO;

class Client
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT cl.*, c.nombre as ciudad_nombre, c.departamento_id, d.nombre as departamento_nombre 
                                  FROM clientes cl 
                                  LEFT JOIN ciudades c ON cl.ciudad_id = c.id 
                                  LEFT JOIN departamentos d ON c.departamento_id = d.id 
                                  ORDER BY cl.id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT cl.*, c.departamento_id FROM clientes cl LEFT JOIN ciudades c ON cl.ciudad_id = c.id WHERE cl.id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO clientes (nombre, tipo_doc, numero_doc, correo, telefono, direccion, ciudad_id, ciudad, is_proveedor, activo) VALUES (:nombre, :tipo_doc, :numero_doc, :correo, :telefono, :direccion, :ciudad_id, :ciudad_nombre, :is_proveedor, :activo)");
        $stmt->execute([
            ':nombre' => $data['nombre'] ?? '',
            ':tipo_doc' => $data['tipo_doc'] ?? 'CC',
            ':numero_doc' => $data['numero_doc'] ?? null,
            ':correo' => $data['correo'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':direccion' => $data['direccion'] ?? null,
            ':ciudad_id' => !empty($data['ciudad_id']) ? $data['ciudad_id'] : null,
            ':ciudad_nombre' => $data['ciudad_nombre'] ?? '',
            ':is_proveedor' => $data['is_proveedor'] ?? 0,
            ':activo' => $data['activo'] ?? 1
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE clientes SET nombre = :nombre, tipo_doc = :tipo_doc, numero_doc = :numero_doc, correo = :correo, telefono = :telefono, direccion = :direccion, ciudad_id = :ciudad_id, ciudad = :ciudad_nombre, is_proveedor = :is_proveedor, activo = :activo WHERE id = :id");
        return $stmt->execute([
            ':nombre' => $data['nombre'] ?? '',
            ':tipo_doc' => $data['tipo_doc'] ?? 'CC',
            ':numero_doc' => $data['numero_doc'] ?? null,
            ':correo' => $data['correo'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':direccion' => $data['direccion'] ?? null,
            ':ciudad_id' => !empty($data['ciudad_id']) ? $data['ciudad_id'] : null,
            ':ciudad_nombre' => $data['ciudad_nombre'] ?? '',
            ':is_proveedor' => $data['is_proveedor'] ?? 0,
            ':activo' => $data['activo'] ?? 1,
            ':id' => $id
        ]);
    }

    public function updateProveedorId($clienteId, $proveedorId) {
        $stmt = $this->db->prepare("UPDATE clientes SET proveedor_id = ? WHERE id = ?");
        return $stmt->execute([$proveedorId, $clienteId]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM clientes WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
