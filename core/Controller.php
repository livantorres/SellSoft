<?php
declare(strict_types=1);

namespace SellSoft\Core;

class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewFile)) {
            if (APP_DEBUG) {
                throw new \RuntimeException('View not found: ' . $viewFile);
            }
            http_response_code(500);
            die('Internal server error.');
        }
        $layoutFile = VIEWS_PATH . '/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            ob_start();
            // Inject layout variables
            try {
                $db = \SellSoft\Core\Database::getInstance()->getPdo();
                $warehouses = $db->query("SELECT id, nombre FROM bodegas WHERE activo = 1 ORDER BY nombre ASC")->fetchAll(\PDO::FETCH_ASSOC);
                $activeWarehouse = \SellSoft\Helpers\Session::get('warehouse_id', 1);
            } catch (\Exception $e) {
                $warehouses = [];
                $activeWarehouse = 1;
            }

            include $viewFile;
            $viewContent = ob_get_clean();
            include $layoutFile;
        } else {
            include $viewFile;
        }
    }

    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    protected function input(string $key, $default = null)
    {
        return isset($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
    }

    protected function query(string $key, $default = null)
    {
        return isset($_GET[$key]) ? trim((string)$_GET[$key]) : $default;
    }
}
