<?php
try {
    $db = new \PDO('mysql:host=localhost;dbname=sellsoft_db;charset=utf8mb4', 'root', '');
    $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    
    // Add abreviatura to categorias if it doesn't exist
    try {
        $db->exec("ALTER TABLE categorias ADD COLUMN abreviatura VARCHAR(10) DEFAULT NULL AFTER nombre");
        echo "Added abreviatura to categorias.\n";
    } catch (\PDOException $e) {
        echo "Column abreviatura might already exist in categorias.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Update Category.php model
$file = 'app/Models/Category.php';
$c = file_get_contents($file);
$c = str_replace(
    "INSERT INTO categorias (nombre, slug, descripcion, activo) VALUES (:nombre, :slug, :descripcion, :activo)",
    "INSERT INTO categorias (nombre, abreviatura, slug, descripcion, activo) VALUES (:nombre, :abreviatura, :slug, :descripcion, :activo)",
    $c
);
$c = preg_replace(
    "/':nombre'           => \\\$data\['nombre'\],/",
    "':nombre'           => \$data['nombre'],\n            ':abreviatura'      => \$data['abreviatura'] ?? null,",
    $c
);
$c = str_replace(
    "UPDATE categorias SET nombre = :nombre, slug = :slug, descripcion = :descripcion, activo = :activo WHERE id = :id",
    "UPDATE categorias SET nombre = :nombre, abreviatura = :abreviatura, slug = :slug, descripcion = :descripcion, activo = :activo WHERE id = :id",
    $c
);
file_put_contents($file, $c);

// Update CategoryController.php
$file = 'app/Controllers/CategoryController.php';
$c = file_get_contents($file);
$c = str_replace(
    "'nombre' => \$_POST['name'] ?? '',",
    "'nombre' => \$_POST['name'] ?? '', 'abreviatura' => strtoupper(trim(\$_POST['abreviatura'] ?? '')),",
    $c
);
$c = str_replace(
    "'nombre' => \$data['name'],",
    "'nombre' => \$data['name'], 'abreviatura' => strtoupper(trim(\$data['abreviatura'] ?? '')),",
    $c
);
file_put_contents($file, $c);

// Update categories/index.php view
$file = 'resources/views/catalog/categories/index.php';
$c = file_get_contents($file);

$abrevInput = '
              <div class="mb-3 row">
                  <div class="col-md-8">
                      <label for="categoryName" class="form-label"><?= \SellSoft\Helpers\Lang::get(\'common.name\') ?> *</label>
                      <input type="text" class="form-control" id="categoryName" name="name" required>
                  </div>
                  <div class="col-md-4">
                      <label for="categoryAbrev" class="form-label">Abreviatura</label>
                      <input type="text" class="form-control text-uppercase" id="categoryAbrev" name="abreviatura" placeholder="Ej: ZAP" maxlength="10">
                  </div>
              </div>';

$c = preg_replace('/<div class="mb-3">\s*<label for="categoryName" class="form-label">.*?<\/label>\s*<input type="text" class="form-control" id="categoryName" name="name" required>\s*<\/div>/s', $abrevInput, $c);

// Update Javascript edit function
$c = str_replace(
    "document.getElementById('categoryName').value = cat.nombre;",
    "document.getElementById('categoryName').value = cat.nombre;\n    document.getElementById('categoryAbrev').value = cat.abreviatura || '';",
    $c
);
file_put_contents($file, $c);

echo "Category DB, Model, Controller and View updated.\n";
