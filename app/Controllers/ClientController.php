<?php

namespace SellSoft\Controllers;

use SellSoft\Models\Client;
use SellSoft\Helpers\Lang;
use SellSoft\Core\Controller;

class ClientController extends Controller
{
    protected $clientModel;

    public function __construct()
    {
        $this->clientModel = new Client();
    }

    public function index()
    {
        $clients = $this->clientModel->getAll();
        $db = \SellSoft\Core\Database::getInstance()->getPdo();
        $stmt = $db->query("SELECT id, nombre FROM departamentos WHERE pais_id = (SELECT id FROM paises WHERE nombre = 'Colombia' LIMIT 1) ORDER BY nombre ASC");
        $departamentos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $this->view('catalog.clients.index', [
            'clients' => $clients,
            'departamentos' => $departamentos,
            'title' => 'Clientes'
        ]);
    }

    public function store()
    {
        $data = [
            'nombre' => $_POST['name'] ?? '',
            'tipo_doc' => $_POST['tipo_documento'] ?? 'CC',
            'numero_doc' => $_POST['numero_doc'] ?? null,
            'correo' => $_POST['email'] ?? null,
            'telefono' => $_POST['phone'] ?? null,
            'direccion' => $_POST['address'] ?? null,
            'ciudad_id' => $_POST['ciudad_id'] ?? null,
            'is_proveedor' => isset($_POST['is_proveedor']) ? 1 : 0,
            'activo' => $_POST['status'] ?? 1
        ];

        // Obtener nombre de la ciudad
        $data['ciudad_nombre'] = '';
        if (!empty($data['ciudad_id'])) {
            $db = \SellSoft\Core\Database::getInstance()->getPdo();
            $stmtC = $db->prepare("SELECT nombre FROM ciudades WHERE id = ?");
            $stmtC->execute([$data['ciudad_id']]);
            $data['ciudad_nombre'] = $stmtC->fetchColumn();
        }

        $clientId = $this->clientModel->create($data);
        if ($clientId) {
            if ($data['is_proveedor']) {
                $this->syncProveedor($clientId, $data);
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
        
        $data = [
            'nombre' => $_POST['name'] ?? '',
            'tipo_doc' => $_POST['tipo_documento'] ?? 'CC',
            'numero_doc' => $_POST['numero_doc'] ?? null,
            'correo' => $_POST['email'] ?? null,
            'telefono' => $_POST['phone'] ?? null,
            'direccion' => $_POST['address'] ?? null,
            'ciudad_id' => $_POST['ciudad_id'] ?? null,
            'is_proveedor' => isset($_POST['is_proveedor']) ? 1 : 0,
            'activo' => $_POST['status'] ?? 1
        ];

        // Obtener nombre de la ciudad
        $data['ciudad_nombre'] = '';
        if (!empty($data['ciudad_id'])) {
            $db = \SellSoft\Core\Database::getInstance()->getPdo();
            $stmtC = $db->prepare("SELECT nombre FROM ciudades WHERE id = ?");
            $stmtC->execute([$data['ciudad_id']]);
            $data['ciudad_nombre'] = $stmtC->fetchColumn();
        }

        if ($this->clientModel->update($id, $data)) {
            if ($data['is_proveedor']) {
                $this->syncProveedor($id, $data);
            }
            echo json_encode(['success' => true, 'message' => Lang::get('messages.updated_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => Lang::get('messages.error_updating')]);
        }
    }

    private function syncProveedor($clientId, $data) {
        $db = \SellSoft\Core\Database::getInstance()->getPdo();
        
        // Map tipo_doc to proveedores (which is a generic string in proveedores, but typically NIT, CC, etc.)
        $tipoMap = [
            'NIT' => 'NIT',
            'CC' => 'CC',
            'CE' => 'CE',
            'PAS' => 'PASAPORTE',
            'NIT_EXT' => 'NIT_EXT'
        ];
        $tipoDoc = $tipoMap[$data['tipo_doc'] ?? 'CC'] ?? 'CC';
        
        // Check if there is already a proveedor for this client. Wait, we don't have cliente_id in proveedores.
        // We do have proveedor_id in clientes!
        $stmt = $db->prepare("SELECT proveedor_id FROM clientes WHERE id = ?");
        $stmt->execute([$clientId]);
        $proveedorId = $stmt->fetchColumn();
        
        if ($proveedorId) {
            // Update
            $update = $db->prepare("UPDATE proveedores SET nombre = ?, tipo_documento = ?, nit = ?, correo = ?, telefono = ?, direccion = ?, ciudad_id = ? WHERE id = ?");
            $update->execute([
                $data['nombre'] ?? '',
                $tipoDoc,
                $data['numero_doc'] ?? '',
                $data['correo'] ?? '',
                $data['telefono'] ?? '',
                $data['direccion'] ?? '',
                !empty($data['ciudad_id']) ? $data['ciudad_id'] : null,
                $proveedorId
            ]);
        } else {
            // Insert
            $insert = $db->prepare("INSERT INTO proveedores (nombre, tipo_documento, nit, correo, telefono, direccion, ciudad_id, is_cliente) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
            $insert->execute([
                $data['nombre'] ?? '',
                $tipoDoc,
                $data['numero_doc'] ?? '',
                $data['correo'] ?? '',
                $data['telefono'] ?? '',
                $data['direccion'] ?? '',
                !empty($data['ciudad_id']) ? $data['ciudad_id'] : null
            ]);
            $newProvId = $db->lastInsertId();
            $this->clientModel->updateProveedorId($clientId, $newProvId);
        }
    }

    public function delete()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'ID is missing']); return; }
        
        $db = \SellSoft\Core\Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT proveedor_id FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        $proveedorId = $stmt->fetchColumn();

        if ($this->clientModel->delete($id)) {
            if ($proveedorId) {
                $db->prepare("UPDATE proveedores SET is_cliente = 0 WHERE id = ?")->execute([$proveedorId]);
            }
            echo json_encode(['success' => true, 'message' => Lang::get('messages.deleted_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => Lang::get('messages.error_deleting')]);
        }
    }
}
