<?php

namespace SellSoft\Controllers;

use SellSoft\Models\Provider;
use SellSoft\Helpers\Lang;

use SellSoft\Core\Controller;
class ProviderController extends Controller
{
    protected $providerModel;

    public function __construct()
    {
        $this->providerModel = new Provider();
    }

    public function index()
    {
        $providers = $this->providerModel->getAll();
        
        $this->view('catalog.providers.index', [
            'providers' => $providers,
            'title' => Lang::get('catalog.providers.title')
        ]);
    }

    public function store()
    {
        $data = ['nombre' => $_POST['name'] ?? '', 'nit' => $_POST['nit'] ?? null, 'correo' => $_POST['email'] ?? null, 'telefono' => $_POST['phone'] ?? null, 'direccion' => $_POST['address'] ?? null, 'contacto' => $_POST['contact'] ?? null, 'activo' => $_POST['status'] ?? 1];
        
        if ($this->providerModel->create($data)) {
            echo json_encode(['success' => true, 'message' => Lang::get('messages.created_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => Lang::get('messages.error_creating')]);
        }
    }

    public function update()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'ID is missing']); return; }
        $data = ['nombre' => $_POST['name'] ?? '', 'nit' => $_POST['nit'] ?? null, 'correo' => $_POST['email'] ?? null, 'telefono' => $_POST['phone'] ?? null, 'direccion' => $_POST['address'] ?? null, 'contacto' => $_POST['contact'] ?? null, 'activo' => $_POST['status'] ?? 1];
        if (empty($data)) {
            $data = json_decode(file_get_contents('php://input'), true) ?? []; if(isset($data['name'])) { $data = ['nombre' => $data['name'], 'nit' => $data['nit'] ?? null, 'correo' => $data['email'] ?? null, 'telefono' => $data['phone'] ?? null, 'direccion' => $data['address'] ?? null, 'contacto' => $data['contact'] ?? null, 'activo' => $data['status'] ?? 1]; }
        }

        if ($this->providerModel->update($id, $data)) {
            echo json_encode(['success' => true, 'message' => Lang::get('messages.updated_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => Lang::get('messages.error_updating')]);
        }
    }

    public function delete()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'ID is missing']); return; }
        if ($this->providerModel->delete($id)) {
            echo json_encode(['success' => true, 'message' => Lang::get('messages.deleted_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => Lang::get('messages.error_deleting')]);
        }
    }
}
