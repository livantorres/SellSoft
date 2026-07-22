<?php
declare(strict_types=1);

namespace SellSoft\Core;

class View
{
    public static function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public static function url(string $path = ''): string
    {
        return APP_URL . '/' . ltrim($path, '/');
    }

    public static function asset(string $path): string
    {
        return ASSETS_URL . '/' . ltrim($path, '/');
    }
}
