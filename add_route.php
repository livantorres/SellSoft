<?php
$file = 'config/routes.php';
$c = file_get_contents($file);

if (strpos($c, '/products/next-sku') === false) {
    $c = str_replace(
        "\$r->get('/products',          [\SellSoft\Controllers\ProductController::class, 'index']);",
        "\$r->get('/products',          [\SellSoft\Controllers\ProductController::class, 'index']);\n    \$r->get('/products/next-sku',  [\SellSoft\Controllers\ProductController::class, 'nextSku']);",
        $c
    );
    file_put_contents($file, $c);
    echo "Route /products/next-sku added.\n";
}
