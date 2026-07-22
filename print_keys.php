<?php
$viewsDir = 'resources/views/catalog/';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
$keys = [];

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        preg_match_all("/Lang::get\(['\"]([^'\"]+)['\"]\)/", $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $key) {
                $keys[] = $key;
            }
        }
    }
}

$ctrlIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app/Controllers/'));
foreach ($ctrlIterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        preg_match_all("/Lang::get\(['\"]([^'\"]+)['\"]\)/", $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $key) {
                $keys[] = $key;
            }
        }
    }
}

$keys = array_unique($keys);
sort($keys);

$esFile = 'resources/lang/es.php';
$esLang = require $esFile;

$missingEs = [];
foreach ($keys as $k) {
    if (!isset($esLang[$k])) {
        echo "    '$k' => '$k',\n";
    }
}
