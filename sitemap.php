<?php
/**
 * LabVale - Dynamic XML Sitemap Generator
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';

header('Content-Type: application/xml; charset=utf-8');

$db = Database::getConnection();

// Fetch URLs
$areas = $db->query("SELECT slug FROM `research_areas` WHERE is_active = 1")->fetchAll();
$projects = $db->query("SELECT slug, updated_at FROM `projects`")->fetchAll();
$articles = $db->query("SELECT slug, published_at FROM `articles` WHERE is_published = 1")->fetchAll();
$news = $db->query("SELECT slug, published_at FROM `news` WHERE is_published = 1")->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Main Static Pages -->
    <url>
        <loc><?= APP_URL ?>/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?= APP_URL ?>/about</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= APP_URL ?>/research-areas</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= APP_URL ?>/projects</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= APP_URL ?>/research</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= APP_URL ?>/news</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc><?= APP_URL ?>/contact</loc>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <url>
        <loc><?= APP_URL ?>/privacy</loc>
        <changefreq>yearly</changefreq>
        <priority>0.5</priority>
    </url>

    <!-- Research Areas -->
    <?php foreach ($areas as $a): ?>
    <url>
        <loc><?= APP_URL ?>/research-areas/<?= e($a['slug']) ?></loc>
        <changefreq>weekly</changefreq>
        <priority>0.85</priority>
    </url>
    <?php endforeach; ?>

    <!-- Projects -->
    <?php foreach ($projects as $p): ?>
    <url>
        <loc><?= APP_URL ?>/projects/<?= e($p['slug']) ?></loc>
        <lastmod><?= date('c', strtotime($p['updated_at'])) ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php endforeach; ?>

    <!-- Articles -->
    <?php foreach ($articles as $art): ?>
    <url>
        <loc><?= APP_URL ?>/research/<?= e($art['slug']) ?></loc>
        <lastmod><?= date('c', strtotime($art['published_at'])) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php endforeach; ?>

    <!-- News -->
    <?php foreach ($news as $n): ?>
    <url>
        <loc><?= APP_URL ?>/news/<?= e($n['slug']) ?></loc>
        <lastmod><?= date('c', strtotime($n['published_at'])) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>
</urlset>
