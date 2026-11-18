<?php
/**
 * LabVale - Project Card Component
 * @var array $project
 */
$statusClassMap = [
    'Ý tưởng' => 'status-y-tuong',
    'Đang nghiên cứu' => 'status-dang-nghien-cuu',
    'Đang phát triển' => 'status-dang-phat-trien',
    'Hoàn thành' => 'status-hoan-thanh',
];
$statusClass = $statusClassMap[$project['status']] ?? 'status-dang-nghien-cuu';
$projectUrl = url('projects/' . $project['slug']);
$imgUrl = !empty($project['image']) ? asset('images/' . $project['image']) : asset('images/project-rover.jpg');
?>
<article class="project-card">
    <div class="project-thumbnail-wrapper">
        <a href="<?= $projectUrl ?>" tabindex="-1" aria-hidden="true">
            <img src="<?= e($imgUrl) ?>" alt="<?= e($project['title']) ?>" class="project-thumbnail" loading="lazy" width="600" height="340">
        </a>
        <span class="project-status-tag <?= $statusClass ?>"><?= e($project['status']) ?></span>
    </div>
    <div class="project-body">
        <?php if (!empty($project['area_name'])): ?>
            <div class="project-area-badge"><?= e($project['area_name']) ?></div>
        <?php endif; ?>
        <h3 class="project-title">
            <a href="<?= $projectUrl ?>"><?= e($project['title']) ?></a>
        </h3>
        <p class="project-summary"><?= e(truncate_text($project['summary'], 130)) ?></p>

        <?php if (!empty($project['tech_stack'])): 
            $tags = array_map('trim', explode(',', $project['tech_stack']));
        ?>
            <div class="project-tech-tags">
                <?php foreach (array_slice($tags, 0, 4) as $tag): ?>
                    <span class="tech-tag"><?= e($tag) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="project-footer">
            <a href="<?= $projectUrl ?>" class="area-link">
                Xem chi tiết dự án
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
            <?php if (!empty($project['github_url'])): ?>
                <a href="<?= e($project['github_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm" title="Xem mã nguồn">
                    GitHub
                </a>
            <?php endif; ?>
        </div>
    </div>
</article>
