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
        $categories = (new Category())->getAll();
        $brands = (new Brand())->getAll();
        $providers = (new Provider())->getAll();
        
        $this->view('catalog.products.index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'providers' => $providers,
            'title' => Lang::get('catalog.products.title')
        ]);
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
            $galleryUrls = $this->handleGalleryUpload();
            if (!empty($galleryUrls)) {
                $data['imagen_principal'] = $galleryUrls[0];
            }

            try {
                $productoId = $this->productModel->create($data);
                
                // Si hay más imágenes para la galería, guardarlas
                if (!empty($galleryUrls)) {
                    $this->productModel->addGalleryImages($productoId, $galleryUrls);
                }
                
                echo json_encode(['success' => true, 'message' => \SellSoft\Helpers\Lang::get('messages.created_successfully') ?? 'Guardado con éxito']);
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
            $galleryUrls = $this->handleGalleryUpload();
            
            // Si subieron nuevas imágenes
            if (!empty($galleryUrls)) {
                $data['imagen_principal'] = $galleryUrls[0];
                $this->productModel->deleteGalleryImages($id);
                $this->productModel->addGalleryImages($id, $galleryUrls);
            } else {
                // Keep the old one
                $product = $this->productModel->find($id);
                $data['imagen_principal'] = $product['imagen_principal'] ?? null;
            }

            try {
                $this->productModel->update($id, $data);
                echo json_encode(['success' => true, 'message' => \SellSoft\Helpers\Lang::get('messages.updated_successfully') ?? 'Actualizado con éxito']);
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

    protected function handleGalleryUpload()
    {
        $urls = [];
        if (isset($_FILES['galeria']) && is_array($_FILES['galeria']['error'])) {
            $uploadDir = __DIR__ . '/../../public/storage/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $count = count($_FILES['galeria']['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($_FILES['galeria']['error'][$i] === UPLOAD_ERR_OK) {
                    $fileName = uniqid() . '_' . basename($_FILES['galeria']['name'][$i]);
                    $uploadFile = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES['galeria']['tmp_name'][$i], $uploadFile)) {
                        $urls[] = 'storage/products/' . $fileName;
                    }
                }
            }
        }
        return $urls;
    }

}
