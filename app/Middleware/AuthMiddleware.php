<?php
declare(strict_types=1);
namespace SellSoft\Middleware;
use SellSoft\Helpers\Session;
class AuthMiddleware
{
    public function handle(): void
    {
        if (!Session::isAuthenticated()) {
            Session::set('intended_url', isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/');
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }
}
