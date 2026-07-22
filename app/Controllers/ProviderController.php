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
        $data = $_POST;
        
        // Handle file upload
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('prov_') . '.' . $ext;
            $dest = __DIR__ . '/../../public/uploads/providers/';
            if (!is_dir($dest)) mkdir($dest, 0777, true);
            move_uploaded_file($_FILES['imagen']['tmp_name'], $dest . $filename);
            $data['imagen'] = '/uploads/providers/' . $filename;
        }

        $data['is_cliente'] = isset($_POST['is_cliente']) ? 1 : 0;
        
        $provId = $this->providerModel->create($data);
        if ($provId) {
            if ($data['is_cliente']) {
                $this->syncCliente($provId, $data);
            }
            echo json_encode(['success' => true, 'message' => Lang::get('messages.created_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => Lang::get('messages.error_creating')]);
        }
    }

    public function update()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'ID is missing']); return; }
        
        $data = $_POST;
        if (empty($data)) {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
        }
        
        // Handle file upload
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('prov_') . '.' . $ext;
            $dest = __DIR__ . '/../../public/uploads/providers/';
            if (!is_dir($dest)) mkdir($dest, 0777, true);
            move_uploaded_file($_FILES['imagen']['tmp_name'], $dest . $filename);
            $data['imagen'] = '/uploads/providers/' . $filename;
        }

        $data['is_cliente'] = isset($_POST['is_cliente']) ? 1 : 0;

        if ($this->providerModel->update($id, $data)) {
            if ($data['is_cliente']) {
                $this->syncCliente($id, $data);
            }
            echo json_encode(['success' => true, 'message' => Lang::get('messages.updated_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => Lang::get('messages.error_updating')]);
        }
    }

    private function syncCliente($providerId, $data) {
        $db = \SellSoft\Core\Database::getInstance()->getPdo();
        
        // Map tipo_documento to clientes ENUM
        $tipoMap = [
            'NIT' => 'NIT',
            'CC' => 'CC',
            'CE' => 'CE',
            'PASAPORTE' => 'PAS',
            'RUT' => 'NIT'
        ];
        $tipoDoc = $tipoMap[$data['tipo_documento'] ?? 'NIT'] ?? 'NIT';
        
        // Find existing client linked to this provider
        $stmt = $db->prepare("SELECT id FROM clientes WHERE proveedor_id = ?");
        $stmt->execute([$providerId]);
        $clienteId = $stmt->fetchColumn();
        
        $nombre = $data['name'] ?? $data['nombre'] ?? '';
        
        if ($clienteId) {
            // Update
            $update = $db->prepare("UPDATE clientes SET nombre = ?, tipo_doc = ?, numero_doc = ?, correo = ?, telefono = ?, direccion = ? WHERE id = ?");
            $update->execute([
                $nombre,
                $tipoDoc,
                $data['nit'] ?? '',
                $data['email'] ?? $data['correo'] ?? '',
                $data['phone'] ?? $data['telefono'] ?? '',
                $data['address'] ?? $data['direccion'] ?? '',
                $clienteId
            ]);
        } else {
            // Insert
            $insert = $db->prepare("INSERT INTO clientes (proveedor_id, nombre, tipo_doc, numero_doc, correo, telefono, direccion) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert->execute([
                $providerId,
                $nombre,
                $tipoDoc,
                $data['nit'] ?? '',
                $data['email'] ?? $data['correo'] ?? '',
                $data['phone'] ?? $data['telefono'] ?? '',
                $data['address'] ?? $data['direccion'] ?? ''
            ]);
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
