<?php

namespace SellSoft\Controllers;

use SellSoft\Models\Product;
use SellSoft\Models\Category;
use SellSoft\Models\Brand;
use SellSoft\Models\Provider;

use SellSoft\Core\Controller;
class ProductController extends Controller
{
    protected $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function index()
    {
        $products = $this->productModel->getAll();
        $this->view('catalog.products.index', ['products' => $products]);
    }

    public function create() {
        $catModel = new Category();
        $brandModel = new Brand();
        $provModel = new Provider();
        $this->view('catalog.products.create', [
            'categories' => $catModel->getAll(),
            'brands' => $brandModel->getAll(),
            'providers' => $provModel->getAll()
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            
            // Handle image upload
            $data['imagen_principal'] = $this->handleImageUpload();

            try {
                $this->productModel->create($data);
                // Redirect on success
                echo json_encode(['success' => true, 'message' => \SellSoft\Helpers\Lang::get('messages.created_successfully')]);
                exit;
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }

    public function edit($id)
    {
        $product = $this->productModel->getById($id);
        if (!$product) {
            echo json_encode(['success' => true, 'message' => \SellSoft\Helpers\Lang::get('messages.created_successfully')]);
            exit;
        }
        
        $catModel = new Category();
        $brandModel = new Brand();
        $provModel = new Provider();
        $this->view('catalog.products.edit', [
            'product' => $product,
            'categories' => $catModel->getAll(),
            'brands' => $brandModel->getAll(),
            'providers' => $provModel->getAll()
        ]);
    }

    public function update()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'ID is missing']); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            
            $product = $this->productModel->getById($id);
            if (!$product) {
                echo json_encode(['success' => true, 'message' => \SellSoft\Helpers\Lang::get('messages.created_successfully')]);
                exit;
            }

            // Handle image upload
            $uploadedImage = $this->handleImageUpload();
            $data['imagen_principal'] = $uploadedImage ? $uploadedImage : $product['imagen_principal'];

            try {
                $this->productModel->update($id, $data);
                echo json_encode(['success' => true, 'message' => \SellSoft\Helpers\Lang::get('messages.created_successfully')]);
                exit;
            } catch (\Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }

    public function delete()
    {
        $id = $_POST['id'] ?? null;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'ID is missing']); return; }
        try {
            $this->productModel->delete($id);
        } catch (\Exception $e) {
            // Log or handle delete failure
        }
        echo json_encode(['success' => true, 'message' => \SellSoft\Helpers\Lang::get('messages.created_successfully')]);
        exit;
    }

    protected function handleImageUpload()
    {
        if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/storage/products/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = uniqid() . '_' . basename($_FILES['imagen_principal']['name']);
            $uploadFile = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['imagen_principal']['tmp_name'], $uploadFile)) {
                return 'storage/products/' . $fileName;
            }
        }
        return null;
    }
}
