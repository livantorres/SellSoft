<?php
// Add the global updateTableDynamic function to app.js
$jsFile = 'public/assets/js/app.js';
$js = file_get_contents($jsFile);
if (strpos($js, 'function updateTableDynamic') === false) {
    $fn = "
async function updateTableDynamic() {
    try {
        const res = await fetch(window.location.href);
        const html = await res.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newTbody = doc.querySelector('.data-table tbody');
        if (newTbody) {
            document.querySelector('.data-table tbody').innerHTML = newTbody.innerHTML;
        }
        const openModal = document.querySelector('.modal.show');
        if (openModal) {
            const modalInstance = bootstrap.Modal.getInstance(openModal);
            if (modalInstance) modalInstance.hide();
        }
        if (Swal.isVisible()) {
            Swal.close();
        }
    } catch(err) {
        console.error('Error:', err);
        window.location.reload();
    }
}
";
    file_put_contents($jsFile, $js . $fn);
}

// Replace setTimeout(() => window.location.reload(), 1000); with updateTableDynamic(); in the views
$views = [
    'resources/views/catalog/categories/index.php',
    'resources/views/catalog/brands/index.php',
    'resources/views/catalog/providers/index.php',
    'resources/views/catalog/products/index.php'
];

foreach ($views as $file) {
    if (file_exists($file)) {
        $c = file_get_contents($file);
        $c = str_replace("setTimeout(() => window.location.reload(), 1000);", "updateTableDynamic();", $c);
        file_put_contents($file, $c);
    }
}
echo "Dynamic table update logic applied.\n";
