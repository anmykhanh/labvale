<?php
/**
 * LabVale - Chi tiết Lĩnh vực nghiên cứu
 * @var array $area
 * @var array $relatedProjects
 * @var array $relatedArticles
 */
$pageTitle = e($area['name']) . ' — Lĩnh vực nghiên cứu — LabVale';
$pageDescription = e($area['subtitle']) . '. ' . e($area['short_desc']);
$currentNav = 'research-areas';
$ogImage = image_url_absolute($area['image'] ?? null, 'hero-lab.jpg');

require_once __DIR__ . '/layout/header.php';
$breadcrumbItems = [
    ['title' => 'Lĩnh vực nghiên cứu', 'url' => url('/research-areas')],
    ['title' => $area['name'], 'url' => null]
];
require __DIR__ . '/layout/breadcrumb.php';
?>

<div class="container container-narrow" style="padding-bottom: 5rem;">
    <div class="detail-header">
        <span class="detail-kicker">LĨNH VỰC NGHIÊN CỨU</span>
        <h1 class="detail-title"><?= e($area['name']) ?></h1>
        <div class="detail-meta-bar">
            <span><?= e($area['subtitle']) ?></span>
        </div>
    </div>

    <div class="detail-hero-media">
        <img src="<?= e(image_url($area['image'] ?? null, 'hero-lab.jpg')) ?>" alt="<?= e($area['name']) ?>" class="detail-hero-img" width="1000" height="500" onerror="this.onerror=null; this.src='<?= asset('images/hero-lab.jpg') ?>';">
        <div class="detail-hero-caption">
            Nghiên cứu và đo kiểm thực tế trong lĩnh vực <?= e($area['name']) ?>
        </div>
    </div>

    <div class="prose" style="margin-bottom: 3.5rem;">
        <?= render_article_content($area['full_content']) ?>
    </div>

    <!-- Related Projects -->
    <?php if (!empty($relatedProjects)): ?>
        <div style="margin-top: 3.5rem; padding-top: 2.5rem; border-top: 1px solid var(--color-border);">
            <h2 style="font-size: 1.4rem; color: var(--color-primary-navy); margin-bottom: 1.5rem;">
                Dự án &amp; Thử nghiệm trong lĩnh vực này
            </h2>
            <div class="projects-grid">
                <?php foreach ($relatedProjects as $project): ?>
                    <?php require __DIR__ . '/layout/project-card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Related Articles -->
    <?php if (!empty($relatedArticles)): ?>
        <div style="margin-top: 3.5rem; padding-top: 2.5rem; border-top: 1px solid var(--color-border);">
            <h2 style="font-size: 1.4rem; color: var(--color-primary-navy); margin-bottom: 1.5rem;">
                Tài liệu &amp; Bài viết kỹ thuật liên quan
            </h2>
            <div class="articles-grid" style="grid-template-columns: 1fr;">
                <?php foreach ($relatedArticles as $article): ?>
                    <?php require __DIR__ . '/layout/article-card.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
