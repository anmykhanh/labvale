<?php
/**
 * LabVale - Danh sách Lĩnh vực nghiên cứu
 */
$pageTitle = 'Lĩnh vực nghiên cứu — LabVale R&D';
$pageDescription = 'Khám phá 6 trụ cột nghiên cứu công nghệ tại LabVale: Robotics, Drone & UAV, AI, Embedded Systems, Web Software và R&D thực nghiệm.';
$currentNav = 'research-areas';

require_once __DIR__ . '/layout/header.php';
$breadcrumbItems = [
    ['title' => 'Lĩnh vực nghiên cứu', 'url' => null]
];
require __DIR__ . '/layout/breadcrumb.php';
?>

<div class="container" style="padding-bottom: 5rem;">
    <div class="section-header">
        <span class="section-kicker">LĨNH VỰC NGHIÊN CỨU</span>
        <h1 class="section-title">6 Trụ cột công nghệ nền tảng</h1>
        <p class="section-desc">
            LabVale tập trung nguồn lực vào các lĩnh vực kỹ thuật có tính liên kết chặt chẽ, tạo nền móng vững chắc cho việc phát triển các hệ thống tự hành và giải pháp thông minh trong tương lai.
        </p>
    </div>

    <div class="areas-grid" style="margin-bottom: 3rem;">
        <?php foreach ($areas as $area): ?>
            <div class="area-card">
                <div>
                    <div class="area-icon-box">
                        <?= $area['icon_svg'] ?>
                    </div>
                    <h2 class="area-title"><?= e($area['name']) ?></h2>
                    <div class="area-subtitle"><?= e($area['subtitle']) ?></div>
                    <p class="area-desc"><?= e($area['short_desc']) ?></p>
                </div>
                <div>
                    <a href="<?= url('research-areas/' . $area['slug']) ?>" class="area-link">
                        Xem chi tiết &amp; Dự án liên quan
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
