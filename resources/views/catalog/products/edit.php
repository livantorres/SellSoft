<?php
use SellSoft\Helpers\Lang;
use SellSoft\Helpers\Csrf;
?>
<div class="container mt-5 mb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><?= Lang::get('products.edit_title') ?>: <?= htmlspecialchars($product['nombre']) ?></h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error->getMessage()) ?></div>
                <?php endif; ?>

                <form action="/dashboard/products/update" method="POST" enctype="multipart/form-data"><input type="hidden" name="id" value="<?= $product['id'] ?>">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><?= Lang::get('products.name') ?> *</label>
                            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($product['nombre']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= Lang::get('products.sku') ?></label>
                            <input type="text" name="codigo_sku" class="form-control" value="<?= htmlspecialchars($product['codigo_sku']) ?>">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label"><?= Lang::get('products.category') ?></label>
                            <select name="categoria_id" class="form-select">
                                <option value=""><?= Lang::get('products.select_category') ?></option>
                                <!-- Setting category id, dynamically populate options in production -->
                                <?php if($product['categoria_id']): ?>
                                    <option value="<?= $product['categoria_id'] ?>" selected><?= Lang::get('products.selected_category_placeholder') ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?= Lang::get('products.brand') ?></label>
                            <select name="marca_id" class="form-select">
                                <option value=""><?= Lang::get('products.select_brand') ?></option>
                                <?php if($product['marca_id']): ?>
                                    <option value="<?= $product['marca_id'] ?>" selected><?= Lang::get('products.selected_brand_placeholder') ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?= Lang::get('products.provider') ?></label>
                            <select name="proveedor_id" class="form-select">
                                <option value=""><?= Lang::get('products.select_provider') ?></option>
                                <?php if($product['proveedor_id']): ?>
                                    <option value="<?= $product['proveedor_id'] ?>" selected><?= Lang::get('products.selected_provider_placeholder') ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><?= Lang::get('products.purchase_price') ?></label>
                            <input type="number" step="0.01" name="precio_compra" class="form-control" value="<?= htmlspecialchars($product['precio_compra']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= Lang::get('products.sale_price') ?> *</label>
                            <input type="number" step="0.01" name="precio_venta" class="form-control" value="<?= htmlspecialchars($product['precio_venta']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?= Lang::get('products.description') ?></label>
                        <textarea name="descripcion" class="form-control" rows="3"><?= htmlspecialchars($product['descripcion']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?= Lang::get('products.image') ?></label>
                        <?php if (!empty($product['imagen_principal'])): ?>
                            <div class="mb-2">
                                <img src="/<?= htmlspecialchars($product['imagen_principal']) ?>" alt="Current Image" width="100">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="imagen_principal" class="form-control" accept="image/*">
                        <small class="text-muted"><?= Lang::get('products.leave_blank_for_no_change') ?></small>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" name="activo" value="1" class="form-check-input" id="activoCheck" <?= $product['activo'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="activoCheck"><?= Lang::get('products.active') ?></label>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="/dashboard/products" class="btn btn-secondary-app me-2"><?= Lang::get('general.cancel') ?></a>
                        <button type="submit" class="btn btn-warning"><?= Lang::get('general.update') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<script>
document.querySelector('form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    
    
    Swal.fire({
        title: 'Guardando...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    try {
        const res = await fetch('/dashboard/products/update', {
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
</script>