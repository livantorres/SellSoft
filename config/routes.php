<?php
/**
 * Application Route Definitions
 * @var SellSoft\Core\Router $router
 */

use SellSoft\Controllers\AuthController;
use SellSoft\Controllers\DashboardController;
use SellSoft\Middleware\AuthMiddleware;

$router->get('/', function () {
    header('Location: ' . APP_URL . '/login');
    exit;
});

$router->get('/login',  [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'processLogin']);
$router->get('/logout', [AuthController::class, 'logout']);

$router->group(['prefix' => '/dashboard', 'middleware' => [AuthMiddleware::class]], function ($r) {
    $r->get('',      [DashboardController::class, 'index']);
    $r->get('/home', [DashboardController::class, 'index']);
    $r->post('/switch-warehouse', [AuthController::class, 'switchWarehouse']);
    
    // Catalog Module
    $r->get('/categories',          [\SellSoft\Controllers\CategoryController::class, 'index']);
    $r->post('/categories',         [\SellSoft\Controllers\CategoryController::class, 'store']);
    $r->post('/categories/update',  [\SellSoft\Controllers\CategoryController::class, 'update']);
    $r->post('/categories/delete',  [\SellSoft\Controllers\CategoryController::class, 'delete']);
    
    $r->get('/brands',          [\SellSoft\Controllers\BrandController::class, 'index']);
    $r->post('/brands',         [\SellSoft\Controllers\BrandController::class, 'store']);
    $r->post('/brands/update',  [\SellSoft\Controllers\BrandController::class, 'update']);
    $r->post('/brands/delete',  [\SellSoft\Controllers\BrandController::class, 'delete']);
    
    $r->get('/providers',          [\SellSoft\Controllers\ProviderController::class, 'index']);
    $r->post('/providers',         [\SellSoft\Controllers\ProviderController::class, 'store']);
    $r->post('/providers/update',  [\SellSoft\Controllers\ProviderController::class, 'update']);
    $r->post('/providers/delete',  [\SellSoft\Controllers\ProviderController::class, 'delete']);

    $r->get('/products',          [\SellSoft\Controllers\ProductController::class, 'index']);
    $r->get('/products/next-sku',  [\SellSoft\Controllers\ProductController::class, 'nextSku']);
    $r->get('/products/create',   [\SellSoft\Controllers\ProductController::class, 'create']);
    $r->post('/products',         [\SellSoft\Controllers\ProductController::class, 'store']);
    $r->get('/products/{id}/edit',[\SellSoft\Controllers\ProductController::class, 'edit']);
    $r->post('/products/update',  [\SellSoft\Controllers\ProductController::class, 'update']);
    $r->post('/products/delete',  [\SellSoft\Controllers\ProductController::class, 'delete']);
});

$router->group(['prefix' => '/api', 'middleware' => [AuthMiddleware::class]], function ($r) {
    $r->get('/ping', function () {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok', 'time' => date('H:i:s'), 'timezone' => TIMEZONE]);
        exit;
    });
});

$router->get('/lang/{locale}', function ($locale) {
    \SellSoft\Helpers\Lang::setLocale($locale);
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : APP_URL . '/dashboard';
    header('Location: ' . $referer);
    exit;
});
