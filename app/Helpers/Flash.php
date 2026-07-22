<?php
declare(strict_types=1);
namespace SellSoft\Helpers;
class Flash
{
    private const SESSION_KEY = '_flash';
    public const SUCCESS = 'success';
    public const ERROR   = 'error';
    public const WARNING = 'warning';
    public const INFO    = 'info';
    public static function add(string $type, string $message): void { $_SESSION[self::SESSION_KEY][] = ['type' => $type, 'message' => $message]; }
    public static function success(string $message): void { self::add(self::SUCCESS, $message); }
    public static function error(string $message): void   { self::add(self::ERROR,   $message); }
    public static function warning(string $message): void { self::add(self::WARNING,  $message); }
    public static function info(string $message): void    { self::add(self::INFO,     $message); }
    public static function getAll(): array { $messages = isset($_SESSION[self::SESSION_KEY]) ? $_SESSION[self::SESSION_KEY] : []; unset($_SESSION[self::SESSION_KEY]); return $messages; }
    public static function hasMessages(): bool { return !empty($_SESSION[self::SESSION_KEY]); }
}
