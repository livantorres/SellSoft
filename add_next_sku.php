<?php
$file = 'app/Controllers/ProductController.php';
$c = file_get_contents($file);

$nextSkuMethod = '
    public function nextSku()
    {
        $catId = $_GET[\'categoria_id\'] ?? null;
        if (!$catId) {
            echo json_encode([\'success\' => false, \'message\' => \'Missing category ID\']);
            exit;
        }
        
        $catModel = new \SellSoft\Models\Category();
        $cat = $catModel->getById($catId);
        
        if (!$cat || empty($cat[\'abreviatura\'])) {
            echo json_encode([\'success\' => false, \'message\' => \'Category has no abbreviation\']);
            exit;
        }
        
        $abrev = strtoupper(trim($cat[\'abreviatura\']));
        
        // Find highest SKU starting with this abbreviation
        $db = \SellSoft\Core\Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT codigo_sku FROM productos WHERE codigo_sku LIKE :prefix ORDER BY id DESC LIMIT 1");
        $stmt->execute([\':prefix\' => $abrev . \'-\%\']);
        $last = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $nextNum = 1;
        if ($last && !empty($last[\'codigo_sku\'])) {
            $parts = explode(\'-\', $last[\'codigo_sku\']);
            if (count($parts) >= 2) {
                $lastNum = (int)end($parts);
                $nextNum = $lastNum + 1;
            }
        }
        
        $newSku = $abrev . \'-\' . str_pad($nextNum, 3, \'0\', STR_PAD_LEFT);
        
        echo json_encode([\'success\' => true, \'sku\' => $newSku]);
        exit;
    }
';

if (strpos($c, 'public function nextSku()') === false) {
    $c = preg_replace('/\}\s*$/', $nextSkuMethod . "\n}\n", $c);
    file_put_contents($file, $c);
    echo "ProductController nextSku method added.\n";
}

// Update products/index.php Javascript
$file = 'resources/views/catalog/products/index.php';
$c = file_get_contents($file);

$jsUpdate = "
// Evento para auto generar SKU al seleccionar categoria
$(document).ready(function() {
    $('#prodCat').on('change', async function() {
        const catId = $(this).val();
        const skuInput = document.getElementById('prodSku');
        
        // Solo auto-generar si estamos creando un nuevo producto y el SKU esta vacio
        if (catId && !document.getElementById('productId').value && !skuInput.value) {
            try {
                const res = await fetch('/dashboard/products/next-sku?categoria_id=' + catId);
                const data = await res.json();
                if (data.success && data.sku) {
                    skuInput.value = data.sku;
                }
            } catch(e) {
                console.error('Error fetching next SKU', e);
            }
        }
    });
});
";

if (strpos($c, 'next-sku?categoria_id=') === false) {
    $c = str_replace("</script>", $jsUpdate . "\n</script>", $c);
    file_put_contents($file, $c);
    echo "products/index.php updated with SKU auto-generator JS.\n";
}
