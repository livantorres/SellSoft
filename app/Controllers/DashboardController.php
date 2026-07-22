<?php
declare(strict_types=1);
namespace SellSoft\Controllers;
use SellSoft\Core\Controller;
use SellSoft\Core\Database;
use SellSoft\Helpers\Session;
use SellSoft\Helpers\Flash;
use SellSoft\Helpers\Csrf;
use SellSoft\Models\Setting;
class DashboardController extends Controller
{
    private $db;
    private $warehouseId;
    public function __construct()
    {
        $this->db          = Database::getInstance();
        $this->warehouseId = Session::warehouseId() ?? 1;
    }
    public function index(): void
    {
        $todaySales = $this->db->fetchOne(
            'SELECT COUNT(*) AS total_sales, COALESCE(SUM(total), 0) AS total_amount FROM ventas WHERE DATE(creado_en) = CURDATE() AND estado = "completada" AND bodega_id = ?',
            [$this->warehouseId]
        );
        $monthlySales = $this->db->fetchOne(
            'SELECT COUNT(*) AS total_sales, COALESCE(SUM(total), 0) AS total_amount FROM ventas WHERE MONTH(creado_en) = MONTH(NOW()) AND YEAR(creado_en) = YEAR(NOW()) AND estado = "completada" AND bodega_id = ?',
            [$this->warehouseId]
        );
        $lowStockProducts = $this->db->fetchAll(
            'SELECT p.nombre, p.codigo_sku, pb.stock_actual, pb.stock_minimo FROM producto_bodega pb INNER JOIN productos p ON p.id = pb.producto_id WHERE pb.stock_actual <= pb.stock_minimo AND pb.bodega_id = ? AND p.activo = 1 ORDER BY pb.stock_actual ASC LIMIT 10',
            [$this->warehouseId]
        );
        $totalProducts = $this->db->fetchOne('SELECT COUNT(*) AS total FROM productos WHERE activo = 1');
        $totalClients  = $this->db->fetchOne('SELECT COUNT(*) AS total FROM clientes WHERE activo = 1');
        $recentSales = $this->db->fetchAll(
            'SELECT v.codigo, v.total, v.metodo_pago, v.creado_en, u.nombre AS vendedor, COALESCE(c.nombre, "Consumidor Final") AS cliente FROM ventas v INNER JOIN usuarios u ON u.id = v.usuario_id LEFT JOIN clientes c ON c.id = v.cliente_id WHERE v.bodega_id = ? AND v.estado = "completada" ORDER BY v.creado_en DESC LIMIT 8',
            [$this->warehouseId]
        );
        $weeklySales = $this->db->fetchAll(
            'SELECT DATE(creado_en) AS fecha, COUNT(*) AS cantidad, COALESCE(SUM(total), 0) AS monto FROM ventas WHERE creado_en >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND estado = "completada" AND bodega_id = ? GROUP BY DATE(creado_en) ORDER BY fecha ASC',
            [$this->warehouseId]
        );
        $topProducts = $this->db->fetchAll(
            'SELECT p.nombre, SUM(dv.cantidad) AS units, SUM(dv.total) AS revenue FROM detalle_ventas dv INNER JOIN productos p ON p.id = dv.producto_id INNER JOIN ventas v ON v.id = dv.venta_id WHERE MONTH(v.creado_en) = MONTH(NOW()) AND YEAR(v.creado_en) = YEAR(NOW()) AND v.estado = "completada" AND v.bodega_id = ? GROUP BY dv.producto_id ORDER BY units DESC LIMIT 5',
            [$this->warehouseId]
        );
        $warehouses = $this->db->fetchAll('SELECT id, nombre FROM bodegas WHERE activo = 1 ORDER BY nombre');
        $this->view('dashboard.index', [
            'title'            => 'Dashboard — ' . APP_NAME,
            'pageTitle'        => 'Dashboard',
            'todaySales'       => $todaySales,
            'monthlySales'     => $monthlySales,
            'lowStockProducts' => $lowStockProducts,
            'totalProducts'    => (int)($totalProducts['total'] ?? 0),
            'totalClients'     => (int)($totalClients['total'] ?? 0),
            'recentSales'      => $recentSales,
            'weeklySales'      => $weeklySales,
            'topProducts'      => $topProducts,
            'warehouses'       => $warehouses,
            'activeWarehouse'  => $this->warehouseId,
            'warehouseName'    => Session::get('warehouse_name', 'Main Branch'),
            'messages'         => Flash::getAll(),
            'csrfToken'        => Csrf::token(),
        ]);
    }
}
