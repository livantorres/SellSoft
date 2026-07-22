<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <h1 class="h3 mb-0 text-gray-800">Gestión de Clientes</h1>
        <button type="button" class="btn btn-primary-app" data-bs-toggle="modal" data-bs-target="#clientModal" onclick="resetClientForm()">
            <i class="fas fa-plus"></i> Nuevo Cliente
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
                        <?php if(!empty($clients)): ?>
                            <?php foreach($clients as $client): ?>
                            <tr>
                                <td><?= htmlspecialchars((string)($client['id'] ?? '')) ?></td>
                                <td>
                                    
                                            <?= htmlspecialchars((string)($client['nombre'] ?? '')) ?>
                                            <?php if(isset($client['is_proveedor']) && $client['is_proveedor']): ?>
                                                <br><span class="badge bg-info text-dark" style="font-size: 0.65rem;">También Proveedor</span>
                                            <?php endif; ?>
                                        
                                </td>
                                <td><?= htmlspecialchars((string)($client['correo'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)($client['telefono'] ?? '')) ?></td>
                                <td>
                                    <small class="text-muted"><?= htmlspecialchars($client['tipo_doc'] ?? 'CC') ?></small><br>
                                    <b><?= htmlspecialchars($client['numero_doc'] ?? '') ?></b>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($client['ciudad_nombre'] ?? '') ?><br>
                                    <span class="text-muted"><?= htmlspecialchars($client['departamento_nombre'] ?? '') ?></span></small>
                                </td>
                                <td>
                                    <?php if(isset($client['activo']) && $client['activo'] == 1): ?>
                                        <span class="badge bg-success"><?= \SellSoft\Helpers\Lang::get('common.active') ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><?= \SellSoft\Helpers\Lang::get('common.inactive') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info text-white" onclick='editClient(<?= json_encode($client) ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick='deleteClient(<?= htmlspecialchars((string)($client['id'] ?? '')) ?>)'>
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
<div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="clientForm" action="/dashboard/clients" method="POST" >
          <?= \SellSoft\Helpers\Csrf::field() ?>
          <input type="hidden" name="id" id="clientId">
          <div class="modal-header">
            <h5 class="modal-title" id="clientModalLabel">Datos del Cliente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div class="mb-3">
                  <label for="providerName" class="form-label"><?= \SellSoft\Helpers\Lang::get('common.name') ?> *</label>
                  <input type="text" class="form-control" id="clientName" name="name" required>
              </div>
              
              
<div class="row mb-3">
                  <div class="col-md-4">
                      <label for="providerTipoDoc" class="form-label">Tipo Documento</label>
                      <select class="form-select" id="clientTipoDoc" name="tipo_documento">
                          <option value="NIT">NIT</option>
                          <option value="CC">Cédula (CC)</option>
                          <option value="CE">Cédula Extranjería (CE)</option>
                          <option value="RUT">RUT</option>
                          <option value="PASAPORTE">Pasaporte</option>
                      </select>
                  </div>
                  <div class="col-md-8">
                      <label for="providerNit" class="form-label">Número Documento</label>
                      <input type="text" class="form-control" id="clientNit" name="numero_doc">
                  </div>
              </div>
              

<div class="row mb-3">
                  <div class="col-md-6">
                      <label for="providerEmail" class="form-label"><?= \SellSoft\Helpers\Lang::get('common.email') ?></label>
                      <input type="email" class="form-control" id="clientEmail" name="email">
                  </div>
                  <div class="col-md-6">
                      <label for="providerPhone" class="form-label"><?= \SellSoft\Helpers\Lang::get('common.phone') ?></label>
                      <input type="text" class="form-control" id="clientPhone" name="phone">
                  </div>
              </div>
              

<div class="row mb-3">
                  <div class="col-md-6">
                      <label for="providerDepto" class="form-label">Departamento</label>
                      <select class="form-select" id="clientDepto" onchange="loadCities(this.value)">
                          <option value="">Seleccione...</option>
                          <?php if(!empty($departamentos)): foreach($departamentos as $d): ?>
                              <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                          <?php endforeach; endif; ?>
                      </select>
                  </div>
                  <div class="col-md-6">
                      <label for="providerCiudad" class="form-label">Ciudad / Municipio</label>
                      <select class="form-select" id="clientCiudad" name="ciudad_id">
                          <option value="">Seleccione...</option>
                      </select>
                  </div>
              </div>
              <div class="mb-3">
                  <label for="providerAddress" class="form-label"><?= \SellSoft\Helpers\Lang::get('common.address') ?></label>
                  <textarea class="form-control" id="clientAddress" name="address" rows="2"></textarea>
              </div>
              <div class="mb-3">
                  <label for="providerStatus" class="form-label"><?= \SellSoft\Helpers\Lang::get('common.status') ?></label>
                  <select class="form-select" id="clientStatus" name="status">
                      <option value="1"><?= \SellSoft\Helpers\Lang::get('common.active') ?></option>
                      <option value="0"><?= \SellSoft\Helpers\Lang::get('common.inactive') ?></option>
                  </select>
              </div>
          
              <div class="mb-3 form-check form-switch">
                  <input class="form-check-input" type="checkbox" role="switch" id="clientIsProveedor" name="is_proveedor" value="1">
                  <label class="form-check-label" for="providerIsCliente"><strong>Es también proveedor</strong> (Creará un registro en Proveedores vinculado a este Cliente)</label>
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
function editClient(client) {
    document.getElementById('clientId').value = client.id || '';
    document.getElementById('clientName').value = client.nombre || '';
    document.getElementById('clientTipoDoc').value = client.tipo_doc || 'CC';
    document.getElementById('clientNit').value = client.numero_doc || '';
    document.getElementById('clientEmail').value = client.correo || '';
    document.getElementById('clientPhone').value = client.telefono || '';
    document.getElementById('clientAddress').value = client.direccion || '';
    document.getElementById('clientStatus').value = (client.activo !== undefined) ? client.activo : 1;
    
    const isProveedorChk = document.getElementById('clientIsProveedor');
    isProveedorChk.checked = (client.is_proveedor == 1);
    if(client.is_proveedor == 1) {
        isProveedorChk.onclick = function() { return false; };
        isProveedorChk.style.opacity = 0.5;
        isProveedorChk.title = "Ya está vinculado como proveedor, no se puede desvincular.";
    } else {
        isProveedorChk.onclick = null;
        isProveedorChk.style.opacity = 1;
        isProveedorChk.title = "";
    }
    
    // Set dept and load cities
    if(client.departamento_id) {
        $('#clientDepto').val(client.departamento_id).trigger('change');
        // Wait for ajax to load cities then set value
        loadCities(client.departamento_id, client.ciudad_id);
    } else {
        $('#clientDepto').val('').trigger('change');
        $('#clientCiudad').empty().append('<option value="">Seleccione...</option>').trigger('change');
    }
    
    document.getElementById('clientModalLabel').innerText = 'Editar Cliente';
    var modal = new bootstrap.Modal(document.getElementById('clientModal'));
    modal.show();
}

async function loadCities(deptId, selectedCityId = null) {
    const citySelect = $('#clientCiudad');
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

function resetClientForm() {
    document.getElementById('clientForm').reset();
    $('#clientForm select').val('').trigger('change');
    document.getElementById('clientId').value = '';
    const isProveedorChk = document.getElementById('clientIsProveedor');
    isProveedorChk.onclick = null;
    isProveedorChk.style.opacity = 1;
    isProveedorChk.title = "";
    document.getElementById('clientModalLabel').innerText = 'Nuevo Cliente';
}

function deleteClient(id) {
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
                
                const res = await fetch('/dashboard/clients/delete', {
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

document.getElementById('clientForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const id = formData.get('id');
    const endpoint = id ? '/dashboard/clients/update' : '/dashboard/clients';
    
    
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
</script>