<?php
$file = 'resources/views/catalog/products/index.php';
$c = file_get_contents($file);

// Replace native value assignment with jQuery val().trigger('change')
$c = str_replace(
    "document.getElementById('prodCat').value = prod.categoria_id || '';",
    "$('#prodCat').val(prod.categoria_id || '').trigger('change');",
    $c
);
$c = str_replace(
    "document.getElementById('prodBrand').value = prod.marca_id || '';",
    "$('#prodBrand').val(prod.marca_id || '').trigger('change');",
    $c
);
$c = str_replace(
    "document.getElementById('prodProv').value = prod.proveedor_id || '';",
    "$('#prodProv').val(prod.proveedor_id || '').trigger('change');",
    $c
);

// Also reset the selects when resetProductForm is called
$c = str_replace(
    "document.getElementById('productForm').reset();",
    "document.getElementById('productForm').reset();\n    $('#productForm select').val('').trigger('change');",
    $c
);

file_put_contents($file, $c);
echo "products/index.php updated for select2.\n";

// Apply same to categories, brands, providers if they have edit functions
$modules = ['categories', 'brands', 'providers'];
foreach ($modules as $mod) {
    $v = "resources/views/catalog/{$mod}/index.php";
    if (file_exists($v)) {
        $vc = file_get_contents($v);
        $vc = str_replace(
            "document.getElementById('categoryStatus').value = cat.activo;",
            "$('#categoryStatus').val(cat.activo).trigger('change');",
            $vc
        );
        $vc = str_replace(
            "document.getElementById('brandStatus').value = brand.activo;",
            "$('#brandStatus').val(brand.activo).trigger('change');",
            $vc
        );
        $vc = str_replace(
            "document.getElementById('providerStatus').value = prov.activo;",
            "$('#providerStatus').val(prov.activo).trigger('change');",
            $vc
        );
        file_put_contents($v, $vc);
    }
}
echo "Other modules updated for select2.\n";
