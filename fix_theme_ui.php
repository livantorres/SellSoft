<?php
$mainFile = 'resources/views/layouts/main.php';
$main = file_get_contents($mainFile);

// Re-add data-bs-theme="dark" to html tag as default and update script
$main = preg_replace(
    '/<html lang="<\?= Lang::getLocale\(\) \?>">\s*<script>\s*if \(localStorage\.getItem\("theme"\) === "light"\) \{\s*document\.documentElement\.classList\.add\("light-theme"\);\s*\}\s*<\/script>/s',
    '<html lang="<?= Lang::getLocale() ?>" data-bs-theme="dark">
<script>
    if (localStorage.getItem("theme") === "light") {
        document.documentElement.classList.add("light-theme");
        document.documentElement.setAttribute("data-bs-theme", "light");
    }
</script>',
    $main
);

// We should also replace the standard btn-dark in language dropdown with something that adapts, or just keep it since btn-dark is always dark in both themes. But the user said "botones o select que no se ven". Let's change the dropdown toggles to btn-secondary-app.
$main = str_replace('btn-dark dropdown-toggle', 'btn-secondary-app dropdown-toggle', $main);
$main = str_replace('btn-dark me-2', 'btn-secondary-app me-2', $main);

file_put_contents($mainFile, $main);

$jsFile = 'public/assets/js/app.js';
$js = file_get_contents($jsFile);

// Update toggleTheme function to handle data-bs-theme
$js = preg_replace(
    '/function toggleTheme\(\) \{.*?\}/s',
    "function toggleTheme() {
    const html = document.documentElement;
    const icon = document.getElementById('theme-icon');
    if (html.classList.contains('light-theme')) {
        html.classList.remove('light-theme');
        html.setAttribute('data-bs-theme', 'dark');
        localStorage.setItem('theme', 'dark');
        if(icon) { icon.classList.remove('fa-moon'); icon.classList.add('fa-sun'); }
    } else {
        html.classList.add('light-theme');
        html.setAttribute('data-bs-theme', 'light');
        localStorage.setItem('theme', 'light');
        if(icon) { icon.classList.remove('fa-sun'); icon.classList.add('fa-moon'); }
    }
}",
    $js
);
file_put_contents($jsFile, $js);

// Update app.css to ensure modal content follows our theme variables
$cssFile = 'public/assets/css/app.css';
$css = file_get_contents($cssFile);
$cssOverrides = "
/* Theme Overrides for Bootstrap Components */
.modal-content, .card { background-color: var(--color-surface); color: var(--color-text-primary); border-color: var(--color-border); }
.modal-header, .modal-footer, .card-header, .card-footer { border-color: var(--color-border); }
.form-control, .form-select { background-color: var(--color-surface-2); color: var(--color-text-primary); border-color: var(--color-border); }
.form-control:focus, .form-select:focus { background-color: var(--color-surface-3); color: var(--color-text-primary); border-color: var(--color-accent); box-shadow: 0 0 0 3px var(--color-accent-light); }
.dropdown-menu { background-color: var(--color-surface); border-color: var(--color-border); }
.dropdown-item { color: var(--color-text-primary); }
.dropdown-item:hover, .dropdown-item:focus { background-color: var(--color-surface-2); color: var(--color-accent); }
";
if (strpos($css, 'Theme Overrides') === false) {
    file_put_contents($cssFile, $css . "\n" . $cssOverrides);
}

echo "Theme UI fixed.\n";
