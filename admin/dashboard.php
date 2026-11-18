<?php
/**
 * LabVale Admin - Dashboard
 */
$adminTitle = 'Bảng điều khiển tổng quan';
$adminNav = 'dashboard';

require_once __DIR__ . '/views/header.php';

$db = Database::getConnection();

// Query Counts
$countProjects = (int)$db->query("SELECT COUNT(*) FROM `projects`")->fetchColumn();
$countAreas = (int)$db->query("SELECT COUNT(*) FROM `research_areas`")->fetchColumn();
$countArticles = (int)$db->query("SELECT COUNT(*) FROM `articles`")->fetchColumn();
$countNews = (int)$db->query("SELECT COUNT(*) FROM `news`")->fetchColumn();
$countContacts = (int)$db->query("SELECT COUNT(*) FROM `contacts`")->fetchColumn();

// Recent Projects
$recentProjects = $db->query("
    SELECT p.*, a.name AS area_name 
    FROM `projects` p 
    LEFT JOIN `research_areas` a ON p.area_id = a.id 
    ORDER BY p.id DESC 
    LIMIT 5
")->fetchAll();

// Recent Contacts
$recentContacts = $db->query("
    SELECT * FROM `contacts` 
    ORDER BY `id` DESC 
    LIMIT 5
")->fetchAll();
?>

<!-- Stat Cards Grid -->
<div class="admin-stats-grid">
    <div class="admin-stat-card">
        <div class="admin-stat-label">Dự án nghiên cứu</div>
        <div class="admin-stat-value"><?= $countProjects ?></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label">Lĩnh vực hoạt động</div>
        <div class="admin-stat-value"><?= $countAreas ?></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label">Bài viết kỹ thuật</div>
        <div class="admin-stat-value"><?= $countArticles ?></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label">Thư liên hệ mới</div>
        <div class="admin-stat-value"><?= $unreadContacts ?> <span style="font-size: 0.9rem; font-weight: 400; color: var(--admin-muted);">/ <?= $countContacts ?></span></div>
    </div>
</div>

<!-- Quick Action Bar -->
<div style="margin-bottom: 2rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
    <a href="<?= url('/admin/projects?action=create') ?>" class="btn btn-primary btn-sm">
        + Thêm dự án mới
    </a>
    <a href="<?= url('/admin/articles?action=create') ?>" class="btn btn-primary btn-sm">
        + Viết bài nghiên cứu mới
    </a>
    <a href="<?= url('/admin/news?action=create') ?>" class="btn btn-primary btn-sm">
        + Đăng bản tin mới
    </a>
    <a href="<?= url('/admin/media') ?>" class="btn btn-secondary btn-sm">
        Tải lên hình ảnh thật
    </a>
</div>

<!-- Recent Projects Table -->
<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">Dự án cập nhật gần đây</div>
        <a href="<?= url('/admin/projects') ?>" class="btn btn-secondary btn-sm">Xem tất cả</a>
    </div>
    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Tên dự án</th>
                    <th>Lĩnh vực</th>
                    <th>Trạng thái</th>
                    <th>Ngày cập nhật</th>
                    <th style="text-align: right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentProjects)): ?>
                    <?php foreach ($recentProjects as $p): ?>
                        <tr>
                            <td>
                                <strong><?= e($p['title']) ?></strong>
                            </td>
                            <td><?= e($p['area_name'] ?? 'Chưa phân loại') ?></td>
                            <td>
                                <span class="project-status-tag" style="position: static; font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 4px;">
                                    <?= e($p['status']) ?>
                                </span>
                            </td>
                            <td><?= format_date($p['updated_at'] ?? $p['created_at']) ?></td>
                            <td style="text-align: right;">
                                <a href="<?= url('/admin/projects?action=edit&id=' . $p['id']) ?>" class="btn btn-secondary btn-sm">
                                    Sửa
                                </a>
                                <a href="<?= url('/projects/' . $p['slug']) ?>" target="_blank" class="btn btn-secondary btn-sm">
                                    Xem
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--admin-muted);">Chưa có dự án nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Contacts Table -->
<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">Hộp thư trao đổi học thuật gần đây</div>
        <a href="<?= url('/admin/contacts') ?>" class="btn btn-secondary btn-sm">Xem toàn bộ thư</a>
    </div>
    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Người gửi</th>
                    <th>Email</th>
                    <th>Tiêu đề</th>
                    <th>Thời gian</th>
                    <th style="text-align: right;">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentContacts)): ?>
                    <?php foreach ($recentContacts as $c): ?>
                        <tr style="<?= $c['is_read'] == 0 ? 'background-color: #FEFCE8;' : '' ?>">
                            <td><strong><?= e($c['name']) ?></strong></td>
                            <td><?= e($c['email']) ?></td>
                            <td><?= e(truncate_text($c['subject'], 45)) ?></td>
                            <td><?= format_date($c['created_at']) ?></td>
                            <td style="text-align: right;">
                                <?php if ($c['is_read'] == 0): ?>
                                    <span style="font-size: 0.75rem; background-color: #FEE2E2; color: #991B1B; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600;">Mới</span>
                                <?php else: ?>
                                    <span style="font-size: 0.75rem; color: #64748B;">Đã đọc</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--admin-muted);">Chưa có tin nhắn nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/views/footer.php'; ?>
