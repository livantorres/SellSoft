<?php
require_once __DIR__ . '/core/bootstrap.php';
try {
    $db = \SellSoft\Core\Database::getInstance()->getPdo();
    
    // Add descripcion to categorias if it doesn't exist
    try {
        $db->exec("ALTER TABLE categorias ADD COLUMN descripcion TEXT DEFAULT NULL AFTER slug");
        echo "Added descripcion to categorias.\n";
    } catch (\PDOException $e) {
        echo "Column descripcion might already exist in categorias.\n";
    }
    
    // Add descripcion to marcas if it doesn't exist
    try {
        $db->exec("ALTER TABLE marcas ADD COLUMN descripcion TEXT DEFAULT NULL AFTER nombre");
        echo "Added descripcion to marcas.\n";
    } catch (\PDOException $e) {
        echo "Column descripcion might already exist in marcas.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Revert the view variables to expect 'descripcion'
$file = 'resources/views/catalog/categories/index.php';
$c = file_get_contents($file);
$c = str_replace("['slug']", "['descripcion']", $c);
$c = str_replace("category.slug", "category.descripcion", $c);
file_put_contents($file, $c);

$file = 'resources/views/catalog/brands/index.php';
$c = file_get_contents($file);
$c = str_replace("['logo']", "['descripcion']", $c);
$c = str_replace("brand.logo", "brand.descripcion", $c);
file_put_contents($file, $c);

// Update CategoryController to include descripcion
$file = 'app/Controllers/CategoryController.php';
$c = file_get_contents($file);
// We need to add 'descripcion' => $_POST['description'] ?? null to the $data array
$c = preg_replace("/'slug' => strtolower\(trim\(preg_replace\('\/\[\^A-Za-z0-9-\]\+\/', '-', \\\$_POST\['name'\] \?\? ''\)\)\),/", "'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', \$_POST['name'] ?? ''))), 'descripcion' => \$_POST['description'] ?? null,", $c);
$c = preg_replace("/'slug' => strtolower\(trim\(preg_replace\('\/\[\^A-Za-z0-9-\]\+\/', '-', \\\$data\['name'\]\)\)\),/", "'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', \$data['name']))), 'descripcion' => \$data['description'] ?? null,", $c);
file_put_contents($file, $c);

// Update BrandController to include descripcion
$file = 'app/Controllers/BrandController.php';
$c = file_get_contents($file);
$c = preg_replace("/'nombre' => \\\$_POST\['name'\] \?\? '',/", "'nombre' => \$_POST['name'] ?? '', 'descripcion' => \$_POST['description'] ?? null,", $c);
$c = preg_replace("/'nombre' => \\\$data\['name'\],/", "'nombre' => \$data['name'], 'descripcion' => \$data['description'] ?? null,", $c);
file_put_contents($file, $c);

// Update Category Model
$file = 'app/Models/Category.php';
$c = file_get_contents($file);
$c = str_replace("INSERT INTO categorias (nombre, slug, activo) VALUES (:nombre, :slug, :activo)", "INSERT INTO categorias (nombre, slug, descripcion, activo) VALUES (:nombre, :slug, :descripcion, :activo)", $c);
$c = preg_replace("/':activo' => \\\$data\['activo'\] \?\? 1/", "':descripcion' => \$data['descripcion'] ?? null,\n            ':activo' => \$data['activo'] ?? 1", $c);
$c = str_replace("UPDATE categorias SET nombre = :nombre, slug = :slug, activo = :activo WHERE id = :id", "UPDATE categorias SET nombre = :nombre, slug = :slug, descripcion = :descripcion, activo = :activo WHERE id = :id", $c);
file_put_contents($file, $c);

// Update Brand Model
$file = 'app/Models/Brand.php';
$c = file_get_contents($file);
$c = str_replace("INSERT INTO marcas (nombre, activo) VALUES (:nombre, :activo)", "INSERT INTO marcas (nombre, descripcion, activo) VALUES (:nombre, :descripcion, :activo)", $c);
$c = preg_replace("/':activo' => \\\$data\['activo'\] \?\? 1/", "':descripcion' => \$data['descripcion'] ?? null,\n            ':activo' => \$data['activo'] ?? 1", $c);
$c = str_replace("UPDATE marcas SET nombre = :nombre, activo = :activo WHERE id = :id", "UPDATE marcas SET nombre = :nombre, descripcion = :descripcion, activo = :activo WHERE id = :id", $c);
file_put_contents($file, $c);

echo "Database and models updated for descripcion.\n";
