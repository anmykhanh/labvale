<?php
/**
 * LabVale - Article Card Component
 * @var array $article
 */
$articleUrl = url('research/' . $article['slug']);
$imgUrl = !empty($article['image']) ? asset('images/' . $article['image']) : asset('images/article-drone-principles.jpg');
?>
<article class="article-card">
    <div class="article-thumbnail-wrapper">
        <a href="<?= $articleUrl ?>" tabindex="-1" aria-hidden="true">
            <img src="<?= e($imgUrl) ?>" alt="<?= e($article['title']) ?>" class="article-thumbnail" loading="lazy" width="600" height="340">
        </a>
    </div>
    <div class="article-body">
        <div class="article-meta">
            <?php if (!empty($article['area_name'])): ?>
                <span class="article-category"><?= e($article['area_name']) ?></span>
                <span class="article-dot"></span>
            <?php endif; ?>
            <span><?= e($article['reading_time'] ?? '5 phút đọc') ?></span>
            <span class="article-dot"></span>
            <time datetime="<?= e($article['published_at']) ?>"><?= format_date($article['published_at']) ?></time>
        </div>

        <h3 class="article-title">
            <a href="<?= $articleUrl ?>"><?= e($article['title']) ?></a>
        </h3>

        <p class="article-summary"><?= e(truncate_text($article['summary'], 120)) ?></p>

        <div class="article-footer">
            <a href="<?= $articleUrl ?>" class="area-link">
                Đọc bài viết
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
            <span style="font-size: 0.775rem; color: #94A3B8;"><?= e($article['author'] ?? 'LabVale R&D') ?></span>
        </div>
    </div>
</article>
