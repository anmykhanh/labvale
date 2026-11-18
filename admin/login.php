<?php
/**
 * LabVale Admin - Đăng nhập
 */
require_once __DIR__ . '/../includes/auth.php';

// If already logged in, redirect to dashboard
if (Auth::check()) {
    header('Location: ' . url('/admin'));
    exit;
}

$error = null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Mã bảo mật CSRF không hợp lệ. Vui lòng thử lại.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $error = 'Vui lòng nhập tên đăng nhập và mật khẩu.';
        } elseif (Auth::attempt($username, $password)) {
            $redirect = $_SESSION['redirect_after_login'] ?? url('/admin');
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập quản trị — LabVale</title>
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body style="background-color: #F1F5F9;">
    <div class="login-wrapper">
        <div class="login-card">
            <div style="text-align: center; margin-bottom: 2rem;">
                <img src="<?= asset('images/logo.svg') ?>" alt="LabVale" style="height: 38px; margin: 0 auto 0.75rem;">
                <div style="font-size: 0.85rem; color: var(--admin-muted); font-weight: 500;">
                    HỆ THỐNG QUẢN TRỊ NỘI BỘ
                </div>
            </div>

            <?php if ($flashMsg = flash('error')): ?>
                <div class="alert alert-error"><?= e($flashMsg) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= e($error) ?></div>
            <?php endif; ?>

            <form action="<?= url('/admin/login') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="username" class="form-label">Tài khoản hoặc Email</label>
                    <input type="text" id="username" name="username" class="form-control" required autofocus placeholder="admin" value="<?= e($_POST['username'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    Đăng nhập hệ thống
                </button>
            </form>

            <div style="margin-top: 2rem; padding-top: 1.25rem; border-top: 1px solid var(--admin-border); text-align: center;">
                <a href="<?= url('/') ?>" style="font-size: 0.85rem; color: var(--admin-muted);">
                    &larr; Trở về trang chủ LabVale
                </a>
            </div>
        </div>
    </div>
</body>
</html>
