<?php
/**
 * LabVale - Header Component
 * @var string $pageTitle
 * @var string $pageDescription
 * @var string $currentNav
 */
$pageTitle = $pageTitle ?? get_setting('site_name', 'LabVale') . ' — ' . get_setting('site_slogan', 'Research. Innovate. Create Value.');
$pageDescription = $pageDescription ?? get_setting('site_description', 'LabVale là dự án nghiên cứu và phát triển công nghệ độc lập tại Việt Nam.');
$currentNav = $currentNav ?? '';

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$reqPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$canonicalUrl = $protocol . $host . $reqPath;

$ogImage = $ogImage ?? asset_absolute('images/hero-lab.jpg');
if (!str_starts_with($ogImage, 'http://') && !str_starts_with($ogImage, 'https://')) {
    $ogImage = rtrim(APP_URL, '/') . '/' . ltrim($ogImage, '/');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <meta name="keywords" content="<?= e(get_setting('site_keywords', 'LabVale, nghiên cứu công nghệ, robotics, drone, UAV, AI')) ?>">
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">

    <!-- Open Graph / Meta -->
    <meta property="og:type" content="<?= !empty($ogType) ? e($ogType) : 'website' ?>">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <meta property="og:url" content="<?= e($canonicalUrl) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta property="og:site_name" content="LabVale">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($pageTitle) ?>">
    <meta name="twitter:description" content="<?= e($pageDescription) ?>">
    <meta name="twitter:image" content="<?= e($ogImage) ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">

    <!-- Pure Component CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ResearchOrganization",
      "name": "LabVale",
      "alternateName": "LabVale R&D",
      "url": "<?= APP_URL ?>",
      "logo": "<?= asset_absolute('images/logo.svg') ?>",
      "description": "Dự án nghiên cứu và phát triển công nghệ độc lập tại Việt Nam, tập trung vào robotics, UAV/drone, trí tuệ nhân tạo, hệ thống nhúng và giải pháp kỹ thuật thực tiễn.",
      "sameAs": [
        "<?= get_setting('github_url', 'https://github.com/labvale-rd') ?>"
      ]
    }
    </script>
    <?php if (!empty($extraSchemaJson)): ?>
    <script type="application/ld+json">
    <?= $extraSchemaJson ?>
    </script>
    <?php endif; ?>
</head>
<body>
    <!-- Site Header -->
    <header class="site-header">
        <div class="container">
            <div class="header-inner">
                <!-- Brand Logo -->
                <a href="<?= url('/') ?>" class="brand-link" aria-label="LabVale Trang chủ">
                    <img src="<?= asset('images/logo.svg') ?>" alt="Logo LabVale" class="brand-logo-img" width="160" height="38">
                </a>

                <!-- Desktop Navigation -->
                <nav aria-label="Điều hướng chính" class="nav-desktop">
                    <a href="<?= url('/') ?>" class="nav-link <?= $currentNav === 'home' ? 'active' : '' ?>">Trang chủ</a>
                    <a href="<?= url('/about') ?>" class="nav-link <?= $currentNav === 'about' ? 'active' : '' ?>">Giới thiệu</a>
                    <a href="<?= url('/research-areas') ?>" class="nav-link <?= $currentNav === 'research-areas' ? 'active' : '' ?>">Lĩnh vực</a>
                    <a href="<?= url('/projects') ?>" class="nav-link <?= $currentNav === 'projects' ? 'active' : '' ?>">Dự án</a>
                    <a href="<?= url('/research') ?>" class="nav-link <?= $currentNav === 'research' ? 'active' : '' ?>">Nghiên cứu</a>
                    <a href="<?= url('/news') ?>" class="nav-link <?= $currentNav === 'news' ? 'active' : '' ?>">Tin tức</a>
                    <a href="<?= url('/contact') ?>" class="nav-link <?= $currentNav === 'contact' ? 'active' : '' ?>">Liên hệ</a>
                </nav>

                <!-- Mobile Menu Button -->
                <button class="mobile-nav-toggle" aria-label="Mở trình đơn" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Drawer Navigation -->
    <div class="mobile-drawer" aria-label="Trình đơn di động">
        <ul class="mobile-nav-list">
            <li><a href="<?= url('/') ?>" class="mobile-nav-link <?= $currentNav === 'home' ? 'active' : '' ?>">Trang chủ</a></li>
            <li><a href="<?= url('/about') ?>" class="mobile-nav-link <?= $currentNav === 'about' ? 'active' : '' ?>">Giới thiệu</a></li>
            <li><a href="<?= url('/research-areas') ?>" class="mobile-nav-link <?= $currentNav === 'research-areas' ? 'active' : '' ?>">Lĩnh vực nghiên cứu</a></li>
            <li><a href="<?= url('/projects') ?>" class="mobile-nav-link <?= $currentNav === 'projects' ? 'active' : '' ?>">Dự án và thử nghiệm</a></li>
            <li><a href="<?= url('/research') ?>" class="mobile-nav-link <?= $currentNav === 'research' ? 'active' : '' ?>">Góc nghiên cứu kỹ thuật</a></li>
            <li><a href="<?= url('/news') ?>" class="mobile-nav-link <?= $currentNav === 'news' ? 'active' : '' ?>">Tin tức & Cập nhật</a></li>
            <li><a href="<?= url('/contact') ?>" class="mobile-nav-link <?= $currentNav === 'contact' ? 'active' : '' ?>">Liên hệ hợp tác</a></li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <main>
