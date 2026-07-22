<?php
use SellSoft\Helpers\Lang;
use SellSoft\Helpers\Csrf;
?>
<div class="container mt-5 mb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><?= Lang::get('products.create_title') ?></h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error->getMessage()) ?></div>
                <?php endif; ?>

                <form action="/dashboard/products" method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><?= Lang::get('products.name') ?> *</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= Lang::get('products.sku') ?></label>
                            <input type="text" name="codigo_sku" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label"><?= Lang::get('products.category') ?></label>
                            <select name="categoria_id" class="form-select">
                                <option value=""><?= Lang::get('products.select_category') ?></option>
                                <?php if(!empty($categories)): foreach($categories as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?= Lang::get('products.brand') ?></label>
                            <select name="marca_id" class="form-select">
                                <option value=""><?= Lang::get('products.select_brand') ?></option>
                                <?php if(!empty($brands)): foreach($brands as $b): ?><option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nombre']) ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><?= Lang::get('products.provider') ?></label>
                            <select name="proveedor_id" class="form-select">
                                <option value=""><?= Lang::get('products.select_provider') ?></option>
                                <?php if(!empty($providers)): foreach($providers as $p): ?><option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><?= Lang::get('products.purchase_price') ?></label>
                            <input type="number" step="0.01" name="precio_compra" class="form-control" value="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= Lang::get('products.sale_price') ?> *</label>
                            <input type="number" step="0.01" name="precio_venta" class="form-control" value="0.00" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><?= Lang::get('products.initial_stock') ?></label>
                            <input type="number" name="stock_inicial" class="form-control" value="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= Lang::get('products.min_stock') ?></label>
                            <input type="number" name="stock_minimo" class="form-control" value="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?= Lang::get('products.description') ?></label>
                        <textarea name="descripcion" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?= Lang::get('products.image') ?></label>
                        <input type="file" name="imagen_principal" class="form-control" accept="image/*">
                    </div>

                    <div class="mb-4 form-check">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" name="activo" value="1" class="form-check-input" id="activoCheck" checked>
                        <label class="form-check-label" for="activoCheck"><?= Lang::get('products.active') ?></label>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="/dashboard/products" class="btn btn-secondary-app me-2"><?= Lang::get('general.cancel') ?></a>
                        <button type="submit" class="btn btn-primary-app"><?= Lang::get('general.save') ?></button>
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
        const res = await fetch('/dashboard/products', {
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