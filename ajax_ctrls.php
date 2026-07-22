<?php
$controllers = [
    'app/Controllers/CategoryController.php',
    'app/Controllers/BrandController.php',
    'app/Controllers/ProviderController.php'
];

foreach ($controllers as $file) {
    if (file_exists($file)) {
        $c = file_get_contents($file);
        // Replace update
        $c = preg_replace('/public function update\(\$id\)\s*\{/', "public function update()\n    {\n        \$id = \$_POST['id'] ?? null;\n        if (!\$id) { echo json_encode(['success' => false, 'message' => 'ID is missing']); return; }", $c);
        // Replace delete
        $c = preg_replace('/public function delete\(\$id\)\s*\{/', "public function delete()\n    {\n        \$id = \$_POST['id'] ?? null;\n        if (!\$id) { echo json_encode(['success' => false, 'message' => 'ID is missing']); return; }", $c);
        file_put_contents($file, $c);
    }
}

// ProductController is slightly different
$file = 'app/Controllers/ProductController.php';
if (file_exists($file)) {
    $c = file_get_contents($file);
    // Replace store
    $c = str_replace('header(\'Location: /dashboard/products\');', 'echo json_encode([\'success\' => true, \'message\' => \SellSoft\Helpers\Lang::get(\'messages.created_successfully\')]);', $c);
    
    // Replace update
    $c = preg_replace('/public function update\(\$id\)\s*\{/', "public function update()\n    {\n        \$id = \$_POST['id'] ?? null;\n        if (!\$id) { echo json_encode(['success' => false, 'message' => 'ID is missing']); return; }", $c);
    $c = str_replace('header(\'Location: /dashboard/products\');', 'echo json_encode([\'success\' => true, \'message\' => \SellSoft\Helpers\Lang::get(\'messages.updated_successfully\')]);', $c);
    
    // Replace delete
    $c = preg_replace('/public function delete\(\$id\)\s*\{/', "public function delete()\n    {\n        \$id = \$_POST['id'] ?? null;\n        if (!\$id) { echo json_encode(['success' => false, 'message' => 'ID is missing']); return; }", $c);
    $c = str_replace('header(\'Location: /dashboard/products\');', 'echo json_encode([\'success\' => true, \'message\' => \SellSoft\Helpers\Lang::get(\'messages.deleted_successfully\')]);', $c);
    
    // Catch blocks in store and update
    $c = preg_replace('/catch \(\\\\Exception \$e\) \{.*?\$this->view\(\'catalog\.products\.(.*?)\'.*?\}\s*\}/s', "catch (\\Exception \$e) {\n                echo json_encode(['success' => false, 'message' => \$e->getMessage()]);\n            }\n        }", $c);

    file_put_contents($file, $c);
}
echo "Controllers Updated for AJAX.\n";
