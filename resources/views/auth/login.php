<?php
use SellSoft\Helpers\Lang;
?>
<div class="auth-logo" aria-hidden="true"><i class="fas fa-store-alt"></i></div>
<div class="auth-title"><?= APP_NAME ?></div>
<div class="auth-subtitle">Commercial Management System</div>
<form method="POST" action="<?= APP_URL ?>/login" id="login-form" novalidate>
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
    <div class="mb-3">
        <label for="email" class="field-label"><i class="fas fa-envelope me-1 opacity-60"></i> <?= Lang::get('email_address') ?></label>
        <input type="email" id="email" name="email" class="field-input" placeholder="admin@sellsoft.co" required autocomplete="email" value="<?= htmlspecialchars(isset($_POST['email']) ? $_POST['email'] : '', ENT_QUOTES, 'UTF-8') ?>">
    </div>
    <div class="mb-3">
        <label for="password" class="field-label"><i class="fas fa-lock me-1 opacity-60"></i> <?= Lang::get('password') ?></label>
        <div class="input-group-field">
            <input type="password" id="password" name="password" class="field-input" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" required autocomplete="current-password">
            <button type="button" class="password-toggle" onclick="togglePassword(this)" aria-label="Toggle password visibility" tabindex="-1"><i class="fas fa-eye" id="password-eye"></i></button>
        </div>
    </div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
            <label class="form-check-label text-sm" for="remember"><?= Lang::get('remember_me') ?></label>
        </div>
        <span class="text-sm text-muted-app"><?= Lang::get('forgot_password') ?></span>
    </div>
    <button type="submit" class="btn-primary-app w-100" id="btn-login">
        <span id="btn-text"><i class="fas fa-sign-in-alt me-2"></i> <?= Lang::get('sign_in') ?></span>
        <span id="btn-loading" class="d-none"><span class="spinner-border spinner-border-sm me-2"></span> Verifying...</span>
    </button>
</form>
<div class="auth-footer">
    <i class="fas fa-shield-alt me-1"></i> <?= Lang::get('secure_connection') ?> &nbsp;|&nbsp; <strong>InnovRed</strong> &copy; <?= date('Y') ?><br>
    <span class="text-muted-app mt-1 d-block"><i class="fas fa-map-marker-alt me-1"></i>Colombia &nbsp;|&nbsp; <i class="fas fa-clock me-1"></i><?= date('d/m/Y H:i') ?> (Bogotá)</span>
</div>
<script>
function togglePassword(btn) {
    var field = document.getElementById('password');
    var icon = document.getElementById('password-eye');
    if (field.type === 'password') { field.type = 'text'; icon.className = 'fas fa-eye-slash'; } else { field.type = 'password'; icon.className = 'fas fa-eye'; }
}
document.getElementById('login-form').addEventListener('submit', function(e) {
    var email = document.getElementById('email').value.trim();
    var password = document.getElementById('password').value;
    if (!email || !password) {
        e.preventDefault();
        if (typeof Swal !== 'undefined') { Swal.fire({ icon: 'warning', title: 'Incomplete fields', text: 'Please enter your email and password.', confirmButtonColor: '#6366f1', background: '#1e1e2e', color: '#cdd6f4' }); }
        return;
    }
    document.getElementById('btn-text').classList.add('d-none');
    document.getElementById('btn-loading').classList.remove('d-none');
    document.getElementById('btn-login').disabled = true;
});
</script>
