<?php
/**
 * LabVale - Danh sách Dự án & Thử nghiệm
 * @var array $projects
 * @var array $areas
 * @var string $selectedArea
 * @var string $selectedStatus
 */
$pageTitle = 'Dự án & Thử nghiệm — LabVale R&D';
$pageDescription = 'Danh sách các dự án nghiên cứu, thiết kế cơ điện tử, mạch nhúng và nền tảng tự hành thực tế tại LabVale.';
$currentNav = 'projects';

require_once __DIR__ . '/layout/header.php';
$breadcrumbItems = [
    ['title' => 'Dự án & Thử nghiệm', 'url' => null]
];
require __DIR__ . '/layout/breadcrumb.php';
?>

<div class="container" style="padding-bottom: 5rem;">
    <div class="section-header">
        <span class="section-kicker">THỰC HÀNH KỸ THUẬT</span>
        <h1 class="section-title">Dự án &amp; Thử nghiệm thực tế</h1>
        <p class="section-desc">
            Toàn bộ các dự án dưới đây được thực hiện trực tiếp tại LabVale. Chúng tôi cập nhật trung thực tiến độ kỹ thuật, từ giai đoạn khảo sát ý tưởng đến khi đo kiểm mẫu thử hoàn tất.
        </p>
    </div>

    <!-- Filter Controls -->
    <div class="filter-bar">
        <span style="font-size: 0.85rem; font-weight: 600; color: var(--color-primary-navy); margin-right: 0.5rem;">Lĩnh vực:</span>
        <a href="<?= url('/projects' . ($selectedStatus ? '?status=' . urlencode($selectedStatus) : '')) ?>" 
           class="filter-btn <?= empty($selectedArea) ? 'active' : '' ?>">
            Tất cả
        </a>
        <?php foreach ($areas as $a): ?>
            <?php 
                $link = url('/projects?area=' . urlencode($a['slug']) . ($selectedStatus ? '&status=' . urlencode($selectedStatus) : ''));
            ?>
            <a href="<?= $link ?>" class="filter-btn <?= $selectedArea === $a['slug'] ? 'active' : '' ?>">
                <?= e($a['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Status Sub-filter -->
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 2.5rem; align-items: center;">
        <span style="font-size: 0.825rem; font-weight: 600; color: var(--color-secondary-text); margin-right: 0.5rem;">Trạng thái:</span>
        <?php 
            $statuses = ['Ý tưởng', 'Đang nghiên cứu', 'Đang phát triển', 'Hoàn thành'];
        ?>
        <a href="<?= url('/projects' . ($selectedArea ? '?area=' . urlencode($selectedArea) : '')) ?>" 
           class="filter-btn <?= empty($selectedStatus) ? 'active' : '' ?>" style="font-size: 0.8rem;">
            Tất cả trạng thái
        </a>
        <?php foreach ($statuses as $st): ?>
            <?php 
                $link = url('/projects?status=' . urlencode($st) . ($selectedArea ? '&area=' . urlencode($selectedArea) : ''));
            ?>
            <a href="<?= $link ?>" class="filter-btn <?= $selectedStatus === $st ? 'active' : '' ?>" style="font-size: 0.8rem;">
                <?= e($st) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Project Grid -->
    <?php if (!empty($projects)): ?>
        <div class="projects-grid">
            <?php foreach ($projects as $project): ?>
                <?php require __DIR__ . '/layout/project-card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
            </svg>
            <h3 class="empty-state-title">Đang chuẩn bị công bố</h3>
            <p class="empty-state-desc">
                Chưa có dự án nào thuộc tiêu chí lọc này. LabVale chỉ công bố dự án khi đã có dữ liệu đo kiểm phần cứng và mã nguồn nguyên mẫu rõ ràng.
            </p>
            <div style="margin-top: 1.5rem;">
                <a href="<?= url('/projects') ?>" class="btn btn-secondary btn-sm">Xem tất cả dự án</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
