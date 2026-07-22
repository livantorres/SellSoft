<?php

$addKeys = [
    'catalog.brands.create' => 'Nueva Marca',
    'catalog.brands.edit' => 'Editar Marca',
    'catalog.brands.modal_title' => 'Marca',
    'catalog.brands.title' => 'Marcas',
    'catalog.categories.create' => 'Nueva Categoría',
    'catalog.categories.edit' => 'Editar Categoría',
    'catalog.categories.modal_title' => 'Categoría',
    'catalog.categories.title' => 'Categorías',
    'catalog.providers.create' => 'Nuevo Proveedor',
    'catalog.providers.edit' => 'Editar Proveedor',
    'catalog.providers.modal_title' => 'Proveedor',
    'catalog.providers.title' => 'Proveedores',
    'common.actions' => 'Acciones',
    'common.active' => 'Activo',
    'common.address' => 'Dirección',
    'common.cancel' => 'Cancelar',
    'common.delete' => 'Eliminar',
    'common.delete_confirm' => '¿Estás seguro de eliminar este registro?',
    'common.description' => 'Descripción',
    'common.email' => 'Correo Electrónico',
    'common.id' => 'ID',
    'common.inactive' => 'Inactivo',
    'common.name' => 'Nombre',
    'common.phone' => 'Teléfono',
    'common.save' => 'Guardar',
    'common.status' => 'Estado',
    'general.cancel' => 'Cancelar',
    'general.save' => 'Guardar',
    'general.update' => 'Actualizar',
    'messages.created_successfully' => 'Registro creado exitosamente.',
    'messages.deleted_successfully' => 'Registro eliminado exitosamente.',
    'messages.error_creating' => 'Error al crear el registro.',
    'messages.error_deleting' => 'Error al eliminar el registro.',
    'messages.error_updating' => 'Error al actualizar el registro.',
    'messages.updated_successfully' => 'Registro actualizado exitosamente.',
    'products.actions' => 'Acciones',
    'products.active' => 'Activo',
    'products.add_button' => 'Nuevo Producto',
    'products.brand' => 'Marca',
    'products.category' => 'Categoría',
    'products.confirm_delete' => '¿Estás seguro de eliminar este producto?',
    'products.create_title' => 'Crear Producto',
    'products.delete' => 'Eliminar',
    'products.description' => 'Descripción',
    'products.edit' => 'Editar',
    'products.edit_title' => 'Editar Producto',
    'products.id' => 'ID',
    'products.image' => 'Imagen',
    'products.inactive' => 'Inactivo',
    'products.initial_stock' => 'Stock Inicial',
    'products.leave_blank_for_no_change' => 'Dejar en blanco si no desea cambiarla',
    'products.list_title' => 'Catálogo de Productos',
    'products.min_stock' => 'Stock Mínimo',
    'products.name' => 'Nombre del Producto',
    'products.no_image' => 'Sin Imagen',
    'products.price' => 'Precio',
    'products.provider' => 'Proveedor',
    'products.purchase_price' => 'Precio de Compra',
    'products.sale_price' => 'Precio de Venta',
    'products.select_brand' => 'Seleccione una Marca',
    'products.select_category' => 'Seleccione una Categoría',
    'products.select_provider' => 'Seleccione un Proveedor',
    'products.selected_brand_placeholder' => 'Marca Actual',
    'products.selected_category_placeholder' => 'Categoría Actual',
    'products.selected_provider_placeholder' => 'Proveedor Actual',
    'products.sku' => 'Código SKU',
    'products.status' => 'Estado',
    'products.title' => 'Productos',
];

$file = 'resources/lang/es.php';
$content = file_get_contents($file);

$append = "\n    // Subagent Keys\n";
foreach ($addKeys as $k => $v) {
    $append .= "    '$k' => '$v',\n";
}

$content = preg_replace('/];\s*$/', $append . "];\n", $content);
file_put_contents($file, $content);
echo "Updated es.php\n";

