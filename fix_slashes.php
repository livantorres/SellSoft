<?php
$views = [
    'resources/views/catalog/categories/index.php',
    'resources/views/catalog/brands/index.php',
    'resources/views/catalog/providers/index.php',
    'resources/views/catalog/products/index.php'
];

foreach ($views as $file) {
    if (file_exists($file)) {
        $c = file_get_contents($file);
        
        // Remove the backslashes from the Lang::get calls that were incorrectly escaped
        $c = str_replace('\SellSoft\Helpers\Lang::get(\\\'common.delete_confirm\\\')', '\SellSoft\Helpers\Lang::get(\'common.delete_confirm\')', $c);
        $c = str_replace('\SellSoft\Helpers\Lang::get(\\\'common.delete\\\')', '\SellSoft\Helpers\Lang::get(\'common.delete\')', $c);
        $c = str_replace('\SellSoft\Helpers\Lang::get(\\\'common.cancel\\\')', '\SellSoft\Helpers\Lang::get(\'common.cancel\')', $c);
        
        $c = str_replace('\SellSoft\Helpers\Lang::get(\\\'products.confirm_delete\\\')', '\SellSoft\Helpers\Lang::get(\'products.confirm_delete\')', $c);
        $c = str_replace('\SellSoft\Helpers\Lang::get(\\\'products.delete\\\')', '\SellSoft\Helpers\Lang::get(\'products.delete\')', $c);
        $c = str_replace('\SellSoft\Helpers\Lang::get(\\\'general.cancel\\\')', '\SellSoft\Helpers\Lang::get(\'general.cancel\')', $c);

        file_put_contents($file, $c);
    }
}
echo "Fixed backslash syntax error in views.\n";
