<?php
$views = [
    'resources/views/catalog/categories/index.php',
    'resources/views/catalog/brands/index.php',
    'resources/views/catalog/providers/index.php',
    'resources/views/catalog/products/index.php',
    'resources/views/catalog/products/create.php',
    'resources/views/catalog/products/edit.php'
];

foreach ($views as $view) {
    if (!file_exists($view)) continue;
    $content = file_get_contents($view);
    
    // Check if it has doctype
    if (stripos($content, '<!DOCTYPE html>') !== false) {
        // Find the position of <body>
        $bodyPos = stripos($content, '<body>');
        if ($bodyPos !== false) {
            $startCut = $bodyPos + 6;
            // Find the position of </body>
            $endBodyPos = strripos($content, '</body>');
            if ($endBodyPos !== false) {
                // Extract only what's inside body
                $inner = substr($content, $startCut, $endBodyPos - $startCut);
                
                // Add the PHP use statements if needed (Lang)
                $prefix = "<?php\nuse SellSoft\\Helpers\\Lang;\nuse SellSoft\\Helpers\\Csrf;\n?>\n";
                $newContent = $prefix . trim($inner);
                
                file_put_contents($view, $newContent);
                echo "Stripped $view\n";
            }
        }
    }
}
