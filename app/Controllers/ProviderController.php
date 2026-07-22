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
        $db = \SellSoft\Core\Database::getInstance()->getPdo();
        $stmt = $db->query("SELECT id, nombre FROM departamentos WHERE pais_id = (SELECT id FROM paises WHERE nombre = 'Colombia' LIMIT 1) ORDER BY nombre ASC");
        $departamentos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $this->view('catalog.providers.index', [
            'providers' => $providers,
            'departamentos' => $departamentos,
            'title' => Lang::get('catalog.providers.title')
        ]);
    }

    public function store()
    {
        $data = ['nombre' => $_POST['name'] ?? '', 'tipo_documento' => $_POST['tipo_documento'] ?? 'NIT', 'nit' => $_POST['nit'] ?? null, 'correo' => $_POST['email'] ?? null, 'telefono' => $_POST['phone'] ?? null, 'direccion' => $_POST['address'] ?? null, 'contacto' => $_POST['contact'] ?? null, 'ciudad_id' => $_POST['ciudad_id'] ?? null, 'activo' => $_POST['status'] ?? 1];
        
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
        $data = ['nombre' => $_POST['name'] ?? '', 'tipo_documento' => $_POST['tipo_documento'] ?? 'NIT', 'nit' => $_POST['nit'] ?? null, 'correo' => $_POST['email'] ?? null, 'telefono' => $_POST['phone'] ?? null, 'direccion' => $_POST['address'] ?? null, 'contacto' => $_POST['contact'] ?? null, 'ciudad_id' => $_POST['ciudad_id'] ?? null, 'activo' => $_POST['status'] ?? 1];
        if (empty($data)) {
            $data = json_decode(file_get_contents('php://input'), true) ?? []; if(isset($data['name'])) { $data = ['nombre' => $data['name'], 'tipo_documento' => $data['tipo_documento'] ?? 'NIT', 'nit' => $data['nit'] ?? null, 'correo' => $data['email'] ?? null, 'telefono' => $data['phone'] ?? null, 'direccion' => $data['address'] ?? null, 'contacto' => $data['contact'] ?? null, 'ciudad_id' => $data['ciudad_id'] ?? null, 'activo' => $data['status'] ?? 1]; }
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
