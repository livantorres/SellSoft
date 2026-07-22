<?php
// categories/index.php
$file = 'resources/views/catalog/categories/index.php';
$c = file_get_contents($file);
$c = str_replace('action="/catalog/categories/save"', 'action="/dashboard/categories"', $c);
$c = str_replace("action = '/catalog/categories/delete/' + id;", "action = '/dashboard/categories/delete';\n            let idInput = document.createElement('input');\n            idInput.type = 'hidden';\n            idInput.name = 'id';\n            idInput.value = id;\n            form.appendChild(idInput);", $c);
$c = str_replace('btn-primary"', 'btn-primary-app"', $c);
$c = str_replace('btn-secondary"', 'btn-secondary-app"', $c);
file_put_contents($file, $c);

// brands/index.php
$file = 'resources/views/catalog/brands/index.php';
if (file_exists($file)) {
    $c = file_get_contents($file);
    $c = str_replace('action="/catalog/brands/save"', 'action="/dashboard/brands"', $c);
    $c = str_replace("action = '/catalog/brands/delete/' + id;", "action = '/dashboard/brands/delete';\n            let idInput = document.createElement('input');\n            idInput.type = 'hidden';\n            idInput.name = 'id';\n            idInput.value = id;\n            form.appendChild(idInput);", $c);
    $c = str_replace('btn-primary"', 'btn-primary-app"', $c);
    $c = str_replace('btn-secondary"', 'btn-secondary-app"', $c);
    file_put_contents($file, $c);
}

// providers/index.php
$file = 'resources/views/catalog/providers/index.php';
if (file_exists($file)) {
    $c = file_get_contents($file);
    $c = str_replace('action="/catalog/providers/save"', 'action="/dashboard/providers"', $c);
    $c = str_replace("action = '/catalog/providers/delete/' + id;", "action = '/dashboard/providers/delete';\n            let idInput = document.createElement('input');\n            idInput.type = 'hidden';\n            idInput.name = 'id';\n            idInput.value = id;\n            form.appendChild(idInput);", $c);
    $c = str_replace('btn-primary"', 'btn-primary-app"', $c);
    $c = str_replace('btn-secondary"', 'btn-secondary-app"', $c);
    file_put_contents($file, $c);
}

// products/index.php
$file = 'resources/views/catalog/products/index.php';
if (file_exists($file)) {
    $c = file_get_contents($file);
    $c = str_replace('href="/products/create"', 'href="/dashboard/products/create"', $c);
    $c = str_replace('href="/products/edit/<?= $product[\'id\'] ?>" class="btn btn-sm btn-warning"', 'href="/dashboard/products/<?= $product[\'id\'] ?>/edit" class="btn btn-sm btn-warning text-decoration-none"', $c);
    $c = str_replace('action="/products/delete/<?= $product[\'id\'] ?>" method="POST"', 'action="/dashboard/products/delete" method="POST"', $c);
    $c = str_replace('<button type="submit"', '<input type="hidden" name="id" value="<?= $product[\'id\'] ?>"><button type="submit"', $c);
    $c = str_replace('btn-primary"', 'btn-primary-app"', $c);
    $c = str_replace('btn-secondary"', 'btn-secondary-app"', $c);
    file_put_contents($file, $c);
}

// products/create.php
$file = 'resources/views/catalog/products/create.php';
if (file_exists($file)) {
    $c = file_get_contents($file);
    $c = str_replace('action="/products/store"', 'action="/dashboard/products"', $c);
    $c = str_replace('href="/products"', 'href="/dashboard/products"', $c);
    $c = str_replace('btn-primary"', 'btn-primary-app"', $c);
    $c = str_replace('btn-secondary ', 'btn-secondary-app ', $c);
    file_put_contents($file, $c);
}

// products/edit.php
$file = 'resources/views/catalog/products/edit.php';
if (file_exists($file)) {
    $c = file_get_contents($file);
    $c = str_replace('action="/products/update/<?= $product[\'id\'] ?>"', 'action="/dashboard/products/update"', $c);
    $c = preg_replace('/<form action="\/dashboard\/products\/update" method="POST" enctype="multipart\/form-data">/', '<form action="/dashboard/products/update" method="POST" enctype="multipart/form-data"><input type="hidden" name="id" value="<?= $product[\'id\'] ?>">', $c);
    $c = str_replace('href="/products"', 'href="/dashboard/products"', $c);
    $c = str_replace('btn-primary"', 'btn-primary-app"', $c);
    $c = str_replace('btn-secondary ', 'btn-secondary-app ', $c);
    file_put_contents($file, $c);
}
echo "URLs fixed.\n";
