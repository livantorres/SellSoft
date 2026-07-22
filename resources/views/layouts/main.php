<?php
use SellSoft\Helpers\Lang;
use SellSoft\Helpers\Session;
?>
<!DOCTYPE html>
<html lang="<?= Lang::getLocale() ?>" data-bs-theme="dark">
<script>
    if (localStorage.getItem("theme") === "light") {
        document.documentElement.classList.add("light-theme");
        document.documentElement.setAttribute("data-bs-theme", "light");
    }
</script>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($title ?? Lang::get('dashboard') . ' — SellSoft', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body>
<div class="app-wrapper">
    <aside class="sidebar" id="sidebar" role="navigation" aria-label="Main menu">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon" aria-hidden="true"><i class="fas fa-store-alt"></i></div>
            <div class="sidebar-logo-text">SellSoft <div class="sidebar-logo-version">ERP System v1.0</div></div>
        </div>
        <nav class="sidebar-nav">
            <?php
            $currentUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
            $navGroups = [
                Lang::get('general') ?? 'General' => [
                    ['url' => '/dashboard', 'icon' => 'fa-chart-line', 'label' => Lang::get('dashboard')]
                ],
                Lang::get('sales') => [
                    ['url' => '/dashboard/pos', 'icon' => 'fa-cash-register', 'label' => Lang::get('pos')],
                    ['url' => '/dashboard/sales', 'icon' => 'fa-receipt', 'label' => Lang::get('sales_history')]
                ],
                Lang::get('catalog') => [
                    ['url' => '/dashboard/products', 'icon' => 'fa-box-open', 'label' => Lang::get('products')],
                    ['url' => '/dashboard/categories', 'icon' => 'fa-tags', 'label' => Lang::get('categories')],
                    ['url' => '/dashboard/offers', 'icon' => 'fa-percent', 'label' => Lang::get('offers')]
                ],
                Lang::get('inventory') => [
                    ['url' => '/dashboard/inventory', 'icon' => 'fa-boxes-stacked', 'label' => Lang::get('stock_movements')],
                    ['url' => '/dashboard/warehouses', 'icon' => 'fa-warehouse', 'label' => Lang::get('warehouses')],
                    ['url' => '/dashboard/transfers', 'icon' => 'fa-truck-fast', 'label' => Lang::get('transfers')]
                ],
                Lang::get('people') => [
                    ['url' => '/dashboard/clients', 'icon' => 'fa-users', 'label' => Lang::get('clients')],
                    ['url' => '/dashboard/providers', 'icon' => 'fa-truck', 'label' => Lang::get('providers')]
                ],
                Lang::get('analytics') => [
                    ['url' => '/dashboard/reports', 'icon' => 'fa-chart-pie', 'label' => Lang::get('analytics')]
                ],
            ];
            foreach ($navGroups as $groupName => $links):
            ?>
                <div class="nav-group-title"><?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?></div>
                <?php foreach ($links as $link): $isActive = strpos($currentUri, $link['url']) !== false; ?>
                    <a href="<?= APP_URL . $link['url'] ?>" class="nav-link-item <?= $isActive ? 'active' : '' ?>">
                        <i class="fas <?= $link['icon'] ?> nav-icon" aria-hidden="true"></i>
                        <span><?= htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php if (!empty($lowStockProducts) && $link['url'] === '/dashboard/inventory'): ?>
                            <span class="nav-badge"><?= count($lowStockProducts) ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <?php if (Session::get('is_admin')): ?>
                <div class="nav-group-title"><?= Lang::get('administration') ?></div>
                <a href="<?= APP_URL ?>/dashboard/users" class="nav-link-item <?= strpos($currentUri, '/users') !== false ? 'active' : '' ?>"><i class="fas fa-user-shield nav-icon" aria-hidden="true"></i> <span><?= Lang::get('users_roles') ?></span></a>
                <a href="<?= APP_URL ?>/dashboard/settings" class="nav-link-item <?= strpos($currentUri, '/settings') !== false ? 'active' : '' ?>"><i class="fas fa-sliders nav-icon" aria-hidden="true"></i> <span><?= Lang::get('settings') ?></span></a>
            <?php endif; ?>
            <div class="nav-group-title"><?= Lang::get('store') ?></div>
            <a href="<?= APP_URL ?>/store" class="nav-link-item" target="_blank" rel="noopener"><i class="fas fa-store nav-icon" aria-hidden="true"></i> <span><?= Lang::get('view_store') ?></span> <i class="fas fa-external-link-alt ms-auto" style="font-size:.65rem;opacity:.5"></i></a>
        </nav>
        <div class="sidebar-user">
            <div class="user-avatar" aria-hidden="true"><?= strtoupper(substr((string)Session::get('user_name', 'U'), 0, 1)) ?></div>
            <div class="user-info">
                <div class="user-name"><?= htmlspecialchars((string)Session::get('user_name', 'User'), ENT_QUOTES, 'UTF-8') ?></div>
                <div class="user-role"><?= htmlspecialchars(ucfirst(implode(', ', (array)Session::get('roles', ['user']))), ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <a href="<?= APP_URL ?>/logout" class="password-toggle ms-auto" title="<?= Lang::get('sign_out') ?>" aria-label="<?= Lang::get('sign_out') ?>"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </aside>
    <main class="main-content" id="main-content">
        <header class="top-navbar">
            <button class="sidebar-toggle-btn" id="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar" aria-expanded="true" aria-controls="sidebar"><i class="fas fa-bars"></i></button>
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-warehouse" style="color:var(--color-accent);font-size:.85rem"></i>
                <select id="warehouse-selector" class="warehouse-selector" onchange="changeWarehouse(this.value)" aria-label="Select active warehouse">
                    <?php foreach ($warehouses ?? [] as $wh): ?>
                        <option value="<?= (int)$wh['id'] ?>" <?= ((int)$wh['id'] === ($activeWarehouse ?? 1)) ? 'selected' : '' ?>><?= htmlspecialchars($wh['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <nav class="breadcrumb-nav d-none d-md-flex ms-3" aria-label="Breadcrumb">
                <i class="fas fa-home" style="font-size:.75rem"></i> <span class="separator">/</span> <span class="current"><?= htmlspecialchars($pageTitle ?? Lang::get('dashboard'), ENT_QUOTES, 'UTF-8') ?></span>
            </nav>
            <div class="ms-auto d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-secondary-app me-2" id="theme-toggle" onclick="toggleTheme()" type="button" style="background:var(--color-surface-2);border-color:var(--color-border);font-size:0.8rem;color:var(--color-text-primary)">
                        <i class="fas fa-sun" id="theme-icon"></i>
                    </button>
                    <div class="dropdown">
                    <button class="btn btn-sm btn-secondary-app dropdown-toggle" type="button" data-bs-toggle="dropdown" style="background:var(--color-surface-2);border-color:var(--color-border);font-size:0.8rem">
                        <i class="fas fa-language me-1"></i> <?= strtoupper(Lang::getLocale()) ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="background:var(--color-surface);border-color:var(--color-border);font-size:0.85rem">
                        <li><a class="dropdown-item <?= Lang::getLocale() === 'es' ? 'active' : '' ?>" href="<?= APP_URL ?>/lang/es">🇪🇸 Español</a></li>
                        <li><a class="dropdown-item <?= Lang::getLocale() === 'en' ? 'active' : '' ?>" href="<?= APP_URL ?>/lang/en">🇺🇸 English</a></li>
                    </ul>
                </div>
                <div class="d-none d-md-flex align-items-center gap-2" style="font-size:.78rem;color:var(--color-text-muted)">
                    <i class="fas fa-clock"></i> <span id="clock-bogota"><?= date('H:i:s') ?></span> <span>(BOG)</span>
                </div>
                <button class="sidebar-toggle-btn position-relative" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <?php if (!empty($lowStockProducts) && count($lowStockProducts) > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem"><?= count($lowStockProducts) ?> <span class="visually-hidden">low stock</span></span>
                    <?php endif; ?>
                </button>
            </div>
        </header>
        <?php if (!empty($messages)): ?>
            <div class="flash-container">
                <?php
                $classMap = ['success'=>'success','error'=>'danger','warning'=>'warning','info'=>'info'];
                $iconMap  = ['success'=>'fa-check-circle','error'=>'fa-times-circle','warning'=>'fa-exclamation-triangle','info'=>'fa-info-circle'];
                foreach ($messages as $msg):
                    $cls = isset($classMap[$msg['type']]) ? $classMap[$msg['type']] : 'secondary';
                    $ico = isset($iconMap[$msg['type']])  ? $iconMap[$msg['type']]  : 'fa-bell';
                ?>
                    <div class="alert alert-<?= $cls ?> alert-dismissible fade show shadow-lg" role="alert" style="border-radius:.875rem">
                        <i class="fas <?= $ico ?> me-2"></i> <?= htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="page-area fade-in"><?= $viewContent ?></div>
        <footer style="padding:.75rem 1.75rem;border-top:1px solid var(--color-border);font-size:.75rem;color:var(--color-text-muted);display:flex;justify-content:space-between;align-items:center">
            <span><strong>InnovRed</strong> &copy; <?= date('Y') ?> &mdash; Colombia &mdash; IVA <?= TAX_RATE ?>% &mdash; <?= CURRENCY_CODE ?></span>
            <span>Made with <i class="fas fa-heart text-danger"></i> in Colombia</span>
        </footer>
    </main>
</div>
<div id="sidebar-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:999;backdrop-filter:blur(4px)" onclick="closeMobileSidebar()" aria-hidden="true"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
