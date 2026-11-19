<?php
/**
 * LabVale - Chi tiết Bài viết nghiên cứu
 * @var array $article
 * @var array $relatedArticles
 */
$pageTitle = e($article['title']) . ' — Góc nghiên cứu LabVale';
$pageDescription = e($article['summary']);
$currentNav = 'research';
$ogImage = image_url_absolute($article['image'] ?? null, 'article-drone-principles.jpg');
$ogType = 'article';

$extraSchemaJson = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'TechArticle',
    'headline' => $article['title'],
    'description' => $article['summary'],
    'image' => $ogImage,
    'author' => [
        '@type' => 'Organization',
        'name' => $article['author'] ?? 'LabVale Research Team'
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'LabVale',
        'logo' => [
            '@type' => 'ImageObject',
            'url' => asset_absolute('images/logo.svg')
        ]
    ],
    'datePublished' => date('c', strtotime($article['published_at'])),
    'dateModified' => date('c', strtotime($article['published_at']))
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

require_once __DIR__ . '/layout/header.php';
$breadcrumbItems = [
    ['title' => 'Góc nghiên cứu', 'url' => url('/research')],
    ['title' => $article['title'], 'url' => null]
];
require __DIR__ . '/layout/breadcrumb.php';
?>

<article class="container container-narrow" style="padding-bottom: 5rem;">
    <header class="detail-header">
        <?php if (!empty($article['area_name'])): ?>
            <span class="detail-kicker">
                <a href="<?= url('research-areas/' . $article['area_slug']) ?>"><?= e($article['area_name']) ?></a>
            </span>
        <?php endif; ?>
        <h1 class="detail-title"><?= e($article['title']) ?></h1>
        <div class="detail-meta-bar">
            <span>Tác giả: <strong><?= e($article['author'] ?? 'Nhóm Nghiên Cứu LabVale') ?></strong></span>
            <span class="article-dot"></span>
            <time datetime="<?= e($article['published_at']) ?>"><?= format_date($article['published_at']) ?></time>
            <span class="article-dot"></span>
            <span>Thời lượng đọc: <?= e($article['reading_time'] ?? '6 phút') ?></span>
        </div>
    </header>

    <div class="detail-hero-media">
        <img src="<?= e(image_url($article['image'] ?? null, 'article-drone-principles.jpg')) ?>" alt="<?= e($article['title']) ?>" class="detail-hero-img" width="1000" height="500" onerror="this.onerror=null; this.src='<?= asset('images/article-drone-principles.jpg') ?>';">
        <div class="detail-hero-caption">
            Tài liệu nghiên cứu kỹ thuật — Bản quyền nội dung thuộc LabVale
        </div>
    </div>

    <!-- Summary Box -->
    <div style="background-color: #F8FAFC; border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 1.25rem 1.5rem; margin-bottom: 2.5rem;">
        <div style="font-size: 0.8rem; font-weight: 700; color: var(--color-tech-blue); text-transform: uppercase; margin-bottom: 0.4rem;">
            Tóm tắt nội dung
        </div>
        <p style="font-size: 1rem; color: #334155; margin: 0; line-height: 1.65;">
            <?= e($article['summary']) ?>
        </p>
    </div>

    <!-- Article Content -->
    <div class="prose">
        <?= render_article_content($article['content']) ?>
    </div>

    <!-- Tags -->
    <?php if (!empty($article['tags'])): 
        $tagList = array_map('trim', explode(',', $article['tags']));
    ?>
        <div style="margin-top: 3rem; padding-top: 1.5rem; border-top: 1px solid var(--color-border); display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
            <span style="font-size: 0.85rem; font-weight: 600; color: var(--color-secondary-text);">Từ khóa:</span>
            <?php foreach ($tagList as $t): ?>
                <span class="tech-tag"><?= e($t) ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Action Bar -->
    <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <a href="<?= url('/research') ?>" class="btn btn-secondary btn-sm">
            &larr; Xem tất cả bài viết nghiên cứu
        </a>
        <button type="button" class="btn btn-secondary btn-sm" data-copy-link>
            Sao chép liên kết bài viết
        </button>
    </div>

    <!-- Related Articles -->
    <?php if (!empty($relatedArticles)): ?>
        <div style="margin-top: 4rem; padding-top: 2.5rem; border-top: 1px solid var(--color-border);">
            <h2 style="font-size: 1.35rem; color: var(--color-primary-navy); margin-bottom: 1.5rem;">
                Bài viết cùng chuyên mục
            </h2>
            <div class="articles-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
                <?php foreach ($relatedArticles as $art): ?>
                    <?php 
                        $article = $art; 
                        require __DIR__ . '/layout/article-card.php'; 
                    ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</article>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
