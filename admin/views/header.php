<?php
/**
 * LabVale Admin - Header & Sidebar Component
 * @var string $adminTitle
 * @var string $adminNav
 */
require_once __DIR__ . '/../../includes/auth.php';
Auth::requireLogin();

$adminTitle = $adminTitle ?? 'Quản trị LabVale';
$adminNav = $adminNav ?? 'dashboard';
$currentUser = Auth::user();

// Count unread contacts
$db = Database::getConnection();
$unreadContacts = (int)$db->query("SELECT COUNT(*) FROM `contacts` WHERE `is_read` = 0")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($adminTitle) ?> — LabVale Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin-body">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="admin-sidebar-header">
            <a href="<?= url('/admin') ?>" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                <img src="<?= asset('images/logo-white.svg') ?>" alt="LabVale" style="height: 32px;">
            </a>
        </div>

        <nav class="admin-nav">
            <a href="<?= url('/admin') ?>" class="admin-nav-link <?= $adminNav === 'dashboard' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Tổng quan</span>
            </a>

            <a href="<?= url('/admin/projects') ?>" class="admin-nav-link <?= $adminNav === 'projects' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                <span>Dự án nghiên cứu</span>
            </a>

            <a href="<?= url('/admin/research-areas') ?>" class="admin-nav-link <?= $adminNav === 'research-areas' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                <span>Lĩnh vực nghiên cứu</span>
            </a>

            <a href="<?= url('/admin/articles') ?>" class="admin-nav-link <?= $adminNav === 'articles' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                <span>Bài viết kỹ thuật</span>
            </a>

            <a href="<?= url('/admin/news') ?>" class="admin-nav-link <?= $adminNav === 'news' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 13a2 2 0 0 1-2-2V7m2 13a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                <span>Tin tức &amp; Nhật ký</span>
            </a>

            <a href="<?= url('/admin/contacts') ?>" class="admin-nav-link <?= $adminNav === 'contacts' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <span>Hộp thư liên hệ</span>
                <?php if ($unreadContacts > 0): ?>
                    <span class="admin-badge"><?= $unreadContacts ?></span>
                <?php endif; ?>
            </a>

            <a href="<?= url('/admin/media') ?>" class="admin-nav-link <?= $adminNav === 'media' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                <span>Thư viện hình ảnh</span>
            </a>

            <a href="<?= url('/admin/settings') ?>" class="admin-nav-link <?= $adminNav === 'settings' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                <span>Cài đặt &amp; SEO</span>
            </a>
        </nav>

        <div class="admin-sidebar-footer">
            <span style="font-size: 0.8rem; color: #94A3B8;">User: <?= e($currentUser['username'] ?? 'admin') ?></span>
            <a href="<?= url('/admin/logout') ?>" style="color: #F87171; text-decoration: none; font-size: 0.8rem; font-weight: 600;">
                Đăng xuất
            </a>
        </div>
    </aside>

    <!-- Admin Main -->
    <div class="admin-main">
        <!-- Topbar -->
        <header class="admin-topbar">
            <div style="font-weight: 600; font-size: 1.15rem; color: var(--admin-navy);">
                <?= e($adminTitle) ?>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <a href="<?= url('/') ?>" target="_blank" class="btn btn-secondary btn-sm">
                    Xem website &rarr;
                </a>
            </div>
        </header>

        <!-- Admin Content Area -->
        <div class="admin-content">
            <?php if ($flashSuccess = flash('success')): ?>
                <div class="alert alert-success"><?= e($flashSuccess) ?></div>
            <?php endif; ?>
            <?php if ($flashError = flash('error')): ?>
                <div class="alert alert-error"><?= e($flashError) ?></div>
            <?php endif; ?>
