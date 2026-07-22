<?php
// Update Product Model
$file = 'app/Models/Product.php';
$c = file_get_contents($file);

if (strpos($c, 'public function addGalleryImages') === false) {
    $fn = '
    public function addGalleryImages($productoId, $urls) {
        if (empty($urls)) return;
        $sql = "INSERT INTO galeria_productos (producto_id, url_imagen, orden) VALUES (:producto_id, :url_imagen, :orden)";
        $stmt = $this->db->prepare($sql);
        foreach ($urls as $index => $url) {
            $stmt->execute([
                \':producto_id\' => $productoId,
                \':url_imagen\' => $url,
                \':orden\' => $index
            ]);
        }
    }
    
    public function getGalleryImages($productoId) {
        $stmt = $this->db->prepare("SELECT * FROM galeria_productos WHERE producto_id = :id ORDER BY orden ASC");
        $stmt->execute([\':id\' => $productoId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function deleteGalleryImages($productoId) {
        $stmt = $this->db->prepare("DELETE FROM galeria_productos WHERE producto_id = :id");
        $stmt->execute([\':id\' => $productoId]);
    }
';
    // Insert before the last closing brace
    $c = preg_replace('/\}\s*$/', $fn . "\n}\n", $c);
    file_put_contents($file, $c);
}

// Update ProductController
$file = 'app/Controllers/ProductController.php';
$c = file_get_contents($file);

$handleGalleryFn = '
    protected function handleGalleryUpload()
    {
        $urls = [];
        if (isset($_FILES[\'galeria\']) && is_array($_FILES[\'galeria\'][\'error\'])) {
            $uploadDir = __DIR__ . \'/../../public/storage/products/\';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $count = count($_FILES[\'galeria\'][\'name\']);
            for ($i = 0; $i < $count; $i++) {
                if ($_FILES[\'galeria\'][\'error\'][$i] === UPLOAD_ERR_OK) {
                    $fileName = uniqid() . \'_\' . basename($_FILES[\'galeria\'][\'name\'][$i]);
                    $uploadFile = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES[\'galeria\'][\'tmp_name\'][$i], $uploadFile)) {
                        $urls[] = \'storage/products/\' . $fileName;
                    }
                }
            }
        }
        return $urls;
    }
';

if (strpos($c, 'handleGalleryUpload') === false) {
    // Inject the function
    $c = preg_replace('/\}\s*$/', $handleGalleryFn . "\n}\n", $c);
}

// Rewrite store method
$storeMethod = '
    public function store()
    {
        if ($_SERVER[\'REQUEST_METHOD\'] === \'POST\') {
            $data = $_POST;
            $galleryUrls = $this->handleGalleryUpload();
            if (!empty($galleryUrls)) {
                $data[\'imagen_principal\'] = $galleryUrls[0];
            }

            try {
                $productoId = $this->productModel->create($data);
                
                // Si hay más imágenes para la galería, guardarlas
                if (!empty($galleryUrls)) {
                    $this->productModel->addGalleryImages($productoId, $galleryUrls);
                }
                
                echo json_encode([\'success\' => true, \'message\' => \SellSoft\Helpers\Lang::get(\'messages.created_successfully\') ?? \'Guardado con éxito\']);
                exit;
            } catch (\Exception $e) {
                echo json_encode([\'success\' => false, \'message\' => $e->getMessage()]);
            }
        }
    }
';
$c = preg_replace('/public function store\(\).*?\}\s*\}/s', $storeMethod, $c);

// Rewrite update method
$updateMethod = '
    public function update()
    {
        $id = $_POST[\'id\'] ?? null;
        if (!$id) { echo json_encode([\'success\' => false, \'message\' => \'ID is missing\']); return; }

        if ($_SERVER[\'REQUEST_METHOD\'] === \'POST\') {
            $data = $_POST;
            $galleryUrls = $this->handleGalleryUpload();
            
            // Si subieron nuevas imágenes
            if (!empty($galleryUrls)) {
                $data[\'imagen_principal\'] = $galleryUrls[0];
                $this->productModel->deleteGalleryImages($id);
                $this->productModel->addGalleryImages($id, $galleryUrls);
            } else {
                // Keep the old one
                $product = $this->productModel->find($id);
                $data[\'imagen_principal\'] = $product[\'imagen_principal\'] ?? null;
            }

            try {
                $this->productModel->update($id, $data);
                echo json_encode([\'success\' => true, \'message\' => \SellSoft\Helpers\Lang::get(\'messages.updated_successfully\') ?? \'Actualizado con éxito\']);
                exit;
            } catch (\Exception $e) {
                echo json_encode([\'success\' => false, \'message\' => $e->getMessage()]);
            }
        }
    }
';
$c = preg_replace('/public function update\(\).*?\}\s*\}/s', $updateMethod, $c);

file_put_contents($file, $c);
echo "Product models and controllers updated for gallery.\n";
