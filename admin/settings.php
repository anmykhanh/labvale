<?php
/**
 * LabVale Admin - Cài đặt Hệ thống & SEO
 */
require_once __DIR__ . '/../includes/auth.php';
Auth::requireLogin();

$db = Database::getConnection();

// Handle Form Submission
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        flash('error', 'Token CSRF không hợp lệ.');
        header('Location: ' . url('/admin/settings'));
        exit;
    }

    $formType = $_POST['form_type'] ?? '';

    // Update General / SEO Settings
    if ($formType === 'settings') {
        $fields = [
            'site_name',
            'site_slogan',
            'site_description',
            'site_keywords',
            'contact_email',
            'github_url',
            'footer_note'
        ];

        $uStmt = $db->prepare("INSERT INTO `settings` (`key_name`, `value`, `label`, `group_name`) VALUES (?, ?, ?, 'general') ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");

        foreach ($fields as $key) {
            $val = trim($_POST[$key] ?? '');
            $uStmt->execute([$key, $val, $key]);
        }

        flash('success', 'Đã lưu cấu hình hệ thống và SEO thành công.');
        header('Location: ' . url('/admin/settings'));
        exit;
    }

    // Change Admin Password
    if ($formType === 'password') {
        $oldPass = (string)($_POST['old_password'] ?? '');
        $newPass = (string)($_POST['new_password'] ?? '');
        $confirmPass = (string)($_POST['confirm_password'] ?? '');

        $userId = Auth::user()['id'];
        $uStmt = $db->prepare("SELECT `password_hash` FROM `users` WHERE id = ?");
        $uStmt->execute([$userId]);
        $currentHash = $uStmt->fetchColumn();

        if (!password_verify($oldPass, $currentHash)) {
            flash('error', 'Mật khẩu hiện tại không đúng.');
        } elseif (strlen($newPass) < 8) {
            flash('error', 'Mật khẩu mới phải có tối thiểu 8 ký tự.');
        } elseif ($newPass !== $confirmPass) {
            flash('error', 'Mật khẩu xác nhận không trùng khớp.');
        } else {
            $newHash = password_hash($newPass, PASSWORD_DEFAULT);
            $upStmt = $db->prepare("UPDATE `users` SET `password_hash` = ? WHERE id = ?");
            $upStmt->execute([$newHash, $userId]);
            flash('success', 'Đã đổi mật khẩu quản trị thành công.');
        }

        header('Location: ' . url('/admin/settings'));
        exit;
    }
}

$adminNav = 'settings';
$adminTitle = 'Cài đặt hệ thống & SEO';
require_once __DIR__ . '/views/header.php';
?>

<div class="admin-grid-2-1">
    <!-- General & SEO Settings -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title">Cấu hình thông tin &amp; SEO</div>
        </div>
        <div style="padding: 2rem;">
            <form action="<?= url('/admin/settings') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="form_type" value="settings">

                <div class="form-group">
                    <label for="site_name" class="form-label">Tên thương hiệu *</label>
                    <input type="text" id="site_name" name="site_name" class="form-control" required value="<?= e(get_setting('site_name', 'LabVale')) ?>">
                </div>

                <div class="form-group">
                    <label for="site_slogan" class="form-label">Slogan / Khẩu hiệu *</label>
                    <input type="text" id="site_slogan" name="site_slogan" class="form-control" required value="<?= e(get_setting('site_slogan', 'Research. Innovate. Create Value.')) ?>">
                </div>

                <div class="form-group">
                    <label for="site_description" class="form-label">Mô tả Meta (SEO Description) *</label>
                    <textarea id="site_description" name="site_description" class="form-control" rows="3" required><?= e(get_setting('site_description')) ?></textarea>
                    <div class="form-help">Mô tả ngắn gọn, xuất hiện trên công cụ tìm kiếm Google và thẻ Open Graph Facebook/Twitter.</div>
                </div>

                <div class="form-group">
                    <label for="site_keywords" class="form-label">Từ khóa Meta (Keywords)</label>
                    <input type="text" id="site_keywords" name="site_keywords" class="form-control" value="<?= e(get_setting('site_keywords')) ?>">
                </div>

                <div class="admin-grid-2">
                    <div class="form-group">
                        <label for="contact_email" class="form-label">Email liên hệ chính</label>
                        <input type="email" id="contact_email" name="contact_email" class="form-control" value="<?= e(get_setting('contact_email', 'contact@labvale.org')) ?>">
                    </div>

                    <div class="form-group">
                        <label for="github_url" class="form-label">Đường dẫn GitHub</label>
                        <input type="url" id="github_url" name="github_url" class="form-control" value="<?= e(get_setting('github_url')) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="footer_note" class="form-label">Thông điệp chân trang (Footer)</label>
                    <textarea id="footer_note" name="footer_note" class="form-control" rows="3"><?= e(get_setting('footer_note')) ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">
                    Lưu toàn bộ cài đặt
                </button>
            </form>
        </div>
    </div>

    <!-- Security & Password Change -->
    <div>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Đổi mật khẩu quản trị</div>
            </div>
            <div style="padding: 1.5rem;">
                <form action="<?= url('/admin/settings') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="form_type" value="password">

                    <div class="form-group">
                        <label for="old_password" class="form-label">Mật khẩu hiện tại *</label>
                        <input type="password" id="old_password" name="old_password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="new_password" class="form-label">Mật khẩu mới *</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" required minlength="8" placeholder="Tối thiểu 8 ký tự">
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới *</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="8">
                    </div>

                    <button type="submit" class="btn btn-secondary" style="width: 100%; margin-top: 0.5rem;">
                        Cập nhật mật khẩu
                    </button>
                </form>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-title">Sơ đồ Sitemap &amp; Robots</div>
            </div>
            <div style="padding: 1.5rem; font-size: 0.85rem; color: var(--admin-muted); display: flex; flex-direction: column; gap: 0.75rem;">
                <div>
                    <strong>Sitemap XML:</strong><br>
                    <a href="<?= url('/sitemap.xml') ?>" target="_blank" style="color: var(--admin-blue); word-break: break-all;">
                        <?= APP_URL ?>/sitemap.xml
                    </a>
                </div>
                <div>
                    <strong>Robots.txt:</strong><br>
                    <a href="<?= url('/robots.txt') ?>" target="_blank" style="color: var(--admin-blue); word-break: break-all;">
                        <?= APP_URL ?>/robots.txt
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/views/footer.php'; ?>
