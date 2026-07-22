<?php

namespace SellSoft\Models;

use SellSoft\Core\Database;
use PDO;

class Product
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }

    public function getAll()
    {
        $sql = "SELECT p.*, c.nombre as categoria, m.nombre as marca 
                FROM productos p 
                LEFT JOIN categorias c ON p.categoria_id = c.id
                LEFT JOIN marcas m ON p.marca_id = m.id
                ORDER BY p.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM productos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO productos (nombre, slug, descripcion, codigo_sku, categoria_id, marca_id, proveedor_id, precio_compra, precio_venta, imagen_principal, activo) 
                    VALUES (:nombre, :slug, :descripcion, :codigo_sku, :categoria_id, :marca_id, :proveedor_id, :precio_compra, :precio_venta, :imagen_principal, :activo)";
            $stmt = $this->db->prepare($sql);
            
            $stmt->execute([
                ':nombre'           => $data['nombre'],
                ':slug'             => $data['slug'] ?? $this->generateSlug($data['nombre']),
                ':descripcion'      => $data['descripcion'] ?? null,
                ':codigo_sku'       => $data['codigo_sku'] ?? null,
                ':categoria_id'     => $data['categoria_id'] ?? null,
                ':marca_id'         => $data['marca_id'] ?? null,
                ':proveedor_id'     => $data['proveedor_id'] ?? null,
                ':precio_compra'    => $data['precio_compra'] ?? 0.00,
                ':precio_venta'     => $data['precio_venta'] ?? 0.00,
                ':imagen_principal' => $data['imagen_principal'] ?? null,
                ':activo'           => $data['activo'] ?? 1,
            ]);
            
            $productoId = $this->db->lastInsertId();
            
            // Initialize stock in producto_bodega
            $warehouseId = $_SESSION['warehouse_id'] ?? 1;
            $initialStock = $data['stock_inicial'] ?? 0;
            $minStock = $data['stock_minimo'] ?? 0;
            
            $sqlBodega = "INSERT INTO producto_bodega (producto_id, bodega_id, stock, stock_minimo) VALUES (:producto_id, :bodega_id, :stock, :stock_minimo)";
            $stmtBodega = $this->db->prepare($sqlBodega);
            $stmtBodega->execute([
                ':producto_id'  => $productoId,
                ':bodega_id'    => $warehouseId,
                ':stock'        => $initialStock,
                ':stock_minimo' => $minStock
            ]);
            
            $this->db->commit();
            return $productoId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $data)
    {
        $sql = "UPDATE productos SET 
                nombre = :nombre,
                slug = :slug,
                descripcion = :descripcion,
                codigo_sku = :codigo_sku,
                categoria_id = :categoria_id,
                marca_id = :marca_id,
                proveedor_id = :proveedor_id,
                precio_compra = :precio_compra,
                precio_venta = :precio_venta,
                imagen_principal = :imagen_principal,
                activo = :activo
                WHERE id = :id";
                
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'               => $id,
            ':nombre'           => $data['nombre'],
            ':slug'             => $data['slug'] ?? $this->generateSlug($data['nombre']),
            ':descripcion'      => $data['descripcion'] ?? null,
            ':codigo_sku'       => $data['codigo_sku'] ?? null,
            ':categoria_id'     => $data['categoria_id'] ?? null,
            ':marca_id'         => $data['marca_id'] ?? null,
            ':proveedor_id'     => $data['proveedor_id'] ?? null,
            ':precio_compra'    => $data['precio_compra'] ?? 0.00,
            ':precio_venta'     => $data['precio_venta'] ?? 0.00,
            ':imagen_principal' => $data['imagen_principal'] ?? null,
            ':activo'           => $data['activo'] ?? 1,
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM productos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    protected function generateSlug($string)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
        return $slug;
    }

    public function addGalleryImages($productoId, $urls) {
        if (empty($urls)) return;
        $sql = "INSERT INTO galeria_productos (producto_id, url_imagen, orden) VALUES (:producto_id, :url_imagen, :orden)";
        $stmt = $this->db->prepare($sql);
        foreach ($urls as $index => $url) {
            $stmt->execute([
                ':producto_id' => $productoId,
                ':url_imagen' => $url,
                ':orden' => $index
            ]);
        }
    }
    
    public function getGalleryImages($productoId) {
        $stmt = $this->db->prepare("SELECT * FROM galeria_productos WHERE producto_id = :id ORDER BY orden ASC");
        $stmt->execute([':id' => $productoId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function deleteGalleryImages($productoId) {
        $stmt = $this->db->prepare("DELETE FROM galeria_productos WHERE producto_id = :id");
        $stmt->execute([':id' => $productoId]);
    }

}
