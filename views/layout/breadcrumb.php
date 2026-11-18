<?php
/**
 * LabVale - Breadcrumb Component
 * @var array $items Array of ['title' => string, 'url' => string|null]
 */
if (!empty($items)):
?>
<nav aria-label="Đường dẫn điều hướng" class="breadcrumb-nav">
    <div class="container">
        <ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
            <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="<?= url('/') ?>" itemprop="item"><span itemprop="name">Trang chủ</span></a>
                <meta itemprop="position" content="1">
            </li>
            <?php 
            $pos = 2;
            foreach ($items as $index => $item): 
                $isLast = ($index === array_key_last($items));
            ?>
                <li class="breadcrumb-separator" aria-hidden="true">/</li>
                <li class="breadcrumb-item <?= $isLast ? 'active' : '' ?>" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <?php if (!$isLast && !empty($item['url'])): ?>
                        <a href="<?= e($item['url']) ?>" itemprop="item"><span itemprop="name"><?= e($item['title']) ?></span></a>
                    <?php else: ?>
                        <span itemprop="name"><?= e($item['title']) ?></span>
                    <?php endif; ?>
                    <meta itemprop="position" content="<?= $pos++ ?>">
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</nav>
<?php endif; ?>
