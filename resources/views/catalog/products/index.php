<?php
use SellSoft\Helpers\Lang;
use SellSoft\Helpers\Csrf;
?>
<div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><?= Lang::get('products.list_title') ?></h2>
            <a href="/dashboard/products/create" class="btn btn-primary-app"><?= Lang::get('products.add_button') ?></a>
        </div>

        <table id="productsTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?= Lang::get('products.id') ?></th>
                    <th><?= Lang::get('products.image') ?></th>
                    <th><?= Lang::get('products.name') ?></th>
                    <th><?= Lang::get('products.sku') ?></th>
                    <th><?= Lang::get('products.price') ?></th>
                    <th><?= Lang::get('products.status') ?></th>
                    <th><?= Lang::get('products.actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($products) && is_array($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['id']) ?></td>
                            <td>
                                <?php if (!empty($product['imagen_principal'])): ?>
                                    <img src="/<?= htmlspecialchars($product['imagen_principal']) ?>" alt="Img" width="50">
                                <?php else: ?>
                                    <span><?= Lang::get('products.no_image') ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($product['nombre']) ?></td>
                            <td><?= htmlspecialchars($product['codigo_sku']) ?></td>
                            <td><?= htmlspecialchars($product['precio_venta']) ?></td>
                            <td>
                                <?= $product['activo'] ? Lang::get('products.active') : Lang::get('products.inactive') ?>
                            </td>
                            <td>
                                <a href="/dashboard/products/<?= $product['id'] ?>/edit" class="btn btn-sm btn-warning text-decoration-none"><?= Lang::get('products.edit') ?></a>
                                <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?= $product['id'] ?>)"><?= Lang::get('products.delete') ?></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#productsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
                }
            });
        });
    </script>