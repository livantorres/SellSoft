<?php
// Fix CategoryController
$file = 'app/Controllers/CategoryController.php';
$content = file_get_contents($file);
$content = preg_replace('/\$data = \$_POST;/', '$data = [\'nombre\' => $_POST[\'name\'] ?? \'\', \'slug\' => strtolower(trim(preg_replace(\'/[^A-Za-z0-9-]+/\', \'-\', $_POST[\'name\'] ?? \'\'))), \'activo\' => $_POST[\'status\'] ?? 1];', $content);
$content = preg_replace('/\$data = json_decode\(file_get_contents\(\'php:\/\/input\'\), true\) \?\? \[\];/', '$data = json_decode(file_get_contents(\'php://input\'), true) ?? []; if(isset($data[\'name\'])) { $data = [\'nombre\' => $data[\'name\'], \'slug\' => strtolower(trim(preg_replace(\'/[^A-Za-z0-9-]+/\', \'-\', $data[\'name\']))), \'activo\' => $data[\'status\'] ?? 1]; }', $content);
file_put_contents($file, $content);

// Fix BrandController
$file = 'app/Controllers/BrandController.php';
$content = file_get_contents($file);
$content = preg_replace('/\$data = \$_POST;/', '$data = [\'nombre\' => $_POST[\'name\'] ?? \'\', \'activo\' => $_POST[\'status\'] ?? 1];', $content);
$content = preg_replace('/\$data = json_decode\(file_get_contents\(\'php:\/\/input\'\), true\) \?\? \[\];/', '$data = json_decode(file_get_contents(\'php://input\'), true) ?? []; if(isset($data[\'name\'])) { $data = [\'nombre\' => $data[\'name\'], \'activo\' => $data[\'status\'] ?? 1]; }', $content);
file_put_contents($file, $content);

// Fix ProviderController
$file = 'app/Controllers/ProviderController.php';
$content = file_get_contents($file);
$content = preg_replace('/\$data = \$_POST;/', '$data = [\'nombre\' => $_POST[\'name\'] ?? \'\', \'nit\' => $_POST[\'nit\'] ?? null, \'correo\' => $_POST[\'email\'] ?? null, \'telefono\' => $_POST[\'phone\'] ?? null, \'direccion\' => $_POST[\'address\'] ?? null, \'contacto\' => $_POST[\'contact\'] ?? null, \'activo\' => $_POST[\'status\'] ?? 1];', $content);
$content = preg_replace('/\$data = json_decode\(file_get_contents\(\'php:\/\/input\'\), true\) \?\? \[\];/', '$data = json_decode(file_get_contents(\'php://input\'), true) ?? []; if(isset($data[\'name\'])) { $data = [\'nombre\' => $data[\'name\'], \'nit\' => $data[\'nit\'] ?? null, \'correo\' => $data[\'email\'] ?? null, \'telefono\' => $data[\'phone\'] ?? null, \'direccion\' => $data[\'address\'] ?? null, \'contacto\' => $data[\'contact\'] ?? null, \'activo\' => $data[\'status\'] ?? 1]; }', $content);
file_put_contents($file, $content);

// Fix ProductController
$file = 'app/Controllers/ProductController.php';
$content = file_get_contents($file);
$content = str_replace('header(\'Location: /products\');', 'header(\'Location: /dashboard/products\');', $content);
$content = str_replace('$this->productModel->find($id)', '$this->productModel->getById($id)', $content);
file_put_contents($file, $content);

echo "Controllers fixed.\n";
