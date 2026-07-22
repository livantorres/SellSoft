<?php
declare(strict_types=1);

namespace SellSoft\Helpers;

class Lang
{
    private static $translations = [];
    private static $locale = 'es'; // default

    public static function setLocale(string $locale): void
    {
        $allowed = ['es', 'en'];
        self::$locale = in_array($locale, $allowed) ? $locale : 'es';
        Session::set('app_locale', self::$locale);
        self::load();
    }

    public static function getLocale(): string
    {
        if (Session::has('app_locale')) {
            return Session::get('app_locale');
        }
        return self::$locale;
    }

    private static function load(): void
    {
        $locale = self::getLocale();
        $file = dirname(__DIR__, 2) . '/resources/lang/' . $locale . '.php';
        if (file_exists($file)) {
            self::$translations = require $file;
        } else {
            self::$translations = [];
        }
    }

    public static function get(string $key, array $replace = []): string
    {
        if (empty(self::$translations)) {
            self::load();
        }

        $line = isset(self::$translations[$key]) ? self::$translations[$key] : $key;

        foreach ($replace as $search => $value) {
            $line = str_replace(':' . $search, (string)$value, $line);
        }

        return $line;
    }
}
