<?php
/**
 * LabVale - Chính sách quyền riêng tư
 */
$pageTitle = 'Chính sách quyền riêng tư — LabVale R&D';
$pageDescription = 'Cam kết bảo vệ dữ liệu, quyền riêng tư và minh bạch thông tin của dự án LabVale.';
$currentNav = 'privacy';

require_once __DIR__ . '/layout/header.php';
$breadcrumbItems = [
    ['title' => 'Chính sách quyền riêng tư', 'url' => null]
];
require __DIR__ . '/layout/breadcrumb.php';
?>

<div class="container container-narrow" style="padding-bottom: 5rem;">
    <div class="detail-header">
        <span class="detail-kicker">CHÍNH SÁCH</span>
        <h1 class="detail-title">Chính sách quyền riêng tư</h1>
        <div class="detail-meta-bar">
            <span>Hiệu lực từ: Tháng 11, 2026</span>
            <span class="article-dot"></span>
            <span>Phiên bản: 1.0</span>
        </div>
    </div>

    <div class="prose">
        <h2>1. Cam kết tổng thể</h2>
        <p>
            LabVale là dự án nghiên cứu và phát triển công nghệ độc lập. Chúng tôi tôn trọng quyền riêng tư của khách truy cập và cam kết bảo vệ dữ liệu cá nhân theo các chuẩn mực đạo đức khoa học và quy định pháp luật hiện hành.
        </p>

        <h2>2. Thu thập dữ liệu</h2>
        <p>
            Website LabVale được thiết kế tối giản, không sử dụng các dịch vụ theo dõi quảng cáo xâm nhập hoặc các đoạn mã giám sát người dùng của bên thứ ba.
        </p>
        <p>Chúng tôi chỉ ghi nhận các thông tin sau:</p>
        <ul>
            <li><strong>Thông tin liên hệ tự nguyện:</strong> Tên, email, đơn vị công tác và nội dung thông điệp khi bạn chủ động điền vào biểu mẫu liên hệ.</li>
            <li><strong>Nhật ký máy chủ kỹ thuật (Server Logs):</strong> Địa chỉ IP truy cập, loại trình duyệt, mã trạng thái HTTP phục vụ mục đích bảo mật hệ thống và ngăn chặn tấn công mạng.</li>
        </ul>

        <h2>3. Mục đích sử dụng dữ liệu</h2>
        <p>Dữ liệu được thu thập chỉ được dùng vào các mục đích nội bộ sau:</p>
        <ul>
            <li>Phản hồi các thư trao đổi học thuật, đóng góp kỹ thuật từ cộng đồng.</li>
            <li>Đảm bảo hệ thống website vận hành ổn định và an toàn.</li>
            <li>Tuyệt đối <strong>không bán, cho thuê hoặc chuyển nhượng</strong> địa chỉ email hay bất kỳ thông tin nào của bạn cho bất kỳ đơn vị quảng cáo hoặc bên thứ ba nào.</li>
        </ul>

        <h2>4. Quyền của người dùng</h2>
        <p>
            Bất kỳ lúc nào bạn cũng có quyền yêu cầu tra cứu, hiệu chỉnh hoặc xóa bỏ hoàn toàn dữ liệu liên hệ mà bạn đã gửi cho LabVale. Vui lòng gửi yêu cầu đến hòm thư <a href="mailto:<?= e(get_setting('contact_email', 'contact@labvale.org')) ?>"><?= e(get_setting('contact_email', 'contact@labvale.org')) ?></a>.
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
