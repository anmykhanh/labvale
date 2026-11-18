<?php
/**
 * LabVale Admin - Quản lý Hộp thư liên hệ
 */
require_once __DIR__ . '/../includes/auth.php';
Auth::requireLogin();

$db = Database::getConnection();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Handle POST actions
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        flash('error', 'Token CSRF không hợp lệ.');
        header('Location: ' . url('/admin/contacts'));
        exit;
    }

    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'delete') {
        $delId = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("DELETE FROM `contacts` WHERE id = ?");
        $stmt->execute([$delId]);
        flash('success', 'Đã xóa thư liên hệ.');
        header('Location: ' . url('/admin/contacts'));
        exit;
    }

    if ($postAction === 'toggle_read') {
        $cId = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("UPDATE `contacts` SET `is_read` = 1 - `is_read` WHERE id = ?");
        $stmt->execute([$cId]);
        header('Location: ' . url('/admin/contacts'));
        exit;
    }
}

$adminNav = 'contacts';

// Detail View
if ($action === 'view' && $id > 0) {
    // Mark as read automatically when opened
    $uStmt = $db->prepare("UPDATE `contacts` SET `is_read` = 1 WHERE id = ?");
    $uStmt->execute([$id]);

    $stmt = $db->prepare("SELECT * FROM `contacts` WHERE id = ?");
    $stmt->execute([$id]);
    $contact = $stmt->fetch();

    if (!$contact) {
        flash('error', 'Thư liên hệ không tồn tại.');
        header('Location: ' . url('/admin/contacts'));
        exit;
    }

    $adminTitle = 'Chi tiết thư: ' . $contact['subject'];
    require_once __DIR__ . '/views/header.php';
    ?>

    <div class="admin-card" style="max-width: 800px;">
        <div class="admin-card-header">
            <div class="admin-card-title"><?= e($contact['subject']) ?></div>
            <a href="<?= url('/admin/contacts') ?>" class="btn btn-secondary btn-sm">&larr; Quay lại hộp thư</a>
        </div>
        <div style="padding: 2rem;">
            <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--admin-border); display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <div style="font-size: 0.8rem; color: var(--admin-muted); text-transform: uppercase;">Người gửi:</div>
                    <div style="font-weight: 600; font-size: 1.05rem;"><?= e($contact['name']) ?></div>
                    <div style="color: var(--admin-blue);"><?= e($contact['email']) ?></div>
                </div>
                <div>
                    <div style="font-size: 0.8rem; color: var(--admin-muted); text-transform: uppercase;">Đơn vị / Tổ chức:</div>
                    <div><?= !empty($contact['organization']) ? e($contact['organization']) : '<em>(Không ghi)</em>' ?></div>
                    <div style="font-size: 0.825rem; color: var(--admin-muted); margin-top: 0.35rem;">Thời gian gửi: <?= format_date($contact['created_at']) ?></div>
                </div>
            </div>

            <div style="font-size: 0.85rem; color: var(--admin-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Nội dung thông điệp:</div>
            <div style="background-color: #F8FAFC; border: 1px solid var(--admin-border); border-radius: 6px; padding: 1.5rem; font-size: 1rem; line-height: 1.7; white-space: pre-wrap; color: #1E293B;">
<?= e($contact['message']) ?>
            </div>

            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <a href="mailto:<?= e($contact['email']) ?>?subject=Re: <?= urlencode($contact['subject']) ?>" class="btn btn-primary btn-sm">
                    Gửi email phản hồi
                </a>
                <form action="<?= url('/admin/contacts') ?>" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa thư này?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $contact['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Xóa thư</button>
                </form>
            </div>
        </div>
    </div>

    <?php
    require_once __DIR__ . '/views/footer.php';
    exit;
}

// List Action
$adminTitle = 'Hộp thư liên hệ & Trao đổi';
require_once __DIR__ . '/views/header.php';

$contacts = $db->query("SELECT * FROM `contacts` ORDER BY id DESC")->fetchAll();
?>

<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">Danh sách thư trao đổi học thuật &amp; kỹ thuật</div>
    </div>

    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Người gửi</th>
                    <th>Email</th>
                    <th>Đơn vị</th>
                    <th>Tiêu đề</th>
                    <th>Ngày gửi</th>
                    <th style="text-align: right; width: 140px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($contacts)): ?>
                    <?php foreach ($contacts as $c): ?>
                        <tr style="<?= $c['is_read'] == 0 ? 'background-color: #FEFCE8; font-weight: 500;' : '' ?>">
                            <td><?= $c['id'] ?></td>
                            <td>
                                <?= e($c['name']) ?>
                                <?php if ($c['is_read'] == 0): ?>
                                    <span style="font-size: 0.7rem; background-color: #EF4444; color: #FFFFFF; padding: 0.1rem 0.4rem; border-radius: 3px; margin-left: 0.3rem;">Mới</span>
                                <?php endif; ?>
                            </td>
                            <td><?= e($c['email']) ?></td>
                            <td><?= e($c['organization'] ?? '—') ?></td>
                            <td>
                                <a href="<?= url('/admin/contacts?action=view&id=' . $c['id']) ?>" style="color: var(--admin-navy);">
                                    <?= e(truncate_text($c['subject'], 45)) ?>
                                </a>
                            </td>
                            <td><?= format_date($c['created_at']) ?></td>
                            <td style="text-align: right;">
                                <div class="admin-actions-cell" style="justify-content: flex-end;">
                                    <a href="<?= url('/admin/contacts?action=view&id=' . $c['id']) ?>" class="btn btn-secondary btn-sm">Xem</a>
                                    <form action="<?= url('/admin/contacts') ?>" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa thư này?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--admin-muted);">Chưa có thư liên hệ nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/views/footer.php'; ?>
