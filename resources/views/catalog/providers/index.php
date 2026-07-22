<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <h1 class="h3 mb-0 text-gray-800"><?= \SellSoft\Helpers\Lang::get('catalog.providers.title') ?></h1>
        <button type="button" class="btn btn-primary-app" data-bs-toggle="modal" data-bs-target="#providerModal" onclick="resetProviderForm()">
            <i class="fas fa-plus"></i> <?= \SellSoft\Helpers\Lang::get('catalog.providers.create') ?>
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover data-table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th><?= \SellSoft\Helpers\Lang::get('common.id') ?></th>
                            <th><?= \SellSoft\Helpers\Lang::get('common.name') ?></th>
                            <th><?= \SellSoft\Helpers\Lang::get('common.email') ?></th>
                            <th><?= \SellSoft\Helpers\Lang::get('common.phone') ?></th>
                            <th>Documento</th><th>Ubicación</th><th><?= \SellSoft\Helpers\Lang::get('common.status') ?></th>
                            <th><?= \SellSoft\Helpers\Lang::get('common.actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($providers)): ?>
                            <?php foreach($providers as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars((string)($p['id'] ?? '')) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if(!empty($p['imagen'])): ?>
                                            <img src="<?= APP_URL . htmlspecialchars($p['imagen']) ?>" alt="Logo" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white me-2" style="width: 40px; height: 40px;">
                                                <i class="fas fa-building"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <?= htmlspecialchars((string)($p['nombre'] ?? '')) ?>
                                            <?php if(isset($p['is_cliente']) && $p['is_cliente']): ?>
                                                <br><span class="badge bg-info text-dark" style="font-size: 0.65rem;">También Cliente</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars((string)($p['correo'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)($p['telefono'] ?? '')) ?></td>
                                <td>
                                    <small class="text-muted"><?= htmlspecialchars($p['tipo_documento'] ?? 'NIT') ?></small><br>
                                    <b><?= htmlspecialchars($p['nit'] ?? '') ?></b>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($p['ciudad_nombre'] ?? '') ?><br>
                                    <span class="text-muted"><?= htmlspecialchars($p['departamento_nombre'] ?? '') ?></span></small>
                                </td>
                                <td>
                                    <?php if(isset($p['activo']) && $p['activo'] == 1): ?>
                                        <span class="badge bg-success"><?= \SellSoft\Helpers\Lang::get('common.active') ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><?= \SellSoft\Helpers\Lang::get('common.inactive') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info text-white" onclick='editProvider(<?= json_encode($p) ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick='deleteProvider(<?= htmlspecialchars((string)($p['id'] ?? '')) ?>)'>
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

<!-- Modal -->
<div class="modal fade" id="providerModal" tabindex="-1" aria-labelledby="providerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="providerForm" action="/dashboard/providers" method="POST" enctype="multipart/form-data">
          <?= \SellSoft\Helpers\Csrf::field() ?>
          <input type="hidden" name="id" id="providerId">
          <div class="modal-header">
            <h5 class="modal-title" id="providerModalLabel"><?= \SellSoft\Helpers\Lang::get('catalog.providers.modal_title') ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div class="mb-3">
                  <label for="providerName" class="form-label"><?= \SellSoft\Helpers\Lang::get('common.name') ?> *</label>
                  <input type="text" class="form-control" id="providerName" name="name" required>
              </div>
              
              <div class="mb-3">
                  <label for="providerImagen" class="form-label">Logo / Fotografía (Opcional)</label>
                  <div id="providerImagePreviewContainer" style="display: none; margin-bottom: 10px;">
                      <img id="providerImagePreview" src="" alt="Vista previa" class="img-thumbnail" style="max-height: 100px; cursor: pointer;" onclick="previewImageLarge(this.src)">
                      <div class="text-muted" style="font-size: 0.8rem;">Clic en la imagen para ampliar</div>
                  </div>
                  <input type="file" class="form-control" id="providerImagen" name="imagen" accept="image/*" onchange="previewImageFile(this)">
              </div>
<div class="row mb-3">
                  <div class="col-md-4">
                      <label for="providerTipoDoc" class="form-label">Tipo Documento</label>
                      <select class="form-select" id="providerTipoDoc" name="tipo_documento">
                          <option value="NIT">NIT</option>
                          <option value="CC">Cédula (CC)</option>
                          <option value="CE">Cédula Extranjería (CE)</option>
                          <option value="RUT">RUT</option>
                          <option value="PASAPORTE">Pasaporte</option>
                      </select>
                  </div>
                  <div class="col-md-8">
                      <label for="providerNit" class="form-label">Número Documento</label>
                      <input type="text" class="form-control" id="providerNit" name="nit">
                  </div>
              </div>
              

<div class="row mb-3">
                  <div class="col-md-6">
                      <label for="providerEmail" class="form-label"><?= \SellSoft\Helpers\Lang::get('common.email') ?></label>
                      <input type="email" class="form-control" id="providerEmail" name="email">
                  </div>
                  <div class="col-md-6">
                      <label for="providerPhone" class="form-label"><?= \SellSoft\Helpers\Lang::get('common.phone') ?></label>
                      <input type="text" class="form-control" id="providerPhone" name="phone">
                  </div>
              </div>
              

<div class="row mb-3">
                  <div class="col-md-6">
                      <label for="providerDepto" class="form-label">Departamento</label>
                      <select class="form-select" id="providerDepto" onchange="loadCities(this.value)">
                          <option value="">Seleccione...</option>
                          <?php if(!empty($departamentos)): foreach($departamentos as $d): ?>
                              <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                          <?php endforeach; endif; ?>
                      </select>
                  </div>
                  <div class="col-md-6">
                      <label for="providerCiudad" class="form-label">Ciudad / Municipio</label>
                      <select class="form-select" id="providerCiudad" name="ciudad_id">
                          <option value="">Seleccione...</option>
                      </select>
                  </div>
              </div>
              <div class="mb-3">
                  <label for="providerAddress" class="form-label"><?= \SellSoft\Helpers\Lang::get('common.address') ?></label>
                  <textarea class="form-control" id="providerAddress" name="address" rows="2"></textarea>
              </div>
              <div class="mb-3">
                  <label for="providerStatus" class="form-label"><?= \SellSoft\Helpers\Lang::get('common.status') ?></label>
                  <select class="form-select" id="providerStatus" name="status">
                      <option value="1"><?= \SellSoft\Helpers\Lang::get('common.active') ?></option>
                      <option value="0"><?= \SellSoft\Helpers\Lang::get('common.inactive') ?></option>
                  </select>
              </div>
          
              <div class="mb-3 form-check form-switch">
                  <input class="form-check-input" type="checkbox" role="switch" id="providerIsCliente" name="is_cliente" value="1">
                  <label class="form-check-label" for="providerIsCliente"><strong>Es también cliente</strong> (Creará un registro en Clientes vinculado a este Proveedor)</label>
              </div>
</div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary-app" data-bs-dismiss="modal"><?= \SellSoft\Helpers\Lang::get('common.cancel') ?></button>
            <button type="submit" class="btn btn-primary-app"><?= \SellSoft\Helpers\Lang::get('common.save') ?></button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
function editProvider(provider) {
    document.getElementById('providerId').value = provider.id || '';
    document.getElementById('providerName').value = provider.nombre || '';
    document.getElementById('providerTipoDoc').value = provider.tipo_documento || 'NIT';
    document.getElementById('providerNit').value = provider.nit || '';
    document.getElementById('providerEmail').value = provider.correo || '';
    document.getElementById('providerPhone').value = provider.telefono || '';
    document.getElementById('providerAddress').value = provider.direccion || '';
    
    // Preview image
    var previewContainer = document.getElementById('providerImagePreviewContainer');
    var previewImg = document.getElementById('providerImagePreview');
    if(provider.imagen) {
        previewImg.src = '<?= APP_URL ?>' + provider.imagen;
        previewContainer.style.display = 'block';
    } else {
        previewImg.src = '';
        previewContainer.style.display = 'none';
    }
    document.getElementById('providerStatus').value = (provider.activo !== undefined) ? provider.activo : 1;
    const isClienteChk = document.getElementById('providerIsCliente');
    isClienteChk.checked = (provider.is_cliente == 1);
    if(provider.is_cliente == 1) {
        isClienteChk.onclick = function() { return false; };
        isClienteChk.style.opacity = 0.5;
        isClienteChk.title = "Ya está vinculado como cliente, no se puede desvincular.";
    } else {
        isClienteChk.onclick = null;
        isClienteChk.style.opacity = 1;
        isClienteChk.title = "";
    }
    
    // Set dept and load cities
    if(provider.departamento_id) {
        $('#providerDepto').val(provider.departamento_id).trigger('change');
        // Wait for ajax to load cities then set value
        loadCities(provider.departamento_id, provider.ciudad_id);
    } else {
        $('#providerDepto').val('').trigger('change');
        $('#providerCiudad').empty().append('<option value="">Seleccione...</option>').trigger('change');
    }
    
    document.getElementById('providerModalLabel').innerText = '<?= \SellSoft\Helpers\Lang::get('catalog.providers.edit') ?>';
    var modal = new bootstrap.Modal(document.getElementById('providerModal'));
    modal.show();
}

async function loadCities(deptId, selectedCityId = null) {
    const citySelect = $('#providerCiudad');
    citySelect.empty().append('<option value="">Cargando...</option>');
    
    if(!deptId) {
        citySelect.empty().append('<option value="">Seleccione...</option>');
        return;
    }
    
    try {
        const res = await fetch('/api/ciudades?departamento_id=' + deptId);
        const json = await res.json();
        
        citySelect.empty().append('<option value="">Seleccione...</option>');
        if(json.success && json.data) {
            json.data.forEach(c => {
                const option = new Option(c.nombre, c.id, false, false);
                citySelect.append(option);
            });
            if(selectedCityId) {
                citySelect.val(selectedCityId).trigger('change');
            }
        }
    } catch(err) {
        console.error(err);
        citySelect.empty().append('<option value="">Error al cargar</option>');
    }
}

function resetProviderForm() {
    document.getElementById('providerForm').reset();
    $('#providerForm select').val('').trigger('change');
    document.getElementById('providerId').value = '';
    document.getElementById('providerImagePreviewContainer').style.display = 'none';
    document.getElementById('providerImagePreview').src = '';
    const isClienteChk = document.getElementById('providerIsCliente');
    isClienteChk.onclick = null;
    isClienteChk.style.opacity = 1;
    isClienteChk.title = "";
    document.getElementById('providerModalLabel').innerText = '<?= \SellSoft\Helpers\Lang::get('catalog.providers.create') ?>';
}

function deleteProvider(id) {
    Swal.fire({
        title: '<?= \SellSoft\Helpers\Lang::get('common.delete_confirm') ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '<?= \SellSoft\Helpers\Lang::get('common.delete') ?>',
        cancelButtonText: '<?= \SellSoft\Helpers\Lang::get('common.cancel') ?>'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const formData = new FormData();
                formData.append('id', id);
                // Also get CSRF from the page
                const csrfToken = document.querySelector('input[name="_csrf"]').value;
                formData.append('_csrf', csrfToken);
                
                const res = await fetch('/dashboard/providers/delete', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showNotification(data.message, 'success');
                    updateTableDynamic();
                } else {
                    showNotification(data.message || 'Error', 'error');
                }
            } catch (err) {
                showNotification('Connection error', 'error');
            }
        }
    });
}

document.getElementById('providerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const id = formData.get('id');
    const endpoint = id ? '/dashboard/providers/update' : '/dashboard/providers';
    
    
    Swal.fire({
        title: 'Guardando...',
        text: 'Por favor espera',
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
            updateTableDynamic();
        } else {
            showNotification(data.message || 'Error', 'error');
        }
    } catch (err) {
        showNotification('Connection error', 'error');
    }
});

function previewImageLarge(src) {
    Swal.fire({
        imageUrl: src,
        imageAlt: "Vista previa",
        showConfirmButton: false,
        showCloseButton: true,
        customClass: {
            image: "img-fluid"
        }
    });
}

function previewImageFile(input) {
    var previewContainer = document.getElementById("providerImagePreviewContainer");
    var previewImg = document.getElementById("providerImagePreview");
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.style.display = "block";
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = "none";
        previewImg.src = "";
    }
}

</script>