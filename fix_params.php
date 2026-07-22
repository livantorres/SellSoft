<?php

// Category.php
$file = 'app/Models/Category.php';
$content = file_get_contents($file);
$content = preg_replace('/public function create\(\$data\)\s*\{\s*\$stmt = \$this->db->prepare\("INSERT INTO categorias \(nombre, slug, activo\) VALUES \(:nombre, :slug, :activo\)"\);\s*return \$stmt->execute\(\[\s*\'nombre\' => \$data\[\'nombre\'\] \?\? \'\',\s*\'descripcion\' => \$data\[\'descripcion\'\] \?\? null\s*\]\);\s*\}/', 'public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO categorias (nombre, slug, activo) VALUES (:nombre, :slug, :activo)");
        return $stmt->execute([
            \':nombre\' => $data[\'nombre\'] ?? \'\',
            \':slug\' => $data[\'slug\'] ?? \'\',
            \':activo\' => $data[\'activo\'] ?? 1
        ]);
    }', $content);

$content = preg_replace('/public function update\(\$id, \$data\)\s*\{\s*\$stmt = \$this->db->prepare\("UPDATE categorias SET nombre = :nombre, slug = :slug, activo = :activo WHERE id = :id"\);\s*return \$stmt->execute\(\[\s*\'nombre\' => \$data\[\'nombre\'\] \?\? \'\',\s*\'descripcion\' => \$data\[\'descripcion\'\] \?\? null,\s*\'id\' => \$id\s*\]\);\s*\}/', 'public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE categorias SET nombre = :nombre, slug = :slug, activo = :activo WHERE id = :id");
        return $stmt->execute([
            \':nombre\' => $data[\'nombre\'] ?? \'\',
            \':slug\' => $data[\'slug\'] ?? \'\',
            \':activo\' => $data[\'activo\'] ?? 1,
            \':id\' => $id
        ]);
    }', $content);
file_put_contents($file, $content);


// Brand.php
$file = 'app/Models/Brand.php';
$content = file_get_contents($file);
$content = preg_replace('/public function create\(\$data\)\s*\{\s*\$stmt = \$this->db->prepare\("INSERT INTO marcas \(nombre, activo\) VALUES \(:nombre, :activo\)"\);\s*return \$stmt->execute\(\[\s*\'nombre\' => \$data\[\'nombre\'\] \?\? \'\',\s*\'descripcion\' => \$data\[\'descripcion\'\] \?\? null\s*\]\);\s*\}/', 'public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO marcas (nombre, activo) VALUES (:nombre, :activo)");
        return $stmt->execute([
            \':nombre\' => $data[\'nombre\'] ?? \'\',
            \':activo\' => $data[\'activo\'] ?? 1
        ]);
    }', $content);

$content = preg_replace('/public function update\(\$id, \$data\)\s*\{\s*\$stmt = \$this->db->prepare\("UPDATE marcas SET nombre = :nombre, activo = :activo WHERE id = :id"\);\s*return \$stmt->execute\(\[\s*\'nombre\' => \$data\[\'nombre\'\] \?\? \'\',\s*\'descripcion\' => \$data\[\'descripcion\'\] \?\? null,\s*\'id\' => \$id\s*\]\);\s*\}/', 'public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE marcas SET nombre = :nombre, activo = :activo WHERE id = :id");
        return $stmt->execute([
            \':nombre\' => $data[\'nombre\'] ?? \'\',
            \':activo\' => $data[\'activo\'] ?? 1,
            \':id\' => $id
        ]);
    }', $content);
file_put_contents($file, $content);


// Provider.php
$file = 'app/Models/Provider.php';
$content = file_get_contents($file);
$content = preg_replace('/public function create\(\$data\)\s*\{\s*\$stmt = \$this->db->prepare\("INSERT INTO proveedores \(nombre, contacto, telefono, correo, direccion, nit, activo\) VALUES \(:nombre, :contacto, :telefono, :correo, :direccion, :nit, :activo\)"\);\s*return \$stmt->execute\(\[\s*\'nombre\' => \$data\[\'nombre\'\] \?\? \'\',\s*\'contacto\' => \$data\[\'contacto\'\] \?\? null,\s*\'telefono\' => \$data\[\'telefono\'\] \?\? null,\s*\'email\' => \$data\[\'email\'\] \?\? null,\s*\'direccion\' => \$data\[\'direccion\'\] \?\? null\s*\]\);\s*\}/', 'public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO proveedores (nombre, contacto, telefono, correo, direccion, nit, activo) VALUES (:nombre, :contacto, :telefono, :correo, :direccion, :nit, :activo)");
        return $stmt->execute([
            \':nombre\' => $data[\'nombre\'] ?? \'\',
            \':contacto\' => $data[\'contacto\'] ?? null,
            \':telefono\' => $data[\'telefono\'] ?? null,
            \':correo\' => $data[\'correo\'] ?? null,
            \':direccion\' => $data[\'direccion\'] ?? null,
            \':nit\' => $data[\'nit\'] ?? null,
            \':activo\' => $data[\'activo\'] ?? 1
        ]);
    }', $content);

$content = preg_replace('/public function update\(\$id, \$data\)\s*\{\s*\$stmt = \$this->db->prepare\("UPDATE proveedores SET nombre = :nombre, contacto = :contacto, telefono = :telefono, correo = :correo, direccion = :direccion, nit = :nit, activo = :activo WHERE id = :id"\);\s*return \$stmt->execute\(\[\s*\'nombre\' => \$data\[\'nombre\'\] \?\? \'\',\s*\'contacto\' => \$data\[\'contacto\'\] \?\? null,\s*\'telefono\' => \$data\[\'telefono\'\] \?\? null,\s*\'email\' => \$data\[\'email\'\] \?\? null,\s*\'direccion\' => \$data\[\'direccion\'\] \?\? null,\s*\'id\' => \$id\s*\]\);\s*\}/', 'public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE proveedores SET nombre = :nombre, contacto = :contacto, telefono = :telefono, correo = :correo, direccion = :direccion, nit = :nit, activo = :activo WHERE id = :id");
        return $stmt->execute([
            \':nombre\' => $data[\'nombre\'] ?? \'\',
            \':contacto\' => $data[\'contacto\'] ?? null,
            \':telefono\' => $data[\'telefono\'] ?? null,
            \':correo\' => $data[\'correo\'] ?? null,
            \':direccion\' => $data[\'direccion\'] ?? null,
            \':nit\' => $data[\'nit\'] ?? null,
            \':activo\' => $data[\'activo\'] ?? 1,
            \':id\' => $id
        ]);
    }', $content);
file_put_contents($file, $content);

echo "Fixed models.";
