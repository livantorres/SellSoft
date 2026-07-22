<?php
// Fix Controller.php to pass $warehouses to layout
$file = 'core/Controller.php';
$c = file_get_contents($file);
$injection = '
            // Inject layout variables
            try {
                $db = \SellSoft\Core\Database::getInstance()->getPdo();
                $warehouses = $db->query("SELECT id, nombre FROM bodegas WHERE activo = 1 ORDER BY nombre ASC")->fetchAll(\PDO::FETCH_ASSOC);
                $activeWarehouse = \SellSoft\Helpers\Session::get(\'warehouse_id\', 1);
            } catch (\Exception $e) {
                $warehouses = [];
                $activeWarehouse = 1;
            }
';
$c = str_replace('ob_start();', ob_start() . $injection, $c);
// Wait, the string replace might be tricky, let's use preg_replace
$c = preg_replace('/ob_start\(\);/', "ob_start();\n" . $injection, $c);
file_put_contents($file, $c);

// Fix index.php to add Permissions-Policy header
$file = 'public/index.php';
$c = file_get_contents($file);
$header = 'header("Permissions-Policy: browsing-topics=(), join-ad-interest-group=(), run-ad-auction=(), private-state-token-issuance=(), private-state-token-redemption=(), private-aggregation=(), attribution-reporting=()");';
$c = preg_replace('/require_once __DIR__ \. \'\/\.\.\/core\/bootstrap\.php\';/', "require_once __DIR__ . '/../core/bootstrap.php';\n" . $header, $c);
file_put_contents($file, $c);

echo "Fixed warehouse selector and permissions policy headers.\n";
