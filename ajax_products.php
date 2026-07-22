<?php
// Fix products/index.php
$file = 'resources/views/catalog/products/index.php';
$c = file_get_contents($file);
$js = "
<script>
function deleteProduct(id) {
    Swal.fire({
        title: '<?= \\SellSoft\\Helpers\\Lang::get(\'products.confirm_delete\') ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<?= \\SellSoft\\Helpers\\Lang::get(\'products.delete\') ?>',
        cancelButtonText: '<?= \\SellSoft\\Helpers\\Lang::get(\'general.cancel\') ?>'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const formData = new FormData();
                formData.append('id', id);
                formData.append('_csrf', '<?= \\SellSoft\\Helpers\\Csrf::token() ?>');
                
                const res = await fetch('/dashboard/products/delete', {
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
}
</script>
";
// Replace the old HTML forms for delete with a button that calls deleteProduct
$c = preg_replace('/<form action="\/dashboard\/products\/delete".*?<\/form>/s', '<button class="btn btn-sm btn-danger" onclick="deleteProduct(<?= $product[\'id\'] ?>)"><?= Lang::get(\'products.delete\') ?></button>', $c);
$c = str_replace('</body>', $js . '</body>', $c);
file_put_contents($file, $c);


// Fix products/create.php and products/edit.php
$views = [
    'resources/views/catalog/products/create.php' => '/dashboard/products',
    'resources/views/catalog/products/edit.php' => '/dashboard/products/update'
];
foreach ($views as $file => $url) {
    if (file_exists($file)) {
        $c = file_get_contents($file);
        $js = "
<script>
document.querySelector('form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    
    try {
        const res = await fetch('{$url}', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.href = '/dashboard/products', 1000);
        } else {
            showNotification(data.message || 'Error', 'error');
        }
    } catch (err) {
        showNotification('Connection error', 'error');
    }
});
</script>";
        $c = $c . $js;
        file_put_contents($file, $c);
    }
}

echo "Products AJAX updated.\n";
