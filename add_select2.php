<?php
$file = 'resources/views/layouts/main.php';
$c = file_get_contents($file);

if (strpos($c, 'select2.min.css') === false) {
    // Add CSS
    $css = "    <link href=\"https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css\" rel=\"stylesheet\">\n    <link href=\"https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css\" rel=\"stylesheet\">\n";
    $c = str_replace('</head>', $css . '</head>', $c);
    
    // Add JS and initialize select2
    $js = "
<script src=\"https://code.jquery.com/jquery-3.7.0.min.js\"></script>
<script src=\"https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js\"></script>
<script>
$(document).ready(function() {
    function initSelect2() {
        $('select:not([name$=\'_length\'])').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('.modal.show').length ? $('.modal.show') : $(document.body)
        });
    }
    initSelect2();
    // Re-init on modal shown
    $('.modal').on('shown.bs.modal', function () {
        $(this).find('select').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $(this)
        });
    });
});
</script>
";
    $c = str_replace('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3', $js . '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3', $c);
    file_put_contents($file, $c);
    echo "Select2 added to main.php\n";
}

// Remove local jQuery from views
$views = [
    'resources/views/catalog/categories/index.php',
    'resources/views/catalog/brands/index.php',
    'resources/views/catalog/providers/index.php',
    'resources/views/catalog/products/index.php'
];

foreach ($views as $v) {
    if (file_exists($v)) {
        $vc = file_get_contents($v);
        $vc = preg_replace('/<script src="https:\/\/code\.jquery\.com\/jquery-[0-9\.]+\.min\.js"><\/script>\n?/', '', $vc);
        file_put_contents($v, $vc);
    }
}
echo "Removed duplicate jQuery from views.\n";
