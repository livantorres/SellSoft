<?php
/**
 * SellSoft - Front Controller
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$router = new SellSoft\Core\Router();
require_once CONFIG_PATH . '/routes.php';
$router->dispatch();
