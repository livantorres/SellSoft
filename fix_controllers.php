<?php
$files = [
    'app/Controllers/CategoryController.php',
    'app/Controllers/BrandController.php',
    'app/Controllers/ProviderController.php'
];

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Make them extend Controller
    if (strpos($content, 'class CategoryController extends') === false && strpos($content, 'class CategoryController') !== false) {
        $content = str_replace('class CategoryController', 'use SellSoft\Core\Controller;' . "\n" . 'class CategoryController extends Controller', $content);
    }
    if (strpos($content, 'class BrandController extends') === false && strpos($content, 'class BrandController') !== false) {
        $content = str_replace('class BrandController', 'use SellSoft\Core\Controller;' . "\n" . 'class BrandController extends Controller', $content);
    }
    if (strpos($content, 'class ProviderController extends') === false && strpos($content, 'class ProviderController') !== false) {
        $content = str_replace('class ProviderController', 'use SellSoft\Core\Controller;' . "\n" . 'class ProviderController extends Controller', $content);
    }
    
    // Replace view() with $this->view()
    $content = str_replace('return view(', '$this->view(', $content);
    
    file_put_contents($file, $content);
}

// Now for ProductController
$file = 'app/Controllers/ProductController.php';
$content = file_get_contents($file);
if (strpos($content, 'class ProductController extends') === false) {
    $content = str_replace('class ProductController', 'use SellSoft\Core\Controller;' . "\n" . 'class ProductController extends Controller', $content);
}

// Replace requires with $this->view()
$content = str_replace("require_once __DIR__ . '/../../resources/views/catalog/products/index.php';", "\$this->view('catalog.products.index', ['products' => \$products]);", $content);
$content = str_replace("require_once __DIR__ . '/../../resources/views/catalog/products/create.php';", "\$this->view('catalog.products.create');", $content);
$content = preg_replace("/require_once __DIR__ \\. '\\/\\.\\.\\/\\.\\.\\/resources\\/views\\/catalog\\/products\\/edit\\.php';/", "\$this->view('catalog.products.edit', ['product' => \$product]);", $content);

file_put_contents($file, $content);

echo "Fixed Controllers.\n";
