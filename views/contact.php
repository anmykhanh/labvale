<?php
/**
 * LabVale - Trang Liên hệ & Trao đổi hợp tác
 * @var string|null $successMsg
 * @var string|null $errorMsg
 */
$pageTitle = 'Liên hệ & Trao đổi hợp tác — LabVale R&D';
$pageDescription = 'Gửi thông điệp trao đổi học thuật, đóng góp kỹ thuật hoặc liên hệ hợp tác nghiên cứu cùng LabVale.';
$currentNav = 'contact';

require_once __DIR__ . '/layout/header.php';
$breadcrumbItems = [
    ['title' => 'Liên hệ', 'url' => null]
];
require __DIR__ . '/layout/breadcrumb.php';
?>

<div class="container" style="padding-bottom: 5rem;">
    <div class="section-header">
        <span class="section-kicker">KẾT NỐI KỸ THUẬT</span>
        <h1 class="section-title">Liên hệ &amp; Trao đổi học thuật</h1>
        <p class="section-desc">
            Chúng tôi luôn sẵn lòng lắng nghe ý kiến đóng góp, thảo luận chuyên môn hoặc kết nối với các bạn sinh viên, kỹ sư và nhà nghiên cứu có chung đam mê khoa học thực nghiệm.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 3.5rem; align-items: start;">
        <!-- Left: Contact Form -->
        <div style="background-color: var(--color-white); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 2rem;">
            <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--color-primary-navy); margin-bottom: 1.5rem;">
                Gửi thông điệp đến nhóm nghiên cứu
            </h2>

            <?php if (!empty($successMsg)): ?>
                <div class="alert alert-success">
                    <?= e($successMsg) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errorMsg)): ?>
                <div class="alert alert-error">
                    <?= e($errorMsg) ?>
                </div>
            <?php endif; ?>

            <form action="<?= url('/contact') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="name" class="form-label">Họ và tên *</label>
                    <input type="text" id="name" name="name" class="form-control" required placeholder="Nguyễn Văn A" value="<?= e($_POST['name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Địa chỉ Email *</label>
                    <input type="email" id="email" name="email" class="form-control" required placeholder="name@domain.com" value="<?= e($_POST['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="organization" class="form-label">Đơn vị / Trường học (tùy chọn)</label>
                    <input type="text" id="organization" name="organization" class="form-control" placeholder="Đại học Bách Khoa / Kỹ sư tự do..." value="<?= e($_POST['organization'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="subject" class="form-label">Tiêu đề trao đổi *</label>
                    <input type="text" id="subject" name="subject" class="form-control" required placeholder="Thảo luận về thuật toán điều khiển / Góp ý dự án..." value="<?= e($_POST['subject'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="message" class="form-label">Nội dung thông điệp *</label>
                    <textarea id="message" name="message" class="form-control" rows="5" required placeholder="Chi tiết nội dung bạn muốn trao đổi cùng LabVale..."><?= e($_POST['message'] ?? '') ?></textarea>
                    <div class="form-help">Chúng tôi tôn trọng quyền riêng tư và cam kết không chia sẻ địa chỉ email của bạn cho bên thứ ba.</div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Gửi thông điệp
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </form>
        </div>

        <!-- Right: Information & Collaboration Guidelines -->
        <div>
            <div class="spec-box">
                <div class="spec-title">Kênh trao đổi trực tiếp</div>
                <div class="spec-list">
                    <div class="spec-item">
                        <span class="spec-label">Hòm thư điện tử chính thức:</span>
                        <span class="spec-value">
                            <a href="mailto:<?= e(get_setting('contact_email', 'contact@labvale.org')) ?>">
                                <?= e(get_setting('contact_email', 'contact@labvale.org')) ?>
                            </a>
                        </span>
                    </div>

                    <div class="spec-item">
                        <span class="spec-label">Kho lưu trữ mã nguồn mở:</span>
                        <span class="spec-value">
                            <a href="<?= e(get_setting('github_url', 'https://github.com/labvale-rd')) ?>" target="_blank" rel="noopener noreferrer">
                                github.com/labvale-rd &rarr;
                            </a>
                        </span>
                    </div>

                    <div class="spec-item">
                        <span class="spec-label">Địa điểm hoạt động:</span>
                        <span class="spec-value">Việt Nam (Dự án R&amp;D độc lập)</span>
                    </div>
                </div>
            </div>

            <div style="background-color: var(--color-white); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 1.5rem;">
                <h3 style="font-size: 1.05rem; font-weight: 600; color: var(--color-primary-navy); margin-bottom: 0.75rem;">
                    Nguyên tắc trao đổi kỹ thuật
                </h3>
                <ul style="font-size: 0.9rem; color: #475569; padding-left: 1.25rem; line-height: 1.65; margin: 0;">
                    <li style="margin-bottom: 0.5rem;">LabVale trân trọng các phản biện học thuật có cơ sở toán học và thực nghiệm.</li>
                    <li style="margin-bottom: 0.5rem;">Nếu bạn có ý tưởng cải tiến sơ đồ mạch hoặc thuật toán của dự án, vui lòng đính kèm các tài liệu tham khảo nếu có.</li>
                    <li>Chúng tôi phản hồi toàn bộ các thư trao đổi chuyên môn trong vòng 24 - 48 giờ làm việc.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
