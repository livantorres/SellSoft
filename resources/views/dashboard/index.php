<?php
use SellSoft\Helpers\Format;
use SellSoft\Helpers\Session;
use SellSoft\Helpers\Lang;
$todayAmount   = (float)($todaySales['total_amount']   ?? 0);
$todayCount    = (int)($todaySales['total_sales']      ?? 0);
$monthlyAmount = (float)($monthlySales['total_amount'] ?? 0);
$monthlyCount  = (int)($monthlySales['total_sales']    ?? 0);
$userName      = (string)Session::get('user_name', 'User');
$hour          = (int)date('H');
if ($hour >= 5 && $hour < 12)       $greeting = Lang::get('good_morning');
elseif ($hour >= 12 && $hour < 18)  $greeting = Lang::get('good_afternoon');
else                                $greeting = Lang::get('good_evening');
$firstName = explode(' ', $userName)[0];
?>
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="page-title mb-0"><?= htmlspecialchars($greeting, ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8') ?> 👋</h1>
        <p class="page-subtitle mt-1">
            <i class="fas fa-calendar-day me-1"></i> <?= Format::date(date('Y-m-d'), 'long') ?> &nbsp;|&nbsp;
            <i class="fas fa-warehouse me-1"></i> <?= htmlspecialchars($warehouseName ?? 'Main Branch', ENT_QUOTES, 'UTF-8') ?>
        </p>
    </div>
    <a href="<?= APP_URL ?>/dashboard/pos" class="btn-primary-app" id="btn-go-pos"><i class="fas fa-cash-register me-2"></i> <?= Lang::get('new_sale') ?></a>
</div>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <article class="metric-card" style="--card-gradient:linear-gradient(90deg,#6366f1,#8b5cf6)">
            <div class="metric-icon blue" aria-hidden="true"><i class="fas fa-dollar-sign"></i></div>
            <div>
                <div class="metric-label"><?= Lang::get('todays_sales') ?></div>
                <div class="metric-value tabular"><?= Format::currency($todayAmount) ?></div>
                <div class="metric-change positive"><i class="fas fa-shopping-cart"></i> <?= $todayCount ?> <?= $todayCount === 1 ? 'transaction' : Lang::get('transactions') ?></div>
            </div>
        </article>
    </div>
    <div class="col-6 col-md-3">
        <article class="metric-card" style="--card-gradient:linear-gradient(90deg,#22c55e,#16a34a)">
            <div class="metric-icon green" aria-hidden="true"><i class="fas fa-chart-line"></i></div>
            <div>
                <div class="metric-label"><?= Lang::get('monthly_sales') ?></div>
                <div class="metric-value tabular"><?= Format::currency($monthlyAmount) ?></div>
                <div class="metric-change positive"><i class="fas fa-check-circle"></i> <?= $monthlyCount ?> <?= Lang::get('completed') ?></div>
            </div>
        </article>
    </div>
    <div class="col-6 col-md-3">
        <?php $lowCount = count($lowStockProducts); ?>
        <article class="metric-card" style="--card-gradient:linear-gradient(90deg,#f59e0b,#d97706)">
            <div class="metric-icon orange" aria-hidden="true"><i class="fas fa-box-open"></i></div>
            <div>
                <div class="metric-label"><?= Lang::get('active_products') ?></div>
                <div class="metric-value"><?= Format::number($totalProducts) ?></div>
                <div class="metric-change <?= $lowCount > 0 ? 'negative' : 'positive' ?>"><i class="fas fa-<?= $lowCount > 0 ? 'exclamation-triangle' : 'check' ?>"></i> <?= $lowCount > 0 ? $lowCount . ' ' . Lang::get('low_stock') : Lang::get('stock_healthy') ?></div>
            </div>
        </article>
    </div>
    <div class="col-6 col-md-3">
        <article class="metric-card" style="--card-gradient:linear-gradient(90deg,#8b5cf6,#7c3aed)">
            <div class="metric-icon purple" aria-hidden="true"><i class="fas fa-users"></i></div>
            <div>
                <div class="metric-label"><?= Lang::get('active_clients') ?></div>
                <div class="metric-value"><?= Format::number($totalClients) ?></div>
                <div class="metric-change positive"><i class="fas fa-user-plus"></i> Database</div>
            </div>
        </article>
    </div>
</div>
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card-panel h-100">
            <div class="card-header-panel">
                <div class="card-title"><i class="fas fa-chart-bar" style="color:var(--color-accent)"></i> <?= Lang::get('sales_last_7_days') ?></div>
                <span class="status-badge badge-blue"><i class="fas fa-warehouse"></i> <?= htmlspecialchars($warehouseName ?? 'Branch', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div style="position:relative;height:240px"><canvas id="sales-chart-week" aria-label="Weekly sales chart" role="img"></canvas></div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card-panel h-100">
            <div class="card-header-panel">
                <div class="card-title"><i class="fas fa-trophy" style="color:var(--color-warning)"></i> <?= Lang::get('top_products') ?></div>
                <span class="text-sm text-muted-app"><?= Lang::get('this_month') ?></span>
            </div>
            <?php if (!empty($topProducts)): ?>
                <ol class="list-unstyled mb-0">
                    <?php foreach ($topProducts as $i => $prod): ?>
                        <li style="display:flex;align-items:center;gap:.75rem;padding:.625rem 0;border-bottom:1px solid var(--color-border)" class="<?= $i === count($topProducts)-1 ? 'border-0' : '' ?>">
                            <span style="width:24px;height:24px;border-radius:50%;background:var(--color-surface-2);display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;color:var(--color-accent);flex-shrink:0"><?= $i + 1 ?></span>
                            <div style="flex:1;min-width:0">
                                <div style="font-size:.8rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($prod['nombre'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div style="font-size:.72rem;color:var(--color-text-muted)"><?= (int)$prod['units'] ?> <?= Lang::get('units') ?></div>
                            </div>
                            <span class="tabular" style="font-size:.8rem;font-weight:700;color:var(--color-success);white-space:nowrap"><?= Format::currency((float)$prod['revenue']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php else: ?>
                <div class="text-center py-4" style="color:var(--color-text-muted)"><i class="fas fa-chart-pie mb-2" style="font-size:2rem;opacity:.3;display:block"></i><p class="text-sm mb-0">No sales this month</p></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="row g-3">
    <div class="col-12 col-lg-7">
        <div class="card-panel">
            <div class="card-header-panel">
                <div class="card-title"><i class="fas fa-receipt" style="color:var(--color-secondary)"></i> <?= Lang::get('recent_transactions') ?></div>
                <a href="<?= APP_URL ?>/dashboard/sales" class="text-sm" style="color:var(--color-accent)"><?= Lang::get('view_all') ?> <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            <div class="table-wrapper" style="border:none">
                <table class="data-table" aria-label="Recent sales">
                    <thead><tr><th><?= Lang::get('code') ?></th><th><?= Lang::get('client') ?></th><th><?= Lang::get('seller') ?></th><th><?= Lang::get('payment') ?></th><th><?= Lang::get('total') ?></th><th><?= Lang::get('time') ?></th></tr></thead>
                    <tbody>
                        <?php if (!empty($recentSales)): ?>
                            <?php
                            $paymentIcons = ['efectivo' => 'fa-money-bill-wave', 'tarjeta' => 'fa-credit-card', 'nequi' => 'fa-mobile-alt', 'daviplata' => 'fa-mobile-alt', 'transferencia'=> 'fa-university'];
                            foreach ($recentSales as $sale):
                                $payIcon = isset($paymentIcons[$sale['metodo_pago']]) ? $paymentIcons[$sale['metodo_pago']] : 'fa-circle-dot';
                            ?>
                            <tr>
                                <td><a href="<?= APP_URL ?>/dashboard/sales/<?= htmlspecialchars($sale['codigo'], ENT_QUOTES) ?>" style="font-weight:600;font-size:.8rem"><?= htmlspecialchars($sale['codigo'], ENT_QUOTES) ?></a></td>
                                <td style="font-size:.82rem"><?= htmlspecialchars($sale['cliente'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="font-size:.82rem;color:var(--color-text-muted)"><?= htmlspecialchars($sale['vendedor'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="font-size:.78rem;color:var(--color-text-muted)"><i class="fas <?= $payIcon ?>"></i> <?= ucfirst($sale['metodo_pago']) ?></td>
                                <td class="tabular" style="font-weight:700;color:var(--color-success)"><?= Format::currency((float)$sale['total']) ?></td>
                                <td style="font-size:.75rem;color:var(--color-text-muted)"><?= Format::date($sale['creado_en'], 'datetime') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4" style="color:var(--color-text-muted)"><i class="fas fa-inbox mb-2" style="font-size:1.5rem;opacity:.3;display:block"></i> No sales recorded today</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="card-panel">
            <div class="card-header-panel">
                <div class="card-title">
                    <i class="fas fa-exclamation-triangle" style="color:var(--color-warning)"></i> <?= Lang::get('low_stock') ?>
                    <?php if (!empty($lowStockProducts)): ?><span class="status-badge badge-orange ms-1"><?= count($lowStockProducts) ?></span><?php endif; ?>
                </div>
                <a href="<?= APP_URL ?>/dashboard/inventory" class="text-sm" style="color:var(--color-accent)"><?= Lang::get('inventory') ?> <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            <?php if (!empty($lowStockProducts)): ?>
                <div style="max-height:280px;overflow-y:auto">
                    <?php foreach ($lowStockProducts as $prod): ?>
                    <div style="display:flex;align-items:center;gap:.75rem;padding:.75rem;border-radius:.625rem;background:var(--color-surface-2);margin-bottom:.5rem">
                        <div style="width:36px;height:36px;border-radius:.5rem;background:rgba(239,68,68,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="fas fa-box" style="color:var(--color-danger);font-size:.875rem"></i></div>
                        <div style="flex:1;min-width:0">
                            <div style="font-size:.82rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($prod['nombre'], ENT_QUOTES, 'UTF-8') ?></div>
                            <div style="font-size:.72rem;color:var(--color-text-muted)">SKU: <?= htmlspecialchars($prod['codigo_sku'] ?? 'N/A', ENT_QUOTES) ?></div>
                        </div>
                        <div style="text-align:right;flex-shrink:0">
                            <div style="font-size:1.1rem;font-weight:800;color:var(--color-danger)"><?= (int)$prod['stock_actual'] ?></div>
                            <div style="font-size:.68rem;color:var(--color-text-muted)">min: <?= (int)$prod['stock_minimo'] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4" style="color:var(--color-text-muted)"><i class="fas fa-check-circle mb-2" style="font-size:2rem;color:var(--color-success);opacity:.6;display:block"></i><p class="text-sm mb-0"><?= Lang::get('stock_healthy') ?></p></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
window.weeklySalesData = <?= json_encode(array_map(function($v) { return ['label' => date('d/m', strtotime($v['fecha'])), 'amount' => (float)$v['monto'], 'count' => (int)$v['cantidad']]; }, $weeklySales), JSON_UNESCAPED_UNICODE) ?>;
window.CSRF_TOKEN = '<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>'; window.APP_URL = '<?= APP_URL ?>'; window.WAREHOUSE_ID = <?= (int)($activeWarehouse ?? 1) ?>;
</script>
