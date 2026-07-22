<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($title ?? 'SellSoft', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body class="auth-page">
    <?php if (!empty($messages)): ?>
        <div class="flash-container" style="position:fixed;top:1rem;left:50%;transform:translateX(-50%);z-index:9999;min-width:360px">
            <?php
            $classMap = ['success'=>'success','error'=>'danger','warning'=>'warning','info'=>'info'];
            $iconMap  = ['success'=>'fa-check-circle','error'=>'fa-times-circle','warning'=>'fa-exclamation-triangle','info'=>'fa-info-circle'];
            foreach ($messages as $msg):
                $cls = isset($classMap[$msg['type']]) ? $classMap[$msg['type']] : 'secondary';
                $ico = isset($iconMap[$msg['type']])  ? $iconMap[$msg['type']]  : 'fa-bell';
            ?>
                <div class="alert alert-<?= $cls ?> alert-dismissible fade show shadow" role="alert">
                    <i class="fas <?= $ico ?> me-2"></i>
                    <?= htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="auth-card"><?= $viewContent ?></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
