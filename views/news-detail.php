<?php
/**
 * LabVale - Chi tiết Tin tức
 * @var array $item
 * @var array $recentNews
 */
$pageTitle = e($item['title']) . ' — Tin tức LabVale';
$pageDescription = e($item['summary']);
$currentNav = 'news';
$ogImage = image_url_absolute($item['image'] ?? null, 'hero-lab.jpg');
$ogType = 'article';

$extraSchemaJson = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'NewsArticle',
    'headline' => $item['title'],
    'description' => $item['summary'],
    'image' => $ogImage,
    'author' => [
        '@type' => 'Organization',
        'name' => 'LabVale Research Team'
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'LabVale',
        'logo' => [
            '@type' => 'ImageObject',
            'url' => asset_absolute('images/logo.svg')
        ]
    ],
    'datePublished' => date('c', strtotime($item['published_at'])),
    'dateModified' => date('c', strtotime($item['published_at']))
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

require_once __DIR__ . '/layout/header.php';
$breadcrumbItems = [
    ['title' => 'Tin tức & Cập nhật', 'url' => url('/news')],
    ['title' => $item['title'], 'url' => null]
];
require __DIR__ . '/layout/breadcrumb.php';
?>

<article class="container container-narrow" style="padding-bottom: 5rem;">
    <header class="detail-header">
        <span class="detail-kicker">NHẬT KÝ THỰC NGHIỆM</span>
        <h1 class="detail-title"><?= e($item['title']) ?></h1>
        <div class="detail-meta-bar">
            <span>Đăng ngày: <time datetime="<?= e($item['published_at']) ?>"><?= format_date($item['published_at']) ?></time></span>
            <span class="article-dot"></span>
            <span>Nguồn: Phòng nghiên cứu LabVale</span>
        </div>
    </header>

    <div class="detail-hero-media">
        <img src="<?= e(image_url($item['image'] ?? null, 'hero-lab.jpg')) ?>" alt="<?= e($item['title']) ?>" class="detail-hero-img" width="1000" height="500" onerror="this.onerror=null; this.src='<?= asset('images/hero-lab.jpg') ?>';">
        <div class="detail-hero-caption">
            Ghi nhận tại phòng thực nghiệm kỹ thuật LabVale
        </div>
    </div>

    <!-- Summary Box -->
    <div style="background-color: #F8FAFC; border: 1px solid var(--color-border); border-radius: var(--radius-sm); padding: 1.25rem 1.5rem; margin-bottom: 2.5rem;">
        <p style="font-size: 1.05rem; color: #1E293B; margin: 0; line-height: 1.65; font-weight: 500;">
            <?= e($item['summary']) ?>
        </p>
    </div>

    <div class="prose">
        <?= render_article_content($item['content']) ?>
    </div>

    <div style="margin-top: 3.5rem; padding-top: 2rem; border-top: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <a href="<?= url('/news') ?>" class="btn btn-secondary btn-sm">
            &larr; Xem tất cả bản tin
        </a>
        <button type="button" class="btn btn-secondary btn-sm" data-copy-link>
            Sao chép liên kết bản tin
        </button>
    </div>
</article>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
