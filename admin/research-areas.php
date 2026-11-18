<?php
/**
 * LabVale Admin - Quản lý Lĩnh vực nghiên cứu
 */
require_once __DIR__ . '/../includes/auth.php';
Auth::requireLogin();

$db = Database::getConnection();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Handle Save
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        flash('error', 'Token CSRF không hợp lệ.');
        header('Location: ' . url('/admin/research-areas'));
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] === 'save' && $id > 0) {
        $name = trim($_POST['name'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        $shortDesc = trim($_POST['short_desc'] ?? '');
        $fullContent = trim($_POST['full_content'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        $stmt = $db->prepare("
            UPDATE `research_areas` 
            SET `name` = ?, `subtitle` = ?, `short_desc` = ?, `full_content` = ?, `image` = ?, `sort_order` = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $subtitle, $shortDesc, $fullContent, $image, $sortOrder, $id]);
        flash('success', 'Đã cập nhật thông tin lĩnh vực nghiên cứu.');
        header('Location: ' . url('/admin/research-areas'));
        exit;
    }
}

$adminNav = 'research-areas';

if ($action === 'edit' && $id > 0) {
    $stmt = $db->prepare("SELECT * FROM `research_areas` WHERE id = ?");
    $stmt->execute([$id]);
    $area = $stmt->fetch();

    if (!$area) {
        flash('error', 'Lĩnh vực không tồn tại.');
        header('Location: ' . url('/admin/research-areas'));
        exit;
    }

    $adminTitle = 'Hiệu chỉnh lĩnh vực: ' . $area['name'];
    require_once __DIR__ . '/views/header.php';
    ?>
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title"><?= e($adminTitle) ?></div>
            <a href="<?= url('/admin/research-areas') ?>" class="btn btn-secondary btn-sm">&larr; Quay lại danh sách</a>
        </div>
        <div style="padding: 2rem;">
            <form action="<?= url('/admin/research-areas?action=edit&id=' . $id) ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save">

                <div class="form-group">
                    <label for="name" class="form-label">Tên lĩnh vực *</label>
                    <input type="text" id="name" name="name" class="form-control" required value="<?= e($area['name']) ?>">
                </div>

                <div class="form-group">
                    <label for="subtitle" class="form-label">Phụ đề định hướng *</label>
                    <input type="text" id="subtitle" name="subtitle" class="form-control" required value="<?= e($area['subtitle']) ?>">
                </div>

                <div class="form-group">
                    <label for="short_desc" class="form-label">Mô tả ngắn (hiển thị tại thẻ trang chủ) *</label>
                    <textarea id="short_desc" name="short_desc" class="form-control" rows="3" required><?= e($area['short_desc']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="full_content" class="form-label">Nội dung học thuật &amp; Phương hướng chi tiết *</label>
                    <textarea id="full_content" name="full_content" class="form-control" rows="10" required><?= e($area['full_content']) ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="image" class="form-label">Ảnh đại diện (assets/images/)</label>
                        <input type="text" id="image" name="image" class="form-control" value="<?= e($area['image']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="sort_order" class="form-label">Thứ tự hiển thị</label>
                        <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= (int)$area['sort_order'] ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">
                    Cập nhật lĩnh vực nghiên cứu
                </button>
            </form>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/views/footer.php';
    exit;
}

// List Action
$adminTitle = 'Quản lý Lĩnh vực nghiên cứu';
require_once __DIR__ . '/views/header.php';

$areas = $db->query("
    SELECT a.*, COUNT(p.id) AS project_count 
    FROM `research_areas` a 
    LEFT JOIN `projects` p ON p.area_id = a.id 
    GROUP BY a.id 
    ORDER BY a.sort_order ASC
")->fetchAll();
?>

<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">6 Trụ cột nghiên cứu công nghệ</div>
    </div>
    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 60px;">Thứ tự</th>
                    <th>Tên lĩnh vực</th>
                    <th>Phụ đề định hướng</th>
                    <th>Số dự án</th>
                    <th style="text-align: right; width: 140px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($areas as $a): ?>
                    <tr>
                        <td><strong>#<?= $a['sort_order'] ?></strong></td>
                        <td>
                            <strong style="color: var(--admin-navy);"><?= e($a['name']) ?></strong>
                            <div style="font-size: 0.75rem; color: var(--admin-muted); font-family: monospace;">/research-areas/<?= e($a['slug']) ?></div>
                        </td>
                        <td><?= e($a['subtitle']) ?></td>
                        <td><span style="font-weight: 600;"><?= $a['project_count'] ?></span> dự án</td>
                        <td style="text-align: right;">
                            <div class="admin-actions-cell" style="justify-content: flex-end;">
                                <a href="<?= url('/admin/research-areas?action=edit&id=' . $a['id']) ?>" class="btn btn-secondary btn-sm">Hiệu chỉnh</a>
                                <a href="<?= url('/research-areas/' . $a['slug']) ?>" target="_blank" class="btn btn-secondary btn-sm">Xem</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/views/footer.php'; ?>
