<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <h1 class="h3 mb-0 text-gray-800"><?= \SellSoft\Helpers\Lang::get('catalog.brands.title') ?></h1>
        <button type="button" class="btn btn-primary-app" data-bs-toggle="modal" data-bs-target="#brandModal" onclick="resetBrandForm()">
            <i class="fas fa-plus"></i> <?= \SellSoft\Helpers\Lang::get('catalog.brands.create') ?>
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
                            <th><?= \SellSoft\Helpers\Lang::get('common.description') ?></th>
                            <th><?= \SellSoft\Helpers\Lang::get('common.status') ?></th>
                            <th><?= \SellSoft\Helpers\Lang::get('common.actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($brands)): ?>
                            <?php foreach($brands as $brand): ?>
                            <tr>
                                <td><?= htmlspecialchars((string)($brand['id'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)($brand['nombre'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)($brand['descripcion'] ?? '')) ?></td>
                                <td>
                                    <?php if(isset($brand['activo']) && $brand['activo'] == 1): ?>
                                        <span class="badge bg-success"><?= \SellSoft\Helpers\Lang::get('common.active') ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><?= \SellSoft\Helpers\Lang::get('common.inactive') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info text-white" onclick='editBrand(<?= json_encode($brand) ?>)'>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick='deleteBrand(<?= htmlspecialchars((string)($brand['id'] ?? '')) ?>)'>
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
<div class="modal fade" id="brandModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="brandForm" action="/dashboard/brands" method="POST">
          <?= \SellSoft\Helpers\Csrf::field() ?>
          <input type="hidden" name="id" id="brandId">
          <div class="modal-header">
            <h5 class="modal-title" id="brandModalLabel"><?= \SellSoft\Helpers\Lang::get('catalog.brands.modal_title') ?></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <div class="mb-3">
                  <label for="brandName" class="form-label"><?= \SellSoft\Helpers\Lang::get('common.name') ?></label>
                  <input type="text" class="form-control" id="brandName" name="name" required>
              </div>
              <div class="mb-3">
                  <label for="brandDescription" class="form-label"><?= \SellSoft\Helpers\Lang::get('common.description') ?></label>
                  <textarea class="form-control" id="brandDescription" name="description" rows="3"></textarea>
              </div>
              <div class="mb-3">
                  <label for="brandStatus" class="form-label"><?= \SellSoft\Helpers\Lang::get('common.status') ?></label>
                  <select class="form-select" id="brandStatus" name="status">
                      <option value="1"><?= \SellSoft\Helpers\Lang::get('common.active') ?></option>
                      <option value="0"><?= \SellSoft\Helpers\Lang::get('common.inactive') ?></option>
                  </select>
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
function resetBrandForm() {
    document.getElementById('brandForm').reset();
    document.getElementById('brandId').value = '';
    document.getElementById('brandModalLabel').innerText = '<?= \SellSoft\Helpers\Lang::get('catalog.brands.create') ?>';
}

function editBrand(brand) {
    document.getElementById('brandId').value = brand.id || '';
    document.getElementById('brandName').value = brand.nombre || '';
    document.getElementById('brandDescription').value = brand.descripcion || '';
    document.getElementById('brandStatus').value = (brand.activo !== undefined) ? brand.activo : 1;
    document.getElementById('brandModalLabel').innerText = '<?= \SellSoft\Helpers\Lang::get('catalog.brands.edit') ?>';
    var modal = new bootstrap.Modal(document.getElementById('brandModal'));
    modal.show();
}

function deleteBrand(id) {
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
                
                const res = await fetch('/dashboard/brands/delete', {
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

document.getElementById('brandForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const id = formData.get('id');
    const endpoint = id ? '/dashboard/brands/update' : '/dashboard/brands';
    
    
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