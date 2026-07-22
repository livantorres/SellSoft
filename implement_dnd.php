<?php

// 1. Add Sortable JS to main layout
$file = 'resources/views/layouts/main.php';
$c = file_get_contents($file);
if (strpos($c, 'Sortable.min.js') === false) {
    $c = str_replace(
        '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>',
        '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>' . "\n" . '<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>',
        $c
    );
    file_put_contents($file, $c);
    echo "SortableJS added to main.php\n";
}

// 2. Rewrite products/index.php javascript for drag and drop
$file = 'resources/views/catalog/products/index.php';
$c = file_get_contents($file);

// Replace the file input onchange
$c = str_replace(
    'onchange="previewImages()"',
    'onchange="handleFiles(this)"',
    $c
);

// Replace previewImages function with handleFiles, renderGallery, and removeImage
$oldPreviewFunc = '/function previewImages\(\) \{.*?\}\s*\}/s';
$newGalleryJS = '
// --- VARIABLES GLOBALES DE GALERIA ---
let productImages = [];
let sortableGallery = null;

function resetProductForm() {
    document.getElementById(\'productForm\').reset();
    $(\'#productForm select\').val(\'\').trigger(\'change\');
    document.getElementById(\'productId\').value = \'\';
    document.getElementById(\'productModalLabel\').innerText = \'Crear Producto\';
    productImages = [];
    renderGallery();
}

function handleFiles(input) {
    const files = input.files;
    if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
            productImages.push(files[i]);
        }
        renderGallery();
    }
    // Limpiar input nativo para permitir subir la misma foto despues si la borran
    input.value = "";
}

function renderGallery() {
    const preview = document.getElementById(\'galleryPreview\');
    preview.innerHTML = \'\';
    
    productImages.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement(\'div\');
            col.className = \'col-4 col-md-4 position-relative gallery-item\';
            col.dataset.index = index;
            
            const badge = index === 0 ? \'<span class="badge bg-primary position-absolute top-0 start-0 m-1" style="font-size:0.6rem; z-index: 2;">Ppal</span>\' : \'\';
            const delBtn = `<button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-0 rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 20px; height: 20px; z-index: 2;" onclick="removeImage(${index})" title="Eliminar"><i class="fas fa-times" style="font-size: 0.6rem;"></i></button>`;
            
            col.innerHTML = `
                <div class="border rounded overflow-hidden shadow-sm position-relative drag-handle" style="aspect-ratio: 1/1; cursor: grab;">
                    ${badge}
                    ${delBtn}
                    <img src="${e.target.result}" class="w-100 h-100" style="object-fit: cover;">
                </div>
            `;
            preview.appendChild(col);
        }
        reader.readAsDataURL(file);
    });
    
    // Inicializar Sortable si no esta
    if (!sortableGallery) {
        sortableGallery = new Sortable(document.getElementById(\'galleryPreview\'), {
            animation: 150,
            handle: \'.drag-handle\',
            ghostClass: \'bg-light\',
            onEnd: function (evt) {
                // Reordenar array en JS
                const item = productImages.splice(evt.oldIndex, 1)[0];
                productImages.splice(evt.newIndex, 0, item);
                // Volver a renderizar para actualizar indices y badge
                renderGallery();
            }
        });
    }
}

function removeImage(index) {
    productImages.splice(index, 1);
    renderGallery();
}
';

$c = preg_replace('/function resetProductForm\(\).*?\}\s*\}/s', '/* replaced */', $c); // Remove old resetProductForm
$c = preg_replace($oldPreviewFunc, $newGalleryJS, $c);

// Update submit listener to append files
$c = str_replace(
    "const formData = new FormData(form);",
    "const formData = new FormData(form);\n    formData.delete('galeria[]');\n    productImages.forEach(file => {\n        formData.append('galeria[]', file);\n    });",
    $c
);

file_put_contents($file, $c);
echo "Drag and Drop logic added to products/index.php\n";

