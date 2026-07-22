<?php
// Fix Category.php
$file = 'app/Models/Category.php';
$content = file_get_contents($file);
$content = str_replace('$this->db = Database::getInstance();', '$this->db = Database::getInstance()->getPdo();', $content);
$content = preg_replace('/INSERT INTO categorias \(nombre, descripcion\) VALUES \(:nombre, :descripcion\)/', 'INSERT INTO categorias (nombre, slug, activo) VALUES (:nombre, :slug, :activo)', $content);
$content = preg_replace('/UPDATE categorias SET nombre = :nombre, descripcion = :descripcion WHERE id = :id/', 'UPDATE categorias SET nombre = :nombre, slug = :slug, activo = :activo WHERE id = :id', $content);
file_put_contents($file, $content);

// Fix Brand.php
$file = 'app/Models/Brand.php';
$content = file_get_contents($file);
$content = str_replace('$this->db = Database::getInstance();', '$this->db = Database::getInstance()->getPdo();', $content);
$content = preg_replace('/INSERT INTO marcas \(nombre, descripcion\) VALUES \(:nombre, :descripcion\)/', 'INSERT INTO marcas (nombre, activo) VALUES (:nombre, :activo)', $content);
$content = preg_replace('/UPDATE marcas SET nombre = :nombre, descripcion = :descripcion WHERE id = :id/', 'UPDATE marcas SET nombre = :nombre, activo = :activo WHERE id = :id', $content);
file_put_contents($file, $content);

// Fix Provider.php
$file = 'app/Models/Provider.php';
$content = file_get_contents($file);
$content = str_replace('$this->db = Database::getInstance();', '$this->db = Database::getInstance()->getPdo();', $content);
$content = preg_replace('/INSERT INTO proveedores \(nombre, contacto, telefono, email, direccion\) VALUES \(:nombre, :contacto, :telefono, :email, :direccion\)/', 'INSERT INTO proveedores (nombre, contacto, telefono, correo, direccion, nit, activo) VALUES (:nombre, :contacto, :telefono, :correo, :direccion, :nit, :activo)', $content);
$content = preg_replace('/UPDATE proveedores SET nombre = :nombre, contacto = :contacto, telefono = :telefono, email = :email, direccion = :direccion WHERE id = :id/', 'UPDATE proveedores SET nombre = :nombre, contacto = :contacto, telefono = :telefono, correo = :correo, direccion = :direccion, nit = :nit, activo = :activo WHERE id = :id', $content);
file_put_contents($file, $content);

// Now the controllers!
// CategoryController
$file = 'app/Controllers/CategoryController.php';
$content = file_get_contents($file);
$content = str_replace('\'nombre\' => $_POST[\'name\'],', '\'nombre\' => $_POST[\'name\'], \'slug\' => strtolower(trim(preg_replace(\'/[^A-Za-z0-9-]+/\', \'-\', $_POST[\'name\']))), \'activo\' => $_POST[\'status\'],', $content);
$content = str_replace('\'descripcion\' => $_POST[\'description\']', '', $content);
file_put_contents($file, $content);

// BrandController
$file = 'app/Controllers/BrandController.php';
$content = file_get_contents($file);
$content = str_replace('\'nombre\' => $_POST[\'name\'],', '\'nombre\' => $_POST[\'name\'], \'activo\' => $_POST[\'status\'],', $content);
$content = str_replace('\'descripcion\' => $_POST[\'description\']', '', $content);
file_put_contents($file, $content);

// ProviderController
$file = 'app/Controllers/ProviderController.php';
$content = file_get_contents($file);
$content = str_replace('\'email\' => $_POST[\'email\'],', '\'correo\' => $_POST[\'email\'], \'nit\' => $_POST[\'nit\'] ?? null, \'activo\' => $_POST[\'status\'] ?? 1,', $content);
file_put_contents($file, $content);

echo "Models and Controllers Fixed.\n";
