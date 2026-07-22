<?php
// Fix ProductController.php
$file = 'app/Controllers/ProductController.php';
$content = file_get_contents($file);
if (strpos($content, 'use SellSoft\Models\Category;') === false) {
    $content = str_replace('use SellSoft\Models\Product;', "use SellSoft\Models\Product;\nuse SellSoft\Models\Category;\nuse SellSoft\Models\Brand;\nuse SellSoft\Models\Provider;", $content);
}
$content = preg_replace('/public function create\(\)\s*\{\s*\$this->view\(\'catalog\.products\.create\'\);\s*\}/', 'public function create() {
        $catModel = new Category();
        $brandModel = new Brand();
        $provModel = new Provider();
        $this->view(\'catalog.products.create\', [
            \'categories\' => $catModel->getAll(),
            \'brands\' => $brandModel->getAll(),
            \'providers\' => $provModel->getAll()
        ]);
    }', $content);

$content = preg_replace('/\$this->view\(\'catalog\.products\.edit\', \[\'product\' => \$product\]\);/', '$catModel = new Category();
        $brandModel = new Brand();
        $provModel = new Provider();
        $this->view(\'catalog.products.edit\', [
            \'product\' => $product,
            \'categories\' => $catModel->getAll(),
            \'brands\' => $brandModel->getAll(),
            \'providers\' => $provModel->getAll()
        ]);', $content);
file_put_contents($file, $content);

// Fix create.php
$file = 'resources/views/catalog/products/create.php';
$content = file_get_contents($file);
$content = str_replace('<!-- Categories loop here -->', '<?php if(!empty($categories)): foreach($categories as $c): ?><option value="<?= $c[\'id\'] ?>"><?= htmlspecialchars($c[\'nombre\']) ?></option><?php endforeach; endif; ?>', $content);
$content = str_replace('<!-- Brands loop here -->', '<?php if(!empty($brands)): foreach($brands as $b): ?><option value="<?= $b[\'id\'] ?>"><?= htmlspecialchars($b[\'nombre\']) ?></option><?php endforeach; endif; ?>', $content);
$content = str_replace('<!-- Providers loop here -->', '<?php if(!empty($providers)): foreach($providers as $p): ?><option value="<?= $p[\'id\'] ?>"><?= htmlspecialchars($p[\'nombre\']) ?></option><?php endforeach; endif; ?>', $content);
file_put_contents($file, $content);

// Fix edit.php
$file = 'resources/views/catalog/products/edit.php';
$content = file_get_contents($file);
$content = str_replace('<!-- Categories loop here -->', '<?php if(!empty($categories)): foreach($categories as $c): ?><option value="<?= $c[\'id\'] ?>" <?= ($c[\'id\'] == $product[\'categoria_id\']) ? \'selected\' : \'\' ?>><?= htmlspecialchars($c[\'nombre\']) ?></option><?php endforeach; endif; ?>', $content);
$content = str_replace('<!-- Brands loop here -->', '<?php if(!empty($brands)): foreach($brands as $b): ?><option value="<?= $b[\'id\'] ?>" <?= ($b[\'id\'] == $product[\'marca_id\']) ? \'selected\' : \'\' ?>><?= htmlspecialchars($b[\'nombre\']) ?></option><?php endforeach; endif; ?>', $content);
$content = str_replace('<!-- Providers loop here -->', '<?php if(!empty($providers)): foreach($providers as $p): ?><option value="<?= $p[\'id\'] ?>" <?= ($p[\'id\'] == $product[\'proveedor_id\']) ? \'selected\' : \'\' ?>><?= htmlspecialchars($p[\'nombre\']) ?></option><?php endforeach; endif; ?>', $content);
file_put_contents($file, $content);

echo "Dropdowns fixed.\n";
