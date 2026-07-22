<?php
$cssFile = 'public/assets/css/app.css';
$css = file_get_contents($cssFile);

// Add light theme overrides
$lightTheme = "
:root.light-theme {
    --color-bg-dark: #f8fafc;
    --color-bg-base: #f1f5f9;
    --color-surface: #ffffff;
    --color-surface-2: #f8fafc;
    --color-surface-3: #e2e8f0;
    --color-sidebar: #ffffff;
    --color-border: rgba(0, 0, 0, 0.08);
    --color-border-accent: rgba(99, 102, 241, 0.2);
    --color-text-primary: #0f172a;
    --color-text-secondary: #475569;
    --color-text-muted: #64748b;
    --shadow-card: 0 4px 12px rgba(0, 0, 0, 0.04);
    --shadow-elevated: 0 8px 24px rgba(0, 0, 0, 0.06);
}
";

if (strpos($css, ':root.light-theme') === false) {
    // Insert after :root { ... }
    $css = preg_replace('/(:root\s*\{.*?\n\})/s', "$1\n" . $lightTheme, $css, 1);
    file_put_contents($cssFile, $css);
}

$mainFile = 'resources/views/layouts/main.php';
$main = file_get_contents($mainFile);

// Insert theme toggle button
if (strpos($main, 'id="theme-toggle"') === false) {
    $btn = '<button class="btn btn-sm btn-dark me-2" id="theme-toggle" onclick="toggleTheme()" type="button" style="background:var(--color-surface-2);border-color:var(--color-border);font-size:0.8rem;color:var(--color-text-primary)">
                        <i class="fas fa-sun" id="theme-icon"></i>
                    </button>';
    $main = preg_replace('/(<div class="dropdown">\s*<button class="btn btn-sm btn-dark dropdown-toggle")/', $btn . "\n                    $1", $main);
    
    // Also, we need to apply light-theme to <html> if it's set in localStorage on initial page load
    $htmlTag = '<html lang="<?= Lang::getLocale() ?>" data-bs-theme="dark">';
    $newHtmlTag = '<html lang="<?= Lang::getLocale() ?>">
<script>
    if (localStorage.getItem("theme") === "light") {
        document.documentElement.classList.add("light-theme");
    }
</script>';
    $main = str_replace($htmlTag, $newHtmlTag, $main);
    
    file_put_contents($mainFile, $main);
}

$jsFile = 'public/assets/js/app.js';
$js = file_get_contents($jsFile);

// Insert toggleTheme function
if (strpos($js, 'function toggleTheme') === false) {
    $fn = "
function toggleTheme() {
    const html = document.documentElement;
    const icon = document.getElementById('theme-icon');
    if (html.classList.contains('light-theme')) {
        html.classList.remove('light-theme');
        localStorage.setItem('theme', 'dark');
        if(icon) { icon.classList.remove('fa-moon'); icon.classList.add('fa-sun'); }
    } else {
        html.classList.add('light-theme');
        localStorage.setItem('theme', 'light');
        if(icon) { icon.classList.remove('fa-sun'); icon.classList.add('fa-moon'); }
    }
}
";
    $js .= "\n" . $fn;
    
    // Add initialization logic to DOMContentLoaded
    $initLogic = "
    const icon = document.getElementById('theme-icon');
    if (document.documentElement.classList.contains('light-theme')) {
        if(icon) { icon.classList.remove('fa-sun'); icon.classList.add('fa-moon'); }
    }
";
    $js = str_replace('// Move modals to body', $initLogic . "\n    // Move modals to body", $js);
    file_put_contents($jsFile, $js);
}

echo "Theme toggle logic added.\n";
