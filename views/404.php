<?php
/**
 * LabVale - Trang 404 tùy chỉnh
 */
http_response_code(404);
$pageTitle = '404 — Không tìm thấy trang — LabVale';
$pageDescription = 'Trang bạn đang tìm kiếm không tồn tại hoặc đã được thay đổi cấu trúc liên kết.';
$currentNav = '404';

require_once __DIR__ . '/layout/header.php';
?>

<div class="container container-narrow" style="padding-top: 5rem; padding-bottom: 7rem; text-align: center;">
    <div style="font-family: var(--font-mono); font-size: 5rem; font-weight: 700; color: var(--color-primary-navy); line-height: 1; margin-bottom: 1rem;">
        404
    </div>
    <h1 style="font-size: 1.75rem; color: var(--color-primary-navy); margin-bottom: 1rem;">
        Tài liệu hoặc trang không tồn tại
    </h1>
    <p style="font-size: 1.05rem; color: var(--color-secondary-text); max-width: 520px; margin: 0 auto 2.5rem; line-height: 1.7;">
        Địa chỉ liên kết bạn vừa truy cập có thể đã được thay đổi hoặc không có trong kho lưu trữ dữ liệu của LabVale.
    </p>

    <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
        <a href="<?= url('/') ?>" class="btn btn-primary">
            Trở về Trang chủ
        </a>
        <a href="<?= url('/research-areas') ?>" class="btn btn-secondary">
            Xem các lĩnh vực nghiên cứu
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
