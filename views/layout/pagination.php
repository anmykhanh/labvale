<?php
/**
 * LabVale - Pagination Component
 * @var int $currentPage
 * @var int $totalPages
 * @var string $baseUrl
 */
if ($totalPages > 1):
?>
<nav aria-label="Phân trang" style="margin-top: 3rem; display: flex; justify-content: center; gap: 0.5rem;">
    <?php if ($currentPage > 1): ?>
        <a href="<?= e($baseUrl) ?>page=<?= $currentPage - 1 ?>" class="btn btn-secondary btn-sm" aria-label="Trang trước">
            &larr; Trước
        </a>
    <?php endif; ?>

    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <a href="<?= e($baseUrl) ?>page=<?= $p ?>" class="btn <?= $p === $currentPage ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
            <?= $p ?>
        </a>
    <?php endfor; ?>

    <?php if ($currentPage < $totalPages): ?>
        <a href="<?= e($baseUrl) ?>page=<?= $currentPage + 1 ?>" class="btn btn-secondary btn-sm" aria-label="Trang sau">
            Sau &rarr;
        </a>
    <?php endif; ?>
</nav>
<?php endif; ?>
