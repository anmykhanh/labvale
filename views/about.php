<?php
/**
 * LabVale - Trang giới thiệu
 */
$pageTitle = 'Giới thiệu — LabVale R&D';
$pageDescription = 'Tìm hiểu về nguồn gốc, triết lý nghiên cứu, sứ mệnh và phương pháp tiếp cận kỹ thuật của dự án công nghệ LabVale.';
$currentNav = 'about';

require_once __DIR__ . '/layout/header.php';
$breadcrumbItems = [
    ['title' => 'Giới thiệu', 'url' => null]
];
require __DIR__ . '/layout/breadcrumb.php';
?>

<div class="container container-narrow" style="padding-bottom: 5rem;">
    <div class="detail-header">
        <span class="detail-kicker">VỀ CHÚNG TÔI</span>
        <h1 class="detail-title">Nghiên cứu độc lập với tinh thần thực nghiệm nghiêm túc</h1>
        <div class="detail-meta-bar">
            <span>Thành lập tại Việt Nam</span>
            <span class="article-dot"></span>
            <span>Khẩu hiệu: Research. Innovate. Create Value.</span>
        </div>
    </div>

    <div class="detail-hero-media">
        <img src="<?= asset('images/about-philosophy.jpg') ?>" alt="Không gian làm việc và đo kiểm của LabVale" class="detail-hero-img" width="1000" height="500">
        <div class="detail-hero-caption">
            Thực nghiệm phần cứng và đo đạc thông số kỹ thuật tại bàn làm việc
        </div>
    </div>

    <div class="prose">
        <h2>1. Ý nghĩa thương hiệu LabVale</h2>
        <p>
            Tên gọi <strong>LabVale</strong> được kết hợp từ hai khái niệm:
        </p>
        <ul>
            <li><strong>Lab (Laboratory):</strong> Đại diện cho không gian phòng thí nghiệm — nơi mọi ý tưởng đều phải được kiểm tra qua thực nghiệm, đo đạc bằng số liệu và đánh giá bằng phương pháp khoa học.</li>
            <li><strong>Vale (Thung lũng &amp; Value):</strong> Lấy cảm hứng từ hình ảnh thung lũng ươm mầm công nghệ, đồng thời đồng âm với từ <em>Value</em> (giá trị). LabVale mang hàm ý: <em>"Thung lũng nghiên cứu, nơi những ý tưởng được phát triển thành công nghệ và sản phẩm có giá trị thực tiễn."</em></li>
        </ul>

        <h2>2. Tinh thần và Giai đoạn phát triển</h2>
        <p>
            LabVale đang trong giai đoạn hình thành và nghiên cứu ban đầu. Chúng tôi tự nhận thức rõ vị trí của mình: một nhóm nghiên cứu độc lập nhỏ, đam mê công nghệ phần cứng, cơ điện tử và trí tuệ nhân tạo.
        </p>
        <p>
            Chúng tôi <strong>không</strong> tự nhận là tập đoàn lớn, không sở hữu nhà máy sản xuất hàng loạt hay đội ngũ hàng trăm kỹ sư viển vông. Mọi bước đi của LabVale đều gắn liền với những linh kiện cụ thể, từng đoạn mã nguồn được kiểm thử tỉ mỉ và những mẫu thử nghiệm (prototypes) thực sự chạy được trên bàn kỹ thuật.
        </p>

        <blockquote>
            &ldquo;Chúng tôi tin rằng giá trị của nghiên cứu không chỉ nằm ở những ý tưởng mới, mà còn ở khả năng biến chúng thành giải pháp hữu ích cho cuộc sống.&rdquo;
        </blockquote>

        <h2>3. Phương pháp tiếp cận khoa học</h2>
        <p>
            Mọi đề tài nghiên cứu tại LabVale đều tuân thủ chu trình lặp kỹ thuật khép kín:
        </p>
        <ol>
            <li><strong>Xác định vấn đề thực tế:</strong> Không làm công nghệ vì mục đích phô diễn, mà nhằm giải quyết một bài toán đo kiểm, tự động hóa hoặc giám sát cụ thể.</li>
            <li><strong>Mô phỏng và thiết kế sơ bộ:</strong> Tính toán lý thuyết, vẽ sơ đồ khối và lựa chọn linh kiện theo tiêu chí ổn định và độ bền.</li>
            <li><strong>Chế tạo mẫu thử nhanh (Rapid Prototyping):</strong> Gia công PCB, in 3D cơ khí, lắp ráp và thiết lập trạm đo kiểm an toàn.</li>
            <li><strong>Thu thập dữ liệu thực nghiệm:</strong> Sử dụng máy đo chuyên dụng (máy hiện sóng, cảm biến dòng/áp, load cell, máy phân tích logic) để ghi nhận dữ liệu khách quan.</li>
            <li><strong>Đóng góp tri thức mở:</strong> Chia sẻ nhật ký nghiên cứu, tài liệu kỹ thuật và mã nguồn lên cộng đồng để thúc đẩy học thuật trung thực.</li>
        </ol>

        <h2>4. Lĩnh vực hoạt động cốt lõi</h2>
        <p>
            Hiện nay, các hoạt động thử nghiệm của LabVale xoay quanh 6 trục công nghệ:
        </p>
        <ul>
            <li><strong>Robotics:</strong> Khung xe tự hành AMR, điều khiển vi sai, động học robot và lập bản đồ SLAM.</li>
            <li><strong>Drone &amp; UAV:</strong> Thiết bị bay 4 cánh quạt, khí động học cánh nâng, thuật toán lọc PID và truyền thông MAVLink.</li>
            <li><strong>Artificial Intelligence:</strong> Thị giác máy tính nhận diện vật thể nhẹ, tối ưu hóa mô hình AI tại biên (Edge AI).</li>
            <li><strong>Embedded Systems &amp; IoT:</strong> Mạch vi điều khiển STM32/ESP32, bus công nghiệp CAN, giao thức mạng cảm biến.</li>
            <li><strong>Software &amp; Web:</strong> Bảng điều khiển telemetry, công cụ kiểm thử tự động, hạ tầng web hiệu năng cao.</li>
            <li><strong>Research &amp; Development:</strong> Quy trình đo kiểm lực tĩnh động cơ, kiểm tra độ bền và phân tích rung động cơ khí.</li>
        </ul>

        <h2>5. Kết nối và Trao đổi học thuật</h2>
        <p>
            LabVale luôn hoan nghênh những phản hồi đóng góp chuyên môn, sự trao đổi kiến thức từ các kỹ sư, nhà nghiên cứu và sinh viên có chung niềm đam mê công nghệ thực nghiệm.
        </p>
        <p>
            Quý đồng nghiệp có thể gửi trao đổi kỹ thuật hoặc đề xuất phối hợp qua trang <a href="<?= url('/contact') ?>">Liên hệ hợp tác</a> hoặc theo dõi mã nguồn tại <a href="<?= e(get_setting('github_url', 'https://github.com/labvale-rd')) ?>" target="_blank" rel="noopener noreferrer">GitHub LabVale</a>.
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
