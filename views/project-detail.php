<?php
/**
 * LabVale - Chi tiết Dự án
 * @var array $project
 * @var array $relatedProjects
 */
$pageTitle = e($project['title']) . ' — Dự án LabVale';
$pageDescription = e($project['summary']);
$currentNav = 'projects';
$ogImage = asset_absolute('images/' . $project['image']);

$extraSchemaJson = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'CreativeWork',
    'name' => $project['title'],
    'description' => $project['summary'],
    'image' => $ogImage,
    'creator' => [
        '@type' => 'Organization',
        'name' => 'LabVale'
    ],
    'dateCreated' => date('c', strtotime($project['created_at'])),
    'dateModified' => date('c', strtotime($project['updated_at'] ?? $project['created_at']))
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

$statusClassMap = [
    'Ý tưởng' => 'status-y-tuong',
    'Đang nghiên cứu' => 'status-dang-nghien-cuu',
    'Đang phát triển' => 'status-dang-phat-trien',
    'Hoàn thành' => 'status-hoan-thanh',
];
$statusClass = $statusClassMap[$project['status']] ?? 'status-dang-nghien-cuu';

require_once __DIR__ . '/layout/header.php';
$breadcrumbItems = [
    ['title' => 'Dự án & Thử nghiệm', 'url' => url('/projects')],
    ['title' => $project['title'], 'url' => null]
];
require __DIR__ . '/layout/breadcrumb.php';
?>

<div class="container container-narrow" style="padding-bottom: 5rem;">
    <div class="detail-header">
        <span class="detail-kicker">
            <a href="<?= url('research-areas/' . $project['area_slug']) ?>"><?= e($project['area_name']) ?></a>
        </span>
        <h1 class="detail-title"><?= e($project['title']) ?></h1>
        <div class="detail-meta-bar">
            <span>Trạng thái: <strong class="project-status-tag <?= $statusClass ?>" style="position: static;"><?= e($project['status']) ?></strong></span>
            <span class="article-dot"></span>
            <span>Khởi động: <?= format_date($project['created_at']) ?></span>
            <?php if (!empty($project['github_url'])): ?>
                <span class="article-dot"></span>
                <a href="<?= e($project['github_url']) ?>" target="_blank" rel="noopener noreferrer" style="font-weight: 600;">
                    Kho mã nguồn GitHub &rarr;
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Hero Prototype Image -->
    <div class="detail-hero-media">
        <img src="<?= asset('images/' . $project['image']) ?>" alt="<?= e($project['title']) ?>" class="detail-hero-img" width="1000" height="500">
        <?php if (!empty($project['image_caption'])): ?>
            <div class="detail-hero-caption">
                <?= e($project['image_caption']) ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tech Stack Summary Box -->
    <?php if (!empty($project['tech_stack'])): 
        $tags = array_map('trim', explode(',', $project['tech_stack']));
    ?>
        <div class="spec-box">
            <div class="spec-title">Công nghệ &amp; Nền tảng ứng dụng</div>
            <div class="project-tech-tags" style="margin-bottom: 0;">
                <?php foreach ($tags as $tag): ?>
                    <span class="tech-tag" style="font-size: 0.85rem; padding: 0.3rem 0.65rem;"><?= e($tag) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Full Content -->
    <div class="prose">
        <?= render_article_content($project['content']) ?>
    </div>

    <!-- Share / Action Bar -->
    <div style="margin-top: 3.5rem; padding-top: 2rem; border-top: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <a href="<?= url('/projects') ?>" class="btn btn-secondary btn-sm">
            &larr; Quay lại danh sách dự án
        </a>
        <button type="button" class="btn btn-secondary btn-sm" data-copy-link>
            Sao chép liên kết dự án
        </button>
    </div>

    <!-- Related Projects -->
    <?php if (!empty($relatedProjects)): ?>
        <div style="margin-top: 4rem; padding-top: 2.5rem; border-top: 1px solid var(--color-border);">
            <h2 style="font-size: 1.35rem; color: var(--color-primary-navy); margin-bottom: 1.5rem;">
                Các dự án cùng lĩnh vực
            </h2>
            <div class="projects-grid">
                <?php foreach ($relatedProjects as $p): ?>
                    <?php 
                        $project = $p; 
                        require __DIR__ . '/layout/project-card.php'; 
                    ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
