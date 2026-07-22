<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 &mdash; Page Not Found | SellSoft</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style> body { background:#0f0f1a; color:#e2e8f0; font-family:'Inter',sans-serif; display:flex; align-items:center; justify-content:center; min-height:100vh; } .number { font-size:8rem; font-weight:800; background:linear-gradient(135deg,#6366f1,#8b5cf6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; } .back-btn { background:linear-gradient(135deg,#6366f1,#4338ca); color:white; border:none; padding:.65rem 1.5rem; border-radius:.5rem; font-weight:600; text-decoration:none; } </style>
</head>
<body>
    <div class="text-center">
        <div class="number">404</div>
        <h1 style="font-size:1.5rem;margin-bottom:.5rem">Page Not Found</h1>
        <p style="color:#64748b;margin-bottom:2rem">The page you are looking for does not exist or has been moved.</p>
        <a href="<?= defined('APP_URL') ? APP_URL . '/dashboard' : '/' ?>" class="back-btn"><i class="fas fa-home me-2"></i>Go to Dashboard</a>
    </div>
</body>
</html>
