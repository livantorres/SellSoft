<?php
declare(strict_types=1);
namespace SellSoft\Helpers;
class Format
{
    public static function currency($amount, bool $cents = false): string
    {
        $decimals  = $cents ? 2 : 0;
        $formatted = number_format((float)$amount, $decimals, ',', '.');
        return '$ ' . $formatted;
    }
    public static function number($number, int $decimals = 0): string
    {
        return number_format((float)$number, $decimals, ',', '.');
    }
    public static function date(string $date, string $format = 'short'): string
    {
        if (empty($date) || $date === '0000-00-00') return 'N/A';
        $ts = is_numeric($date) ? (int)$date : strtotime($date);
        if ($ts === false) return $date;
        $months = [1=>'enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        if ($format === 'long') {
            return date('j', $ts) . ' de ' . $months[(int)date('n', $ts)] . ' de ' . date('Y', $ts);
        }
        if ($format === 'datetime') return date('d/m/Y H:i', $ts);
        if ($format === 'ago')      return self::timeAgo($ts);
        return date('d/m/Y', $ts);
    }
    public static function timeAgo(int $timestamp): string
    {
        $diff = time() - $timestamp;
        if ($diff < 60)       return 'hace un momento';
        if ($diff < 3600)     return 'hace ' . floor($diff / 60) . ' minutos';
        if ($diff < 86400)    return 'hace ' . floor($diff / 3600) . ' horas';
        if ($diff < 604800)   return 'hace ' . floor($diff / 86400) . ' dias';
        if ($diff < 2592000)  return 'hace ' . floor($diff / 604800) . ' semanas';
        if ($diff < 31536000) return 'hace ' . floor($diff / 2592000) . ' meses';
        return 'hace ' . floor($diff / 31536000) . ' anios';
    }
    public static function slug(string $text): string
    {
        $map = ['a'=>'a','e'=>'e','i'=>'i','o'=>'o','u'=>'u','A'=>'a','E'=>'e','I'=>'i','O'=>'o','U'=>'u','n'=>'n','N'=>'n','u'=>'u','U'=>'u'];
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }
    public static function discountPercentage(float $original, float $sale): int
    {
        if ($original <= 0) return 0;
        return (int) round((($original - $sale) / $original) * 100);
    }
    public static function truncate(string $text, int $max = 100, string $suffix = '...'): string
    {
        if (mb_strlen($text) <= $max) return $text;
        return mb_substr($text, 0, $max) . $suffix;
    }
    public static function fileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) { $bytes /= 1024; $i++; }
        return round($bytes, 1) . ' ' . $units[$i];
    }
}
