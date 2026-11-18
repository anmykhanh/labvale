<?php
/**
 * LabVale - Trang chủ
 */
$pageTitle = 'LabVale — Nghiên cứu hôm nay. Kiến tạo giá trị ngày mai.';
$pageDescription = 'LabVale là dự án nghiên cứu và phát triển công nghệ độc lập tại Việt Nam, tập trung vào robotics, UAV/drone, trí tuệ nhân tạo và các giải pháp kỹ thuật có giá trị thực tiễn.';
$currentNav = 'home';
$ogImage = asset_absolute('images/hero-lab.jpg');

require_once __DIR__ . '/layout/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div>
                <div class="hero-badge">
                    <span class="hero-badge-pulse" aria-hidden="true"></span>
                    <span>INDEPENDENT RESEARCH &amp; DEVELOPMENT</span>
                </div>
                <h1 class="hero-title">Nghiên cứu hôm nay.<br>Kiến tạo giá trị ngày mai.</h1>
                <p class="hero-desc">
                    LabVale là dự án nghiên cứu và phát triển công nghệ, tập trung vào robot, thiết bị bay không người lái, trí tuệ nhân tạo và những giải pháp kỹ thuật có giá trị thực tiễn.
                </p>
                <div class="hero-actions">
                    <a href="<?= url('/projects') ?>" class="btn btn-primary">
                        Khám phá dự án
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                    <a href="<?= url('/about') ?>" class="btn btn-secondary">
                        Tìm hiểu LabVale
                    </a>
                </div>
            </div>
            <div>
                <div class="hero-image-wrapper">
                    <img src="<?= asset('images/hero-lab.jpg') ?>" alt="Bàn thí nghiệm nghiên cứu kỹ thuật thực tế tại LabVale" class="hero-img" width="800" height="480">
                    <div class="hero-img-caption">
                        Không gian nghiên cứu cơ điện tử và thử nghiệm phần cứng thực tế
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Giới thiệu ngắn LabVale -->
<section class="section section-bg-white">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">TỔNG QUAN DỰ ÁN</span>
            <h2 class="section-title">Tinh thần độc lập &amp; Thực nghiệm khoa học</h2>
            <p class="section-desc">
                Được thành lập với mục tiêu xây dựng một không gian nghiên cứu công nghệ chân thực tại Việt Nam, LabVale tập trung vào việc biến lý thuyết kỹ thuật thành các mô hình mẫu có thể đo đạc, kiểm chứng và mang lại hiệu quả thực tế.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-bottom: 2.5rem;">
            <div style="padding: 1.5rem; background-color: #F8FAFC; border: 1px solid var(--color-border); border-radius: var(--radius-md);">
                <div style="font-weight: 700; color: var(--color-primary-navy); margin-bottom: 0.5rem; font-size: 1.1rem;">
                    Ý nghĩa thương hiệu
                </div>
                <p style="font-size: 0.925rem; color: #475569; margin: 0;">
                    <strong>Lab</strong> (Laboratory) là phòng thí nghiệm, <strong>Vale</strong> là thung lũng, gợi nhắc đến <em>Value</em> (giá trị). LabVale mang ý nghĩa "Thung lũng nghiên cứu, nơi những ý tưởng được phát triển thành công nghệ và sản phẩm có giá trị thực tiễn".
                </p>
            </div>

            <div style="padding: 1.5rem; background-color: #F8FAFC; border: 1px solid var(--color-border); border-radius: var(--radius-md);">
                <div style="font-weight: 700; color: var(--color-primary-navy); margin-bottom: 0.5rem; font-size: 1.1rem;">
                    Giai đoạn phát triển
                </div>
                <p style="font-size: 0.925rem; color: #475569; margin: 0;">
                    LabVale đang trong giai đoạn hình thành và nghiên cứu ban đầu. Chúng tôi trân trọng tính trung thực trong khoa học kỹ thuật, tập trung hoàn thiện trang thiết bị đo kiểm và tích lũy tri thức qua từng lần thử nghiệm thực tế.
                </p>
            </div>

            <div style="padding: 1.5rem; background-color: #F8FAFC; border: 1px solid var(--color-border); border-radius: var(--radius-md);">
                <div style="font-weight: 700; color: var(--color-primary-navy); margin-bottom: 0.5rem; font-size: 1.1rem;">
                    Khẩu hiệu cốt lõi
                </div>
                <p style="font-size: 0.925rem; color: #475569; margin: 0;">
                    <strong>Research. Innovate. Create Value.</strong> — Nghiên cứu có kỷ luật, sáng tạo có kiểm chứng, và kiến tạo giá trị thiết thực cho cộng đồng kỹ thuật.
                </p>
            </div>
        </div>

        <div>
            <a href="<?= url('/about') ?>" class="area-link" style="font-size: 0.95rem;">
                Đọc thêm về triết lý và định hướng phát triển của LabVale
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Lĩnh vực nghiên cứu -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-kicker">LĨNH VỰC HOẠT ĐỘNG</span>
            <h2 class="section-title">6 Trụ cột nghiên cứu &amp; Phát triển</h2>
            <p class="section-desc">
                Các hướng nghiên cứu công nghệ được LabVale lựa chọn dựa trên khả năng ứng dụng thực tế và tính liên kết chặt chẽ trong hệ sinh thái kỹ thuật hiện đại.
            </p>
        </div>

        <div class="areas-grid">
            <?php foreach ($researchAreas as $area): ?>
                <div class="area-card">
                    <div>
                        <div class="area-icon-box">
                            <?= $area['icon_svg'] ?>
                        </div>
                        <h3 class="area-title"><?= e($area['name']) ?></h3>
                        <div class="area-subtitle"><?= e($area['subtitle']) ?></div>
                        <p class="area-desc"><?= e($area['short_desc']) ?></p>
                    </div>
                    <div>
                        <a href="<?= url('research-areas/' . $area['slug']) ?>" class="area-link">
                            Xem chi tiết lĩnh vực
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
</section>

<!-- Dự án và thử nghiệm -->
<section class="section section-bg-white">
    <div class="container">
        <div style="display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span class="section-kicker">THỰC HÀNH KỸ THUẬT</span>
                <h2 class="section-title" style="margin-bottom: 0;">Dự án và thử nghiệm thực tế</h2>
            </div>
            <a href="<?= url('/projects') ?>" class="btn btn-secondary btn-sm">
                Xem toàn bộ dự án &rarr;
            </a>
        </div>

        <?php if (!empty($featuredProjects)): ?>
            <div class="projects-grid">
                <?php foreach ($featuredProjects as $project): ?>
                    <?php require __DIR__ . '/layout/project-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p class="empty-state-desc">Các dự án đang trong giai đoạn đo kiểm và chuẩn bị công bố tài liệu kỹ thuật.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Góc nghiên cứu -->
<section class="section">
    <div class="container">
        <div style="display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <span class="section-kicker">TRI THỨC &amp; KỸ THUẬT</span>
                <h2 class="section-title" style="margin-bottom: 0;">Góc nghiên cứu</h2>
            </div>
            <a href="<?= url('/research') ?>" class="btn btn-secondary btn-sm">
                Xem tất cả bài viết &rarr;
            </a>
        </div>

        <?php if (!empty($recentArticles)): ?>
            <div class="articles-grid">
                <?php foreach ($recentArticles as $article): ?>
                    <?php require __DIR__ . '/layout/article-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Triết lý LabVale -->
<section class="philosophy-banner">
    <div class="container container-narrow">
        <blockquote class="philosophy-quote">
            &ldquo;Chúng tôi tin rằng giá trị của nghiên cứu không chỉ nằm ở những ý tưởng mới, mà còn ở khả năng biến chúng thành giải pháp hữu ích cho cuộc sống.&rdquo;
        </blockquote>
        <div class="philosophy-author">Triết lý nghiên cứu LabVale</div>
    </div>
</section>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
