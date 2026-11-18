<?php
/**
 * LabVale - Góc nghiên cứu kỹ thuật
 * @var array $articles
 * @var array $areas
 * @var string $selectedArea
 */
$pageTitle = 'Góc nghiên cứu kỹ thuật — LabVale R&D';
$pageDescription = 'Chia sẻ kiến thức công nghệ thực tế, nhật ký phát triển phần cứng, thị giác máy tính và hệ thống tự hành tại LabVale.';
$currentNav = 'research';

require_once __DIR__ . '/layout/header.php';
$breadcrumbItems = [
    ['title' => 'Góc nghiên cứu', 'url' => null]
];
require __DIR__ . '/layout/breadcrumb.php';
?>

<div class="container" style="padding-bottom: 5rem;">
    <div class="section-header">
        <span class="section-kicker">TRI THỨC &amp; KỸ THUẬT</span>
        <h1 class="section-title">Góc nghiên cứu công nghệ</h1>
        <p class="section-desc">
            Nơi đội ngũ LabVale ghi lại những phân tích kỹ thuật chuyên sâu, nguyên lý vật lý, kiến trúc hệ thống và những bài học rút ra từ quá trình thử nghiệm thực tế.
        </p>
    </div>

    <!-- Category Filter -->
    <div class="filter-bar">
        <span style="font-size: 0.85rem; font-weight: 600; color: var(--color-primary-navy); margin-right: 0.5rem;">Chủ đề:</span>
        <a href="<?= url('/research') ?>" class="filter-btn <?= empty($selectedArea) ? 'active' : '' ?>">
            Tất cả chủ đề
        </a>
        <?php foreach ($areas as $a): ?>
            <a href="<?= url('/research?area=' . urlencode($a['slug'])) ?>" class="filter-btn <?= $selectedArea === $a['slug'] ? 'active' : '' ?>">
                <?= e($a['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Articles Grid -->
    <?php if (!empty($articles)): ?>
        <div class="articles-grid">
            <?php foreach ($articles as $article): ?>
                <?php require __DIR__ . '/layout/article-card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <h3 class="empty-state-title">Đang cập nhật bài viết mới</h3>
            <p class="empty-state-desc">Chưa có bài viết nào thuộc chủ đề được chọn. Chúng tôi sẽ sớm bổ sung tài liệu sau khi hoàn tất các đợt đo kiểm.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
