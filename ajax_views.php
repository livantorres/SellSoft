<?php
$views = [
    'resources/views/catalog/categories/index.php' => ['form' => 'categoryForm', 'delete' => 'deleteCategory', 'url' => '/dashboard/categories'],
    'resources/views/catalog/brands/index.php' => ['form' => 'brandForm', 'delete' => 'deleteBrand', 'url' => '/dashboard/brands'],
    'resources/views/catalog/providers/index.php' => ['form' => 'providerForm', 'delete' => 'deleteProvider', 'url' => '/dashboard/providers']
];

foreach ($views as $file => $config) {
    if (file_exists($file)) {
        $c = file_get_contents($file);
        
        // Add form submit listener
        $formId = $config['form'];
        $url = $config['url'];
        
        $js = "
document.getElementById('{$formId}').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const id = formData.get('id');
    const endpoint = id ? '{$url}/update' : '{$url}';
    
    try {
        const res = await fetch(endpoint, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error', 'error');
        }
    } catch (err) {
        showNotification('Connection error', 'error');
    }
});
</script>";
        // Replace </script> at the very end to inject our listener
        $c = preg_replace('/<\/script>\s*$/', $js, $c);
        
        // Rewrite delete function
        $deleteFnName = $config['delete'];
        $c = preg_replace('/function ' . $deleteFnName . '\(id\) \{.*?\n\}/s', "function {$deleteFnName}(id) {
    Swal.fire({
        title: '<?= \\SellSoft\\Helpers\\Lang::get(\'common.delete_confirm\') ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<?= \\SellSoft\\Helpers\\Lang::get(\'common.delete\') ?>',
        cancelButtonText: '<?= \\SellSoft\\Helpers\\Lang::get(\'common.cancel\') ?>'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const formData = new FormData();
                formData.append('id', id);
                // Also get CSRF from the page
                const csrfToken = document.querySelector('input[name=\"_csrf\"]').value;
                formData.append('_csrf', csrfToken);
                
                const res = await fetch('{$url}/delete', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Error', 'error');
                }
            } catch (err) {
                showNotification('Connection error', 'error');
            }
        }
    });
}", $c);

        file_put_contents($file, $c);
    }
}
echo "Auxiliary views AJAX updated.\n";
