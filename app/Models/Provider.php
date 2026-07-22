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
        $stmt = $this->db->query("SELECT p.*, c.nombre as ciudad_nombre, c.departamento_id, d.nombre as departamento_nombre FROM proveedores p LEFT JOIN ciudades c ON p.ciudad_id = c.id LEFT JOIN departamentos d ON c.departamento_id = d.id ORDER BY p.id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT p.*, c.departamento_id FROM proveedores p LEFT JOIN ciudades c ON p.ciudad_id = c.id WHERE p.id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO proveedores (nombre, tipo_documento, nit, contacto, telefono, correo, direccion, ciudad_id, imagen, is_cliente, activo) VALUES (:nombre, :tipo_documento, :nit, :contacto, :telefono, :correo, :direccion, :ciudad_id, :imagen, :is_cliente, :activo)");
        $stmt->execute([
            ':nombre' => $data['nombre'] ?? '',
            ':tipo_documento' => $data['tipo_documento'] ?? 'NIT',
            ':nit' => $data['nit'] ?? null,
            ':contacto' => $data['contacto'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':correo' => $data['correo'] ?? null,
            ':direccion' => $data['direccion'] ?? null,
            ':ciudad_id' => !empty($data['ciudad_id']) ? $data['ciudad_id'] : null,
            ':imagen' => $data['imagen'] ?? null,
            ':is_cliente' => $data['is_cliente'] ?? 0,
            ':activo' => $data['activo'] ?? 1
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $query = "UPDATE proveedores SET nombre = :nombre, tipo_documento = :tipo_documento, nit = :nit, contacto = :contacto, telefono = :telefono, correo = :correo, direccion = :direccion, ciudad_id = :ciudad_id, is_cliente = :is_cliente, activo = :activo";
        $params = [
            ':nombre' => $data['nombre'] ?? '',
            ':tipo_documento' => $data['tipo_documento'] ?? 'NIT',
            ':nit' => $data['nit'] ?? null,
            ':contacto' => $data['contacto'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':correo' => $data['correo'] ?? null,
            ':direccion' => $data['direccion'] ?? null,
            ':ciudad_id' => !empty($data['ciudad_id']) ? $data['ciudad_id'] : null,
            ':is_cliente' => $data['is_cliente'] ?? 0,
            ':activo' => $data['activo'] ?? 1,
            ':id' => $id
        ];

        if (isset($data['imagen'])) {
            $query .= ", imagen = :imagen";
            $params[':imagen'] = $data['imagen'];
        }

        $query .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM proveedores WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
