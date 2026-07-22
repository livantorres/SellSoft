<?php
$viewsDir = 'resources/views/catalog/';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
$keys = [];

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        
        // Extract Lang keys
        preg_match_all("/Lang::get\(['\"]([^'\"]+)['\"]\)/", $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $key) {
                $keys[] = $key;
            }
        }
        
        // Remove <!DOCTYPE html><html><head>...<body> and </body></html>
        if (strpos($content, '<!DOCTYPE html>') !== false) {
            $content = preg_replace('/<!DOCTYPE html>\s*<html[^>]*>\s*<head>.*?<\/head>\s*<body>/is', '', $content);
            $content = preg_replace('/<\/body>\s*<\/html>/is', '', $content);
            // also remove `use SellSoft\Helpers\Lang;` since the layout might already have it or we can leave it
            file_put_contents($file->getPathname(), $content);
        }
    }
}

// Extract from controllers too
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
$enFile = 'resources/lang/en.php';
$esLang = require $esFile;
$enLang = require $enFile;

$missingEs = [];
$missingEn = [];

foreach ($keys as $k) {
    if (!isset($esLang[$k])) {
        // Create a readable label from the key (e.g. 'catalog.categories.title' -> 'Title')
        $parts = explode('.', $k);
        $last = end($parts);
        $label = ucwords(str_replace('_', ' ', $last));
        $missingEs[$k] = $label;
    }
    if (!isset($enLang[$k])) {
        $parts = explode('.', $k);
        $last = end($parts);
        $label = ucwords(str_replace('_', ' ', $last));
        $missingEn[$k] = $label;
    }
}

function updateLangFile($file, $missing) {
    if (empty($missing)) return;
    $content = file_get_contents($file);
    $append = "\n    // Auto-added keys\n";
    foreach ($missing as $k => $v) {
        $append .= "    '$k' => '$v',\n";
    }
    // insert before the last ];
    $content = preg_replace('/];\s*$/', $append . "];\n", $content);
    file_put_contents($file, $content);
}

updateLangFile($esFile, $missingEs);
updateLangFile($enFile, $missingEn);

echo "Missing keys added to lang files and view headers stripped.\n";
