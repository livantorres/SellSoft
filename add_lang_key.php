<?php
function addToLang($file, $key, $value) {
    if (file_exists($file)) {
        $c = file_get_contents($file);
        // Find 'catalog' => ...
        if (strpos($c, $key) === false) {
            $c = preg_replace("/'catalog'\s*=>\s*'(.*?)',/", "'catalog' => '$1',\n    '$key' => '$value',", $c);
            file_put_contents($file, $c);
        }
    }
}

addToLang('resources/lang/es.php', 'catalog.products.title', 'Catálogo de Productos');
addToLang('resources/lang/en.php', 'catalog.products.title', 'Products Catalog');

echo "Lang updated.\n";
