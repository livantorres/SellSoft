<?php
$views = [
    'resources/views/catalog/categories/index.php',
    'resources/views/catalog/brands/index.php',
    'resources/views/catalog/providers/index.php',
    'resources/views/catalog/products/create.php',
    'resources/views/catalog/products/edit.php'
];

foreach ($views as $file) {
    if (file_exists($file)) {
        $c = file_get_contents($file);
        
        $loaderCode = "
    Swal.fire({
        title: 'Guardando...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    try {
        const res = await fetch";
        
        $c = preg_replace('/try \{\s*const res = await fetch/', $loaderCode, $c);
        file_put_contents($file, $c);
    }
}
echo "Added loaders to form submissions.\n";
