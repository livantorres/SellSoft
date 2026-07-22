<?php
$file = 'app/Controllers/ProductController.php';
$c = file_get_contents($file);

// Add use statements for Models if not there
if (strpos($c, 'use SellSoft\Models\Category;') === false) {
    $c = str_replace('use SellSoft\Models\Product;', "use SellSoft\Models\Product;\nuse SellSoft\Models\Category;\nuse SellSoft\Models\Brand;\nuse SellSoft\Models\Provider;", $c);
}

// Update index method
$c = preg_replace(
    '/public function index\(\).*?\{.*?\}/s',
    'public function index()
    {
        $products = $this->productModel->getAll();
        $categories = (new Category())->getAll();
        $brands = (new Brand())->getAll();
        $providers = (new Provider())->getAll();
        
        $this->view(\'catalog.products.index\', [
            \'products\' => $products,
            \'categories\' => $categories,
            \'brands\' => $brands,
            \'providers\' => $providers,
            \'title\' => Lang::get(\'catalog.products.title\')
        ]);
    }',
    $c
);

file_put_contents($file, $c);
echo "ProductController index method updated.\n";
