<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= \SellSoft\Helpers\Lang::get('catalog.products.title') ?? 'Productos' ?></h1>
        <button type="button" class="btn btn-primary-app" data-bs-toggle="modal" data-bs-target="#productModal" onclick="resetProductForm()">
            <i class="fas fa-plus"></i> <?= \SellSoft\Helpers\Lang::get('products.add_button') ?>
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover data-table" id="productsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><?= \SellSoft\Helpers\Lang::get('products.image') ?></th>
                            <th><?= \SellSoft\Helpers\Lang::get('products.name') ?></th>
                            <th><?= \SellSoft\Helpers\Lang::get('products.sku') ?></th>
                            <th><?= \SellSoft\Helpers\Lang::get('products.price') ?></th>
                            <th><?= \SellSoft\Helpers\Lang::get('products.status') ?></th>
                            <th><?= \SellSoft\Helpers\Lang::get('products.actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($products) && is_array($products)): ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['id']) ?></td>
                                    <td>
                                        <?php if (!empty($product['imagen_principal'])): ?>
                                            <img src="/<?= htmlspecialchars($product['imagen_principal']) ?>" alt="Img" width="40" style="border-radius: 4px; object-fit: cover; aspect-ratio: 1/1; cursor: pointer;" onclick="viewProductGallery(<?= $product['id'] ?>, '<?= htmlspecialchars($product['nombre'], ENT_QUOTES) ?>')">
                                        <?php else: ?>
                                            <div style="width: 40px; height: 40px; background: var(--color-surface-3); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: var(--color-text-muted);">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-semibold"><?= htmlspecialchars($product['nombre']) ?></td>
                                    <td class="text-muted-app text-sm"><?= htmlspecialchars($product['codigo_sku']) ?></td>
                                    <td class="fw-bold tabular">$<?= number_format($product['precio_venta'], 2) ?></td>
                                    <td>
                                        <?php if(isset($product['activo']) && $product['activo'] == 1): ?>
                                            <span class="badge bg-success rounded-pill px-3"><?= \SellSoft\Helpers\Lang::get('common.active') ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger rounded-pill px-3"><?= \SellSoft\Helpers\Lang::get('common.inactive') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <!-- pasamos todo el json al onclick -->
                                        <button class="btn btn-sm btn-info text-white" onclick='editProduct(<?= json_encode($product) ?>)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?= $product['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Product -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form id="productForm" method="POST" enctype="multipart/form-data">
          <?= \SellSoft\Helpers\Csrf::field() ?>
          <input type="hidden" name="id" id="productId">
          <div class="modal-header border-bottom-0 pb-0">
            <h5 class="modal-title fw-bold" id="productModalLabel">Crear Producto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              
              <div class="row">
                  <!-- Columna Izquierda: Datos principales -->
                  <div class="col-md-8">
                      <div class="card mb-3 border-0 bg-transparent">
                          <div class="card-body p-0">
                              <h6 class="fw-bold mb-3 text-accent">Información Básica</h6>

                              <div class="row mb-3">
                                  <div class="col-md-4">
                                      <label class="form-label text-sm fw-semibold"><?= \SellSoft\Helpers\Lang::get('products.category') ?></label>
                                      <select name="categoria_id" id="prodCat" class="form-select">
                                          <option value=""><?= \SellSoft\Helpers\Lang::get('products.select_category') ?></option>
                                          <?php if(!empty($categories)): foreach($categories as $c): ?><option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option><?php endforeach; endif; ?>
                                      </select>
                                  </div>
                                  <div class="col-md-4">
                                      <label class="form-label text-sm fw-semibold"><?= \SellSoft\Helpers\Lang::get('products.brand') ?></label>
                                      <select name="marca_id" id="prodBrand" class="form-select">
                                          <option value=""><?= \SellSoft\Helpers\Lang::get('products.select_brand') ?></option>
                                          <?php if(!empty($brands)): foreach($brands as $b): ?><option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['nombre']) ?></option><?php endforeach; endif; ?>
                                      </select>
                                  </div>
                                  <div class="col-md-4">
                                      <label class="form-label text-sm fw-semibold"><?= \SellSoft\Helpers\Lang::get('products.provider') ?></label>
                                      <select name="proveedor_id" id="prodProv" class="form-select">
                                          <option value=""><?= \SellSoft\Helpers\Lang::get('products.select_provider') ?></option>
                                          <?php if(!empty($providers)): foreach($providers as $p): ?><option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option><?php endforeach; endif; ?>
                                      </select>
                                  </div>
                              </div>


                              <div class="row mb-3">
                                  <div class="col-md-8">
                                      <label class="form-label text-sm fw-semibold"><?= \SellSoft\Helpers\Lang::get('products.name') ?> *</label>
                                      <input type="text" name="nombre" id="prodNombre" class="form-control" required>
                                  </div>
                                  <div class="col-md-4">
                                      <label class="form-label text-sm fw-semibold"><?= \SellSoft\Helpers\Lang::get('products.sku') ?></label>
                                      <input type="text" name="codigo_sku" id="prodSku" class="form-control" placeholder="Generado automático">
                                  </div>
                              </div>
                              <div class="mb-3">
                                  <label class="form-label text-sm fw-semibold"><?= \SellSoft\Helpers\Lang::get('products.description') ?></label>
                                  <textarea name="descripcion" id="prodDesc" class="form-control" rows="3"></textarea>
                              </div>

                          </div>
                      </div>
                      
                      <hr class="my-4 opacity-25">
                      
                      <div class="card mb-3 border-0 bg-transparent">
                          <div class="card-body p-0">
                              <h6 class="fw-bold mb-3 text-success">Precios e Inventario</h6>
                              <div class="row mb-3">
                                  <div class="col-md-4">
                                      <label class="form-label text-sm fw-semibold"><?= \SellSoft\Helpers\Lang::get('products.purchase_price') ?></label>
                                      <div class="input-group">
                                          <span class="input-group-text bg-transparent text-muted">$</span>
                                          <input type="number" step="0.01" name="precio_compra" id="prodCompra" class="form-control" value="0.00" onkeyup="calcPrice()">
                                      </div>
                                  </div>
                                  <div class="col-md-4">
                                      <label class="form-label text-sm fw-semibold">Margen Ganancia (%)</label>
                                      <div class="input-group">
                                          <input type="number" step="0.1" id="prodMargin" class="form-control" value="30" onkeyup="calcPrice()">
                                          <span class="input-group-text bg-transparent text-muted">%</span>
                                      </div>
                                  </div>
                                  <div class="col-md-4">
                                      <label class="form-label text-sm fw-semibold text-primary"><?= \SellSoft\Helpers\Lang::get('products.sale_price') ?> *</label>
                                      <div class="input-group">
                                          <span class="input-group-text bg-transparent text-primary fw-bold">$</span>
                                          <input type="number" step="0.01" name="precio_venta" id="prodVenta" class="form-control fw-bold" value="0.00" required>
                                      </div>
                                  </div>
                              </div>
                              <div class="row mb-3">
                                  <div class="col-md-4">
                                      <label class="form-label text-sm fw-semibold"><?= \SellSoft\Helpers\Lang::get('products.initial_stock') ?></label>
                                      <input type="number" name="stock_inicial" id="prodStock" class="form-control" value="0">
                                  </div>
                                  <div class="col-md-4">
                                      <label class="form-label text-sm fw-semibold"><?= \SellSoft\Helpers\Lang::get('products.min_stock') ?></label>
                                      <input type="number" name="stock_minimo" id="prodStockMin" class="form-control" value="0">
                                  </div>
                                  <div class="col-md-4 d-flex align-items-end pb-2">
                                      <div class="form-check form-switch fs-5">
                                          <input type="hidden" name="activo" value="0">
                                          <input type="checkbox" name="activo" value="1" id="prodActivo" class="form-check-input" checked>
                                          <label class="form-check-label fs-6 ms-2 mt-1" for="prodActivo"><?= \SellSoft\Helpers\Lang::get('common.active') ?></label>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                  
                  <!-- Columna Derecha: Imágenes -->
                  <div class="col-md-4 border-start">
                      <div class="card mb-3 border-0 bg-transparent h-100">
                          <div class="card-body p-0 ps-3">
                              <h6 class="fw-bold mb-3 text-info">Galería de Imágenes</h6>
                              
                              <div class="mb-4">
                                  <label class="form-label text-sm fw-semibold">Subir Imágenes</label>
                                  <div class="upload-zone border border-dashed rounded p-4 text-center mb-2" style="background: var(--color-surface-2); border-color: var(--color-border) !important; border-style: dashed !important; cursor: pointer;" onclick="document.getElementById('prodGaleria').click()">
                                      <i class="fas fa-cloud-upload-alt fs-2 text-muted mb-2"></i>
                                      <p class="mb-0 text-sm text-muted">Clic para seleccionar fotos</p>
                                      <small class="text-muted" style="font-size: 0.7rem">La primera imagen será la principal</small>
                                  </div>
                                  <!-- File input oculto -->
                                  <input type="file" name="galeria[]" id="prodGaleria" class="d-none" multiple accept="image/*" onchange="handleFiles(this)">
                              </div>
                              
                              <div class="gallery-preview row g-2" id="galleryPreview">
                                  <!-- Previews aparecerán aquí -->
                              </div>
                              
                          </div>
                      </div>
                  </div>
              </div>
              
          </div>
          <div class="modal-footer border-top-0 pt-0">
            <button type="button" class="btn btn-secondary-app" data-bs-dismiss="modal"><?= \SellSoft\Helpers\Lang::get('common.cancel') ?></button>
            <button type="submit" class="btn btn-primary-app px-4"><i class="fas fa-save me-2"></i> <?= \SellSoft\Helpers\Lang::get('common.save') ?></button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Gallery Carousel -->
<div class="modal fade" id="galleryCarouselModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="background-color: var(--color-surface);">
      <div class="modal-header border-bottom">
        <h5 class="modal-title fw-bold" id="galleryCarouselTitle" style="color: var(--color-text);"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-3">
        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner rounded" id="carouselInner">
            <!-- Images here -->
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
let productImages = [];
let sortableGallery = null;

function resetProductForm() {
    document.getElementById('productForm').reset();
    $('#productForm select').val('').trigger('change');
    document.getElementById('productId').value = '';
    document.getElementById('productModalLabel').innerText = 'Crear Producto';
    productImages = [];
    renderGallery();
}

function editProduct(prod) {
    resetProductForm();
    document.getElementById('productModalLabel').innerText = 'Editar Producto';
    document.getElementById('productId').value = prod.id;
    document.getElementById('prodNombre').value = prod.nombre || '';
    document.getElementById('prodSku').value = prod.codigo_sku || '';
    document.getElementById('prodDesc').value = prod.descripcion || '';
    $('#prodCat').val(prod.categoria_id || '').trigger('change');
    $('#prodBrand').val(prod.marca_id || '').trigger('change');
    $('#prodProv').val(prod.proveedor_id || '').trigger('change');
    document.getElementById('prodCompra').value = prod.precio_compra || '0.00';
    document.getElementById('prodVenta').value = prod.precio_venta || '0.00';
    document.getElementById('prodStock').value = prod.stock_inicial || '0';
    document.getElementById('prodStockMin').value = prod.stock_minimo || '0';
    document.getElementById('prodActivo').checked = (prod.activo == 1);
    
    // Calcular margen inverso aprox
    let compra = parseFloat(prod.precio_compra) || 0;
    let venta = parseFloat(prod.precio_venta) || 0;
    if(compra > 0) {
        document.getElementById('prodMargin').value = ((venta - compra) / compra * 100).toFixed(1);
    }
    // Fetch gallery
    fetch('/dashboard/products/gallery?id=' + prod.id)
        .then(res => res.json())
        .then(data => {
            if(data.success && data.gallery) {
                productImages = data.gallery.map(img => ({
                    isExisting: true,
                    url: '/' + img.url_imagen,
                    path: img.url_imagen,
                    id: img.id
                }));
                renderGallery();
            }
        });
    
    var modal = new bootstrap.Modal(document.getElementById('productModal'));
    modal.show();
}

function calcPrice() {
    let compra = parseFloat(document.getElementById('prodCompra').value) || 0;
    let margin = parseFloat(document.getElementById('prodMargin').value) || 0;
    let venta = compra + (compra * (margin / 100));
    document.getElementById('prodVenta').value = venta.toFixed(2);
}

function handleFiles(input) {
    const files = input.files;
    if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
            productImages.push(files[i]);
        }
        renderGallery();
    }
    input.value = "";
}

function renderGallery() {
    const preview = document.getElementById('galleryPreview');
    preview.innerHTML = '';
    
    productImages.forEach((file, index) => {
        const col = document.createElement('div');
        col.className = 'col-4 col-md-4 position-relative gallery-item';
        col.dataset.index = index;
        
        const badge = index === 0 ? '<span class="badge bg-primary position-absolute top-0 start-0 m-1" style="font-size:0.6rem; z-index: 2;">Ppal</span>' : '';
        const delBtn = `<button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-0 rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 20px; height: 20px; z-index: 2;" onclick="removeImage(${index})" title="Eliminar"><i class="fas fa-times" style="font-size: 0.6rem;"></i></button>`;
        
        if (file.isExisting) {
            col.innerHTML = `
                <div class="border rounded overflow-hidden shadow-sm position-relative drag-handle" style="aspect-ratio: 1/1; cursor: grab;">
                    ${badge}
                    ${delBtn}
                    <img src="${file.url}" class="w-100 h-100" style="object-fit: cover;" onclick="previewFull('${file.url}')">
     <div class="position-absolute bottom-0 end-0 m-1 bg-dark text-white rounded-circle d-flex align-items-center justify-content-center opacity-75" style="width: 20px; height: 20px; pointer-events: none;"><i class="fas fa-search-plus" style="font-size: 0.6rem;"></i></div>
                </div>
            `;
            preview.appendChild(col);
        } else {
            const reader = new FileReader();
            reader.onload = function(e) {
                col.innerHTML = `
                    <div class="border rounded overflow-hidden shadow-sm position-relative drag-handle" style="aspect-ratio: 1/1; cursor: grab;">
                        ${badge}
                        ${delBtn}
                        <img src="${e.target.result}" class="w-100 h-100" style="object-fit: cover;" onclick="previewFull('${e.target.result}')">
         <div class="position-absolute bottom-0 end-0 m-1 bg-dark text-white rounded-circle d-flex align-items-center justify-content-center opacity-75" style="width: 20px; height: 20px; pointer-events: none;"><i class="fas fa-search-plus" style="font-size: 0.6rem;"></i></div>
                    </div>
                `;
                preview.appendChild(col);
            }
            reader.readAsDataURL(file);
        }
    });
    
    if (!sortableGallery) {
        sortableGallery = new Sortable(document.getElementById('galleryPreview'), {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'bg-light',
            onEnd: function (evt) {
                const item = productImages.splice(evt.oldIndex, 1)[0];
                productImages.splice(evt.newIndex, 0, item);
                renderGallery();
            }
        });
    }
}

function removeImage(index) {
    productImages.splice(index, 1);
    renderGallery();
}

document.getElementById('productForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    formData.delete('galeria[]');
    formData.delete('existing_gallery[]');
    productImages.forEach(item => {
        if (item.isExisting) {
            formData.append('existing_gallery[]', item.path);
        } else {
            formData.append('galeria[]', item);
        }
    });
    const id = formData.get('id');
    const endpoint = id ? '/dashboard/products/update' : '/dashboard/products';
    
    Swal.fire({
        title: 'Guardando Producto...',
        text: 'Subiendo imágenes y datos',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    try {
        const res = await fetch(endpoint, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            showNotification(data.message, 'success');
            if (typeof updateTableDynamic === 'function') {
                updateTableDynamic();
            } else {
                window.location.reload();
            }
        } else {
            showNotification(data.message || 'Error', 'error');
        }
    } catch (err) {
        showNotification('Connection error', 'error');
    }
});

function deleteProduct(id) {
    Swal.fire({
        title: '<?= \SellSoft\Helpers\Lang::get('products.confirm_delete') ?? '¿Estás seguro?' ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            Swal.fire({title: 'Eliminando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
            try {
                const formData = new FormData();
                formData.append('id', id);
                formData.append('_csrf', '<?= \SellSoft\Helpers\Csrf::token() ?>');
                
                const res = await fetch('/dashboard/products/delete', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showNotification(data.message, 'success');
                    if (typeof updateTableDynamic === 'function') updateTableDynamic(); else window.location.reload();
                } else {
                    showNotification(data.message || 'Error', 'error');
                }
            } catch (err) {
                showNotification('Connection error', 'error');
            }
        }
    });
}

// Evento para auto generar SKU al seleccionar categoria
$(document).ready(function() {
    $('#prodCat').on('change', async function() {
        const catId = $(this).val();
        const skuInput = document.getElementById('prodSku');
        
        // Solo auto-generar si estamos creando un nuevo producto y el SKU esta vacio
        if (catId && !document.getElementById('productId').value && !skuInput.value) {
            try {
                const res = await fetch('/dashboard/products/next-sku?categoria_id=' + catId);
                const data = await res.json();
                if (data.success && data.sku) {
                    skuInput.value = data.sku;
                }
            } catch(e) {
                console.error('Error fetching next SKU', e);
            }
        }
    });
});


function previewFull(src) {
    Swal.fire({
        imageUrl: src,
        imageAlt: "Vista Previa",
        showConfirmButton: false,
        showCloseButton: true,
        width: "auto",
        padding: "1rem",
        background: "transparent",
        backdrop: "rgba(0,0,0,0.85)"
    });
}

function viewProductGallery(id, name) {
    document.getElementById('galleryCarouselTitle').innerText = name;
    const inner = document.getElementById('carouselInner');
    inner.innerHTML = '<div class="text-center text-white my-5"><div class="spinner-border text-light" role="status"></div></div>';
    
    var modal = new bootstrap.Modal(document.getElementById('galleryCarouselModal'));
    modal.show();
    
    fetch('/dashboard/products/gallery?id=' + id)
        .then(res => res.json())
        .then(data => {
            inner.innerHTML = '';
            if(data.success && data.gallery && data.gallery.length > 0) {
                data.gallery.forEach((img, i) => {
                    const active = i === 0 ? 'active' : '';
                    inner.innerHTML += `
                        <div class="carousel-item ${active} bg-light">
                            <img src="/${img.url_imagen}" class="d-block mx-auto" style="height: 400px; width: auto; max-width: 100%; object-fit: contain;">
                        </div>
                    `;
                });
            } else {
                inner.innerHTML = '<div class="text-center my-5"><h4 style="color: var(--color-text-muted);">No hay imágenes</h4></div>';
            }
        })
        .catch(err => {
            inner.innerHTML = '<div class="text-center my-5"><h4 style="color: var(--color-text-muted);">Error cargando galería</h4></div>';
        });
}

</script>