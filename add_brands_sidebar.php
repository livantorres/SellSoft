<?php
$file = 'resources/views/layouts/main.php';
$c = file_get_contents($file);

// Add Brands to sidebar under catalog
$c = str_replace(
    "['url' => '/dashboard/categories', 'icon' => 'fa-tags', 'label' => Lang::get('categories')],",
    "['url' => '/dashboard/categories', 'icon' => 'fa-tags', 'label' => Lang::get('categories')],\n                    ['url' => '/dashboard/brands', 'icon' => 'fa-copyright', 'label' => Lang::get('brands') ?? 'Marcas'],",
    $c
);

file_put_contents($file, $c);
echo "Sidebar updated.\n";
