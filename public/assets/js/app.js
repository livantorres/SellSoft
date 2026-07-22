'use strict';
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('main-content');
    const toggleBtn = document.getElementById('sidebar-toggle');
    if (!sidebar) return;
    const isCollapsed = sidebar.classList.toggle('collapsed');
    if (content) content.classList.toggle('expanded', isCollapsed);
    if (toggleBtn) toggleBtn.setAttribute('aria-expanded', String(!isCollapsed));
    localStorage.setItem('sellsoft_sidebar_collapsed', isCollapsed ? '1' : '0');
}
function closeMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    if (sidebar) sidebar.classList.remove('mobile-open');
    if (overlay) overlay.style.display = 'none';
}
function openMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    if (sidebar) sidebar.classList.add('mobile-open');
    if (overlay) overlay.style.display = 'block';
}
function restoreSidebarState() {
    const collapsed = localStorage.getItem('sellsoft_sidebar_collapsed') === '1';
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('main-content');
    const toggleBtn = document.getElementById('sidebar-toggle');
    if (collapsed && sidebar && window.innerWidth > 768) {
        sidebar.classList.add('collapsed');
        if (content) content.classList.add('expanded');
        if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
    }
}
function startClock() {
    const clockEl = document.getElementById('clock-bogota');
    if (!clockEl) return;
    function tick() {
        const now = new Date();
        const options = { timeZone: 'America/Bogota', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false };
        clockEl.textContent = new Intl.DateTimeFormat('es-CO', options).format(now);
    }
    tick(); setInterval(tick, 1000);
}
async function changeWarehouse(warehouseId) {
    const selector = document.getElementById('warehouse-selector');
    try {
        const formData = new FormData();
        formData.append('warehouse_id', warehouseId);
        formData.append('_csrf', window.CSRF_TOKEN || '');
        const response = await fetch(window.APP_URL + '/dashboard/switch-warehouse', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: formData });
        const data = await response.json();
        if (data.success) {
            showNotification('Warehouse switched successfully', 'success');
            setTimeout(function() { location.reload(); }, 800);
        } else {
            showNotification(data.error || 'Error switching warehouse', 'error');
            if (selector) selector.value = String(window.WAREHOUSE_ID);
        }
    } catch (err) {
        console.error('Warehouse switch error:', err);
        showNotification('Connection error', 'error');
        if (selector) selector.value = String(window.WAREHOUSE_ID);
    }
}
function initSalesChart() {
    const canvas = document.getElementById('sales-chart-week');
    if (!canvas || typeof Chart === 'undefined') return;
    const chartData = window.weeklySalesData || [];
    const labels = chartData.map(function(d) { return d.label; });
    const amounts = chartData.map(function(d) { return d.amount; });
    const counts = chartData.map(function(d) { return d.count; });
    const ctx = canvas.getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 240);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.35)');
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.02)');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                { label: 'Sales (COP)', data: amounts, borderColor: '#6366f1', backgroundColor: gradient, borderWidth: 2.5, pointBackgroundColor: '#6366f1', pointBorderColor: '#1e1e2e', pointBorderWidth: 2, pointRadius: 4, pointHoverRadius: 6, fill: true, tension: 0.4, yAxisID: 'y' },
                { label: 'Transactions', data: counts, borderColor: '#22c55e', backgroundColor: 'transparent', borderWidth: 2, pointBackgroundColor: '#22c55e', pointRadius: 3, pointHoverRadius: 5, borderDash: [6, 4], tension: 0.4, yAxisID: 'y1' }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: true, labels: { color: '#94a3b8', font: { family: 'Inter', size: 12 }, usePointStyle: true } },
                tooltip: { backgroundColor: '#252535', borderColor: 'rgba(255,255,255,0.08)', borderWidth: 1, titleColor: '#e2e8f0', bodyColor: '#94a3b8', padding: 12, callbacks: { label: function(ctx) { if (ctx.datasetIndex === 0) { return ' $ ' + Number(ctx.raw).toLocaleString('es-CO'); } return ' ' + ctx.raw + ' transactions'; } } }
            },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false }, ticks: { color: '#64748b', font: { family: 'Inter', size: 11 } } },
                y: { position: 'left', grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false }, ticks: { color: '#64748b', font: { family: 'Inter', size: 11 }, callback: function(val) { return '$ ' + Number(val).toLocaleString('es-CO'); } } },
                y1: { position: 'right', grid: { display: false }, ticks: { color: '#64748b', font: { family: 'Inter', size: 11 }, stepSize: 1 } }
            }
        }
    });
}
function showNotification(message, type) {
    type = type || 'info';
    if (typeof Swal !== 'undefined') {
        var iconMap = { success: 'success', error: 'error', warning: 'warning', info: 'info' };
        Swal.fire({ toast: true, position: 'top-end', icon: iconMap[type] || 'info', title: message, showConfirmButton: false, timer: 3000, timerProgressBar: true, background: '#252535', color: '#e2e8f0' });
        return;
    }
    alert(message);
}
async function confirmAction(message, callback) {
    if (typeof Swal === 'undefined') { if (window.confirm(message)) callback(); return; }
    const result = await Swal.fire({ title: 'Are you sure?', text: message, icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#252535', confirmButtonText: 'Yes, continue', cancelButtonText: 'Cancel', background: '#1e1e2e', color: '#e2e8f0' });
    if (result.isConfirmed) callback();
}
async function postRequest(url, data) {
    data = data || {};
    var formData = new FormData();
    formData.append('_csrf', window.CSRF_TOKEN || '');
    Object.keys(data).forEach(function(key) { formData.append(key, data[key]); });
    var response = await fetch(url, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: formData });
    if (!response.ok) throw new Error('HTTP ' + response.status + ': ' + response.statusText);
    return response.json();
}
function formatCurrencyCOP(value) { return '$ ' + Number(value).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }); }
document.addEventListener('DOMContentLoaded', function() {
    restoreSidebarState(); startClock(); initSalesChart();
    var currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link-item').forEach(function(link) {
        var href = link.getAttribute('href');
        if (href && currentPath.indexOf(href) === 0 && href !== '/') link.classList.add('active');
    });
    document.querySelectorAll('.flash-container .alert').forEach(function(alert) {
        setTimeout(function() { alert.classList.remove('show'); setTimeout(function() { if (alert.parentNode) alert.parentNode.removeChild(alert); }, 300); }, 5000);
    });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeMobileSidebar(); });
    if (window.innerWidth <= 768) { var toggleBtn = document.getElementById('sidebar-toggle'); if (toggleBtn) toggleBtn.addEventListener('click', openMobileSidebar); }
    
    
    const icon = document.getElementById('theme-icon');
    if (document.documentElement.classList.contains('light-theme')) {
        if(icon) { icon.classList.remove('fa-sun'); icon.classList.add('fa-moon'); }
    }

    // Move modals to body to prevent stacking context z-index issues
    document.querySelectorAll('.modal').forEach(function(modal) {
        document.body.appendChild(modal);
    });

    console.log('%cSellSoft ERP %cv1.0 %c| Colombia', 'color:#6366f1;font-weight:bold;font-size:14px', 'color:#22c55e;font-weight:bold;font-size:14px', 'color:#94a3b8;font-size:12px');
});


function toggleTheme() {
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
}

async function updateTableDynamic() {
    try {
        const res = await fetch(window.location.href);
        const html = await res.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newTbody = doc.querySelector('.data-table tbody');
        if (newTbody) {
            document.querySelector('.data-table tbody').innerHTML = newTbody.innerHTML;
        }
        const openModal = document.querySelector('.modal.show');
        if (openModal) {
            const modalInstance = bootstrap.Modal.getInstance(openModal);
            if (modalInstance) modalInstance.hide();
        }
        if (Swal.isVisible()) {
            Swal.close();
        }
    } catch(err) {
        console.error('Error:', err);
        window.location.reload();
    }
}
