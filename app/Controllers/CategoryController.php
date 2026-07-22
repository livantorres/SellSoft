<?php

namespace SellSoft\Controllers;

use SellSoft\Models\Category;
use SellSoft\Helpers\Lang;

use SellSoft\Core\Controller;
class CategoryController extends Controller
{
    protected $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    public function index()
    {
        $categories = $this->categoryModel->getAll();
        
        // Render the view. Assuming a generic view helper exists.
        $this->view('catalog.categories.index', [
            'categories' => $categories,
            'title' => Lang::get('catalog.categories.title')
        ]);
    }

    public function store()
    {
        $data = ['nombre' => $_POST['name'] ?? '', 'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['name'] ?? ''))), 'descripcion' => $_POST['description'] ?? null, 'activo' => $_POST['status'] ?? 1];
        
        if ($this->categoryModel->create($data)) {
            echo json_encode(['success' => true, 'message' => Lang::get('messages.created_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => Lang::get('messages.error_creating')]);
        }
    }

    public function update()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'ID is missing']); return; }
        // Parse request payload
        $data = ['nombre' => $_POST['name'] ?? '', 'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['name'] ?? ''))), 'descripcion' => $_POST['description'] ?? null, 'activo' => $_POST['status'] ?? 1];
        // If raw json was sent via PUT/PATCH:
        if (empty($data)) {
            $data = json_decode(file_get_contents('php://input'), true) ?? []; if(isset($data['name'])) { $data = ['nombre' => $data['name'], 'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name']))), 'descripcion' => $data['description'] ?? null, 'activo' => $data['status'] ?? 1]; }
        }

        if ($this->categoryModel->update($id, $data)) {
            echo json_encode(['success' => true, 'message' => Lang::get('messages.updated_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => Lang::get('messages.error_updating')]);
        }
    }

    public function delete()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'ID is missing']); return; }
        if ($this->categoryModel->delete($id)) {
            echo json_encode(['success' => true, 'message' => Lang::get('messages.deleted_successfully')]);
        } else {
            echo json_encode(['success' => false, 'message' => Lang::get('messages.error_deleting')]);
        }
    }
}
