<?php

namespace SellSoft\Controllers;

use SellSoft\Models\Brand;
use SellSoft\Helpers\Lang;

use SellSoft\Core\Controller;
class BrandController extends Controller
{
    protected $brandModel;

    public function __construct()
    {
        $this->brandModel = new Brand();
    }

    public function index()
    {
        $brands = $this->brandModel->getAll();
        
        $this->view('catalog.brands.index', [
            'brands' => $brands,
            'title' => Lang::get('catalog.brands.title')
        ]);
    }

    public function store()
    {
        $data = ['nombre' => $_POST['name'] ?? '', 'descripcion' => $_POST['description'] ?? null, 'activo' => $_POST['status'] ?? 1];
        
        if ($this->brandModel->create($data)) {
            echo json_encode(['success' => true, 'message' => Lang::get('messages.created_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => Lang::get('messages.error_creating')]);
        }
    }

    public function update()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'ID is missing']); return; }
        $data = ['nombre' => $_POST['name'] ?? '', 'descripcion' => $_POST['description'] ?? null, 'activo' => $_POST['status'] ?? 1];
        if (empty($data)) {
            $data = json_decode(file_get_contents('php://input'), true) ?? []; if(isset($data['name'])) { $data = ['nombre' => $data['name'], 'descripcion' => $data['description'] ?? null, 'activo' => $data['status'] ?? 1]; }
        }

        if ($this->brandModel->update($id, $data)) {
            echo json_encode(['success' => true, 'message' => Lang::get('messages.updated_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => Lang::get('messages.error_updating')]);
        }
    }

    public function delete()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'ID is missing']); return; }
        if ($this->brandModel->delete($id)) {
            echo json_encode(['success' => true, 'message' => Lang::get('messages.deleted_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => Lang::get('messages.error_deleting')]);
        }
    }
}
