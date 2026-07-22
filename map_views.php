<?php
// Fix Categories
$file = 'resources/views/catalog/categories/index.php';
$c = file_get_contents($file);
$c = str_replace("['name']", "['nombre']", $c);
$c = str_replace("['description']", "['slug']", $c);
$c = str_replace("['status']", "['activo']", $c);
$c = str_replace("category.name", "category.nombre", $c);
$c = str_replace("category.description", "category.slug", $c);
$c = str_replace("category.status", "category.activo", $c);
file_put_contents($file, $c);

// Fix Brands
$file = 'resources/views/catalog/brands/index.php';
$c = file_get_contents($file);
$c = str_replace("['name']", "['nombre']", $c);
$c = str_replace("['description']", "['logo']", $c); // there is no description in DB, let's just fall back to logo or empty
$c = str_replace("['status']", "['activo']", $c);
$c = str_replace("brand.name", "brand.nombre", $c);
$c = str_replace("brand.description", "brand.logo", $c);
$c = str_replace("brand.status", "brand.activo", $c);
file_put_contents($file, $c);

// Fix Providers
$file = 'resources/views/catalog/providers/index.php';
$c = file_get_contents($file);
$c = str_replace("['name']", "['nombre']", $c);
$c = str_replace("['email']", "['correo']", $c);
$c = str_replace("['phone']", "['telefono']", $c);
$c = str_replace("['address']", "['direccion']", $c);
$c = str_replace("['status']", "['activo']", $c);
$c = str_replace("provider.name", "provider.nombre", $c);
$c = str_replace("provider.email", "provider.correo", $c);
$c = str_replace("provider.phone", "provider.telefono", $c);
$c = str_replace("provider.address", "provider.direccion", $c);
$c = str_replace("provider.status", "provider.activo", $c);
file_put_contents($file, $c);

echo "Views mapped to database columns.\n";
