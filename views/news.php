<?php
/**
 * LabVale - Tin tức & Cập nhật
 * @var array $newsList
 */
$pageTitle = 'Tin tức & Cập nhật — LabVale R&D';
$pageDescription = 'Thông tin cập nhật mới nhất về các thử nghiệm, cột mốc và hoạt động nghiên cứu công nghệ tại LabVale.';
$currentNav = 'news';

require_once __DIR__ . '/layout/header.php';
$breadcrumbItems = [
    ['title' => 'Tin tức & Cập nhật', 'url' => null]
];
require __DIR__ . '/layout/breadcrumb.php';
?>

<div class="container" style="padding-bottom: 5rem;">
    <div class="section-header">
        <span class="section-kicker">HOẠT ĐỘNG THỰC NGHIỆM</span>
        <h1 class="section-title">Tin tức &amp; Nhật ký nghiên cứu</h1>
        <p class="section-desc">
            Cập nhật các bước tiến thực tế, báo cáo thử nghiệm thiết bị và hoạt động thường nhật tại phòng nghiên cứu LabVale.
        </p>
    </div>

    <?php if (!empty($newsList)): ?>
        <div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 860px; margin: 0 auto;">
            <?php foreach ($newsList as $item): 
                $newsUrl = url('news/' . $item['slug']);
                $imgUrl = image_url($item['image'] ?? null, 'hero-lab.jpg');
                $fallbackUrl = asset('images/hero-lab.jpg');
            ?>
                <article class="news-card-horizontal">
                    <div class="news-card-media">
                        <a href="<?= $newsUrl ?>" tabindex="-1" aria-hidden="true">
                            <img src="<?= e($imgUrl) ?>" alt="<?= e($item['title']) ?>" loading="lazy" onerror="this.onerror=null; this.src='<?= $fallbackUrl ?>';">
                        </a>
                    </div>
                    <div class="news-card-content">
                        <div>
                            <div style="font-size: 0.8rem; color: var(--color-secondary-text); margin-bottom: 0.5rem;">
                                <time datetime="<?= e($item['published_at']) ?>"><?= format_date($item['published_at']) ?></time>
                            </div>
                            <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--color-primary-navy); margin-bottom: 0.75rem; line-height: 1.4;">
                                <a href="<?= $newsUrl ?>"><?= e($item['title']) ?></a>
                            </h2>
                            <p style="font-size: 0.925rem; color: var(--color-secondary-text); line-height: 1.6; margin-bottom: 1rem;">
                                <?= e($item['summary']) ?>
                            </p>
                        </div>
                        <div>
                            <a href="<?= $newsUrl ?>" class="area-link">
                                Đọc chi tiết nhật ký
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <h3 class="empty-state-title">Chưa có tin tức mới</h3>
            <p class="empty-state-desc">Các bản tin cập nhật thử nghiệm sẽ được đăng tải định kỳ sau mỗi đợt đo kiểm.</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
