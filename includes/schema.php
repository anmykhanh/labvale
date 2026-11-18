<?php
/**
 * LabVale - Database Schema Migration & Seeder
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

function init_database() {
    $db = Database::getConnection();

    // 1. Users table
    $db->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `email` VARCHAR(100) NOT NULL UNIQUE,
        `password_hash` VARCHAR(255) NOT NULL,
        `full_name` VARCHAR(100) NOT NULL,
        `role` VARCHAR(20) DEFAULT 'admin',
        `last_login_at` DATETIME NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // 2. Research Areas table
    $db->exec("CREATE TABLE IF NOT EXISTS `research_areas` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `slug` VARCHAR(100) NOT NULL UNIQUE,
        `name` VARCHAR(150) NOT NULL,
        `subtitle` VARCHAR(255) NOT NULL,
        `icon_svg` TEXT NOT NULL,
        `short_desc` TEXT NOT NULL,
        `full_content` LONGTEXT NOT NULL,
        `image` VARCHAR(255) NOT NULL,
        `sort_order` INT DEFAULT 0,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // 3. Projects table
    $db->exec("CREATE TABLE IF NOT EXISTS `projects` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `area_id` INT NULL,
        `title` VARCHAR(200) NOT NULL,
        `slug` VARCHAR(200) NOT NULL UNIQUE,
        `status` ENUM('Ý tưởng', 'Đang nghiên cứu', 'Đang phát triển', 'Hoàn thành') DEFAULT 'Đang nghiên cứu',
        `summary` TEXT NOT NULL,
        `content` LONGTEXT NOT NULL,
        `tech_stack` VARCHAR(255) NOT NULL,
        `image` VARCHAR(255) NOT NULL,
        `image_caption` VARCHAR(255) NULL,
        `github_url` VARCHAR(255) NULL,
        `demo_url` VARCHAR(255) NULL,
        `is_featured` TINYINT(1) DEFAULT 0,
        `sort_order` INT DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX (`area_id`),
        CONSTRAINT `fk_projects_area` FOREIGN KEY (`area_id`) REFERENCES `research_areas` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // 4. Articles table (Góc nghiên cứu)
    $db->exec("CREATE TABLE IF NOT EXISTS `articles` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `area_id` INT NULL,
        `title` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL UNIQUE,
        `summary` TEXT NOT NULL,
        `content` LONGTEXT NOT NULL,
        `image` VARCHAR(255) NOT NULL,
        `reading_time` VARCHAR(20) DEFAULT '6 phút',
        `author` VARCHAR(100) DEFAULT 'LabVale Research Team',
        `tags` VARCHAR(255) NOT NULL,
        `is_published` TINYINT(1) DEFAULT 1,
        `views` INT DEFAULT 0,
        `published_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX (`area_id`),
        CONSTRAINT `fk_articles_area` FOREIGN KEY (`area_id`) REFERENCES `research_areas` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // 5. News table
    $db->exec("CREATE TABLE IF NOT EXISTS `news` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL UNIQUE,
        `summary` TEXT NOT NULL,
        `content` LONGTEXT NOT NULL,
        `image` VARCHAR(255) NOT NULL,
        `is_published` TINYINT(1) DEFAULT 1,
        `published_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // 6. Contacts table
    $db->exec("CREATE TABLE IF NOT EXISTS `contacts` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `organization` VARCHAR(150) NULL,
        `subject` VARCHAR(200) NOT NULL,
        `message` TEXT NOT NULL,
        `is_read` TINYINT(1) DEFAULT 0,
        `ip_address` VARCHAR(45) NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // 7. Settings table
    $db->exec("CREATE TABLE IF NOT EXISTS `settings` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `key_name` VARCHAR(50) NOT NULL UNIQUE,
        `value` LONGTEXT NOT NULL,
        `label` VARCHAR(100) NOT NULL,
        `group_name` VARCHAR(50) DEFAULT 'general'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // 8. Media table
    $db->exec("CREATE TABLE IF NOT EXISTS `media` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `filename` VARCHAR(255) NOT NULL,
        `original_name` VARCHAR(255) NOT NULL,
        `file_path` VARCHAR(255) NOT NULL,
        `file_size` INT NOT NULL,
        `mime_type` VARCHAR(50) NOT NULL,
        `alt_text` VARCHAR(255) NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Check if initial seeding is needed
    $chkAdmin = $db->query("SELECT COUNT(*) FROM `users`")->fetchColumn();
    if ($chkAdmin == 0) {
        seed_database($db);
    }
}

function seed_database(PDO $db) {
    // 1. Seed Admin
    $adminPassword = password_hash('LabVale@2026', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO `users` (`username`, `email`, `password_hash`, `full_name`, `role`) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['admin', 'admin@labvale.org', $adminPassword, 'Quản Trị Viên LabVale', 'admin']);

    // 2. Seed Settings
    $settings = [
        ['site_name', 'LabVale', 'Tên thương hiệu', 'general'],
        ['site_slogan', 'Research. Innovate. Create Value.', 'Khẩu hiệu', 'general'],
        ['site_description', 'LabVale là dự án nghiên cứu và phát triển công nghệ độc lập tại Việt Nam, tập trung vào robotics, UAV/drone, trí tuệ nhân tạo, hệ thống nhúng và các giải pháp kỹ thuật mang giá trị thực tiễn.', 'Mô tả website', 'seo'],
        ['site_keywords', 'LabVale, nghiên cứu công nghệ, robotics, drone, UAV, trí tuệ nhân tạo, hệ thống nhúng, IoT, phát triển công nghệ', 'Từ khóa SEO', 'seo'],
        ['contact_email', 'contact@labvale.org', 'Email liên hệ', 'contact'],
        ['github_url', 'https://github.com/labvale-rd', 'GitHub Repository', 'social'],
        ['footer_note', 'LabVale là dự án nghiên cứu và phát triển công nghệ độc lập đang trong giai đoạn hình thành. Toàn bộ tài liệu, mô hình và thử nghiệm được xây dựng trên tinh thần trung thực khoa học và ứng dụng thực tiễn.', 'Ghi chú chân trang', 'general']
    ];
    $sStmt = $db->prepare("INSERT INTO `settings` (`key_name`, `value`, `label`, `group_name`) VALUES (?, ?, ?, ?)");
    foreach ($settings as $s) {
        $sStmt->execute($s);
    }

    // 3. Seed Research Areas
    $areas = [
        [
            'slug' => 'robotics',
            'name' => 'Robotics',
            'subtitle' => 'Robot và hệ thống tự hành',
            'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="16" x2="8.01" y2="16"/><line x1="16" y1="16" x2="16.01" y2="16"/><line x1="9" y1="2" x2="15" y2="2"/></svg>',
            'short_desc' => 'Nghiên cứu cấu trúc cơ điện tử, động học robot, hệ điều hành robot (ROS 2) và thuật toán định vị, lập bản đồ (SLAM) cho các phương tiện tự hành mặt đất.',
            'full_content' => "Lĩnh vực Robotics tại LabVale tập trung vào việc nghiên cứu và thử nghiệm các nền tảng robot tự hành cỡ nhỏ (Autonomous Mobile Robots - AMR). Chúng tôi chú trọng việc tích hợp phần cứng cơ khí chính xác, động cơ bước/BLDC, cảm biến khoảng cách LiDAR và kiến trúc phần mềm điều khiển thời gian thực.\n\n### Trọng tâm nghiên cứu:\n- **Động học và điều khiển chuyển động:** Tối ưu mô hình vi sai (differential drive) và đa hướng (mecanum/omni-directional).\n- **Định vị và lập bản đồ (SLAM):** Ứng dụng Cartographer và Nav2 trên nền tảng ROS 2 để lập bản đồ 2D/3D môi trường trong nhà.\n- **Kiến trúc nhúng điều khiển:** Tách biệt tầng điều khiển động cơ thời gian thực (microcontroller STM32/ESP32) và tầng tính toán cao cấp (Single Board Computer x86/ARM).\n- **An toàn và phản hồi môi trường:** Tích hợp cảm biến siêu âm, cản quang và thuật toán tránh chướng ngại vật linh hoạt.",
            'image' => 'area-robotics.jpg',
            'sort_order' => 1
        ],
        [
            'slug' => 'drone-uav',
            'name' => 'Drone & UAV',
            'subtitle' => 'Thiết bị bay không người lái',
            'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4"/><path d="M12 18v4"/><path d="M4.93 4.93l2.83 2.83"/><path d="M16.24 16.24l2.83 2.83"/><path d="M2 12h4"/><path d="M18 12h4"/><path d="M4.93 19.07l2.83-2.83"/><path d="M16.24 7.76l2.83-2.83"/><circle cx="12" cy="12" r="3"/></svg>',
            'short_desc' => 'Thiết kế, cân chỉnh khí động học và thử nghiệm thiết bị bay không người lái dạng quadcopter và cánh bằng, phục vụ mục đích giám sát và khảo sát kỹ thuật.',
            'full_content' => "Tại LabVale, nghiên cứu UAV hướng đến việc làm chủ các thành phần cốt lõi của thiết bị bay: từ lựa chọn cấu hình cánh quạt, tính toán tỷ số lực nâng trên trọng lượng (Thrust-to-Weight ratio), cấu hình mạch điều khiển bay (Flight Controller) đến kiểm tra độ ổn định khí động học trong điều kiện thực tế.\n\n### Hướng nghiên cứu chính:\n- **Cấu hình động lực học UAV:** Nghiên cứu đặc tính điện áp, dòng xả và nhiệt độ của cụm pin LiPo, mạch ESC và động cơ không chổi than (BLDC).\n- **Bộ điều khiển bay mã nguồn mở:** Khảo sát và tinh chỉnh thuật toán lọc nhiễu PID, Gyro/Accelerometer trên nền tảng ArduPilot và PX4.\n- **Giao thức truyền dữ liệu không dây:** Tối ưu hóa đường truyền telemetry thời gian thực qua giao thức MAVLink với tần số 915MHz/433MHz.\n- **Kiểm thử tĩnh và kiểm thử bay có dây:** Xây dựng quy trình thử nghiệm an toàn trong phòng thí nghiệm trước khi thử nghiệm ngoài hiện trường.",
            'image' => 'area-drone.jpg',
            'sort_order' => 2
        ],
        [
            'slug' => 'artificial-intelligence',
            'name' => 'Artificial Intelligence',
            'subtitle' => 'Trí tuệ nhân tạo và thị giác máy tính',
            'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 2A2.5 2.5 0 0 1 12 4.5v15a2.5 2.5 0 0 1-4.96.44 2.5 2.5 0 0 1-2.96-3.08 3 3 0 0 1-.34-5.58 2.5 2.5 0 0 1 1.32-4.24 2.5 2.5 0 0 1 4.44-2.04z"/><path d="M14.5 2A2.5 2.5 0 0 0 12 4.5v15a2.5 2.5 0 0 0 4.96.44 2.5 2.5 0 0 0 2.96-3.08 3 3 0 0 0 .34-5.58 2.5 2.5 0 0 0-1.32-4.24 2.5 2.5 0 0 0-4.44-2.04z"/></svg>',
            'short_desc' => 'Nghiên cứu mô hình thị giác máy tính (Computer Vision) nhẹ, học sâu (Deep Learning) và triển khai AI tại biên (Edge AI) trên các thiết bị phần cứng có giới hạn tài nguyên.',
            'full_content' => "Thay vì tập trung vào các mô hình khổng lồ đòi hỏi cụm máy chủ đắt đỏ, LabVale ưu tiên nghiên cứu các giải pháp AI thiết thực, có khả năng chạy độc lập trên các vi mạch máy tính nhúng (Edge Computing) với mức tiêu thụ điện năng thấp.\n\n### Các hướng tiếp cận:\n- **Thị giác máy tính ứng dụng (Computer Vision):** Nhận diện làn đường, phát hiện vật cản, đọc mã vạch và phân loại linh kiện kỹ thuật.\n- **Tối ưu hóa mô hình AI (Model Quantization & Pruning):** Chuyển đổi mô hình PyTorch sang ONNX, TensorRT và TFLite định dạng INT8/FP16 nhằm tăng tốc độ suy luận (inference speed).\n- **Xử lý tín hiệu thời gian thực:** Phân tích dữ liệu cảm biến rung chấn, âm thanh và nhiệt độ để cảnh báo bất thường trong thiết bị cơ khí.",
            'image' => 'area-ai.jpg',
            'sort_order' => 3
        ],
        [
            'slug' => 'embedded-iot',
            'name' => 'Embedded Systems & IoT',
            'subtitle' => 'Hệ thống nhúng và thiết bị thông minh',
            'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/></svg>',
            'short_desc' => 'Thiết kế phần cứng, lập trình vi điều khiển STM32/ESP32, bus công nghiệp CAN/Modbus và mạng cảm biến kết nối tầm xa tiêu thụ năng lượng thấp.',
            'full_content' => "Hệ thống nhúng là cầu nối vật lý giữa thuật toán và thế giới hiện thực. LabVale tập trung vào việc làm chủ thiết kế mạch in (PCB), tối ưu hoá mã nguồn C/C++ vi điều khiển, quản lý ngắt và truyền thông công nghiệp ổn định.\n\n### Hoạt động nghiên cứu:\n- **Thiết kế phần cứng mạch nguyên mẫu:** Vẽ schematic, layout PCB 2-4 lớp trên KiCAD, tuân thủ các quy tắc chống nhiễu EMC/EMI.\n- **Lập trình firmware Bare-metal & FreeRTOS:** Quản lý tác vụ đa nhiệm, đồng bộ hóa hàng đợi, giao tiếp ngoại vi SPI, I2C, UART, CAN Bus.\n- **Mạng kết nối IoT không dây:** Nghiên cứu giao thức LoRa, ESP-NOW, MQTT nhẹ nhàng, truyền dữ liệu cảm biến định kỳ với dòng điện chế độ ngủ chỉ vài microampe (µA).",
            'image' => 'area-embedded.jpg',
            'sort_order' => 4
        ],
        [
            'slug' => 'software-web',
            'name' => 'Software & Web',
            'subtitle' => 'Phần mềm, website và hạ tầng công nghệ',
            'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
            'short_desc' => 'Xây dựng giao diện điều khiển trung tâm (Dashboard), hệ thống ghi nhận dữ liệu đo kiểm (Telemetry Logger) và các công cụ phần mềm nội bộ chuẩn mực.',
            'full_content' => "Hạ tầng phần mềm của LabVale tuân thủ triết lý tinh gọn, hiệu quả và dễ bảo trì. Chúng tôi ưu tiên kiến trúc phi phụ thuộc, viết code chuẩn ngữ nghĩa, tối ưu tốc độ và đảm bảo khả năng tương thích cao trên nhiều môi trường triển khai.\n\n### Phạm vi phát triển:\n- **Giao diện giám sát thiết bị (Ground Control Interface):** Web socket, biểu đồ dữ liệu thời gian thực theo dõi thông số telemetry của robot và drone.\n- **Công cụ giả lập và kiểm thử tự động:** Viết script Python/Bash phục vụ kiểm tra tự động mã nguồn firmware và mô phỏng giao thức truyền thông.\n- **Hệ thống quản lý tri thức kỹ thuật:** Lưu trữ báo cáo thử nghiệm, tài liệu hướng dẫn và kho dữ liệu nghiên cứu nội bộ an toàn.",
            'image' => 'area-software.jpg',
            'sort_order' => 5
        ],
        [
            'slug' => 'research-development',
            'name' => 'Research & Development',
            'subtitle' => 'Nghiên cứu, thử nghiệm và phát triển công nghệ',
            'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2v7.31"/><path d="M14 9.3V2"/><path d="M8.5 2h7"/><path d="M14 9.3a6.5 6.5 0 1 1-4 0"/><path d="M5.52 16h12.96"/></svg>',
            'short_desc' => 'Thiết lập quy trình đo kiểm khoa học, đánh giá độ bền cơ điện tử, phân tích dữ liệu thực nghiệm và tinh chỉnh liên tục qua các chu kỳ chế tạo mẫu thử.',
            'full_content' => "Tại LabVale, R&D không chỉ là hoạt động kỹ thuật mà là một kỷ luật làm việc có phương pháp. Chúng tôi tôn trọng các số liệu đo lường thực tế, không đưa ra kết luận thiếu căn cứ và luôn kiểm tra chéo các giả thuyết thông qua các bài test thực nghiệm lặp lại.\n\n### Phương pháp tiếp cận R&D:\n- **Quy trình lặp mẫu thử (Rapid Prototyping):** Mô hình hóa ý tưởng -> Thiết kế sơ bộ -> In 3D / Gia công mạch mẫu -> Đo kiểm -> Đánh giá sai số -> Tinh chỉnh thiết kế.\n- **Đo kiểm thông số vật lý:** Đo dòng tiêu thụ, nhiệt độ hoạt động, độ rung động của motor, độ ổn định của nguồn cấp xung nhịp.\n- **Ghi chép và công khai kết quả:** Tất cả nhật ký nghiên cứu, bảng dữ liệu đo kiểm và các bài học từ thất bại đều được ghi chép trung thực phục vụ mục tiêu học thuật.",
            'image' => 'area-rnd.jpg',
            'sort_order' => 6
        ]
    ];

    $aStmt = $db->prepare("INSERT INTO `research_areas` (`slug`, `name`, `subtitle`, `icon_svg`, `short_desc`, `full_content`, `image`, `sort_order`, `is_active`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
    $areaIdMap = [];
    foreach ($areas as $a) {
        $aStmt->execute([$a['slug'], $a['name'], $a['subtitle'], $a['icon_svg'], $a['short_desc'], $a['full_content'], $a['image'], $a['sort_order']]);
        $areaIdMap[$a['slug']] = $db->lastInsertId();
    }

    // 4. Seed Realistic Projects
    $projects = [
        [
            'area_slug' => 'robotics',
            'title' => 'Nền tảng xe tự hành AMR-01 phục vụ lập bản đồ trong nhà',
            'slug' => 'amr-01-indoor-mapping-rover',
            'status' => 'Đang phát triển',
            'summary' => 'Thiết kế khung gầm vi sai 2 bánh chủ động tích hợp cảm biến LiDAR 360 độ và vi điều khiển STM32, kết nối máy tính nhúng chạy ROS 2 để lập bản đồ 2D Cartographer.',
            'content' => "### Tổng quan dự án\n\nDự án **AMR-01** là bước đi đầu tiên của LabVale trong việc xây dựng một nền tảng robot di động tự hành cỡ nhỏ phục vụ mục đích nghiên cứu và giáo dục kỹ thuật. Thay vì sử dụng các bộ kit thương mại đóng kín, chúng tôi tự thiết kế cơ khí, mạch điều khiển công suất động cơ và kiến trúc phần mềm từ đầu.\n\n### Cấu hình phần cứng thực tế\n- **Khung gầm:** Nhôm định hình 2020 kết hợp các chi tiết gia công CNC acrylic gia cường.\n- **Động cơ:** 2 động cơ DC Geared có gắn encoder quang học độ phân giải 330 xung/vòng.\n- **Mạch điều khiển tầng thấp:** Bo mạch tự thiết kế sử dụng vi điều khiển STM32F401, giao tiếp qua UART/CAN.\n- **Bộ tính toán tầng cao:** Máy tính nhúng SBC Raspberry Pi 4 (4GB RAM) chạy Ubuntu 22.04 LTS và ROS 2 Humble.\n- **Cảm biến định vị:** LiDAR 2D RPLIDAR A1M8 quét 360 độ với tần số quét 5.5Hz, bán kính đo 12m.\n- **Nguồn cấp:** Cụm pin Li-ion 3S2P 12.6V kèm mạch bảo vệ BMS và hạ áp DC-DC cách ly.\n\n### Trạng thái thử nghiệm hiện tại\n- Đã hoàn thành lắp ráp cơ khí và hiệu chuẩn sai số khoảng cách lăn bánh (wheel odometry calibration).\n- Đã thử nghiệm lập bản đồ phòng thí nghiệm với độ chính xác sai số vị trí ghi nhận dưới 3.5cm.\n- Đang tiếp tục nghiên cứu thuật toán tránh chướng ngại vật động với gói điều hướng Nav2.",
            'tech_stack' => 'ROS 2, STM32F4, C++, RPLIDAR, Python, FreeRTOS',
            'image' => 'project-rover.jpg',
            'image_caption' => 'Khung gầm thử nghiệm xe tự hành AMR-01 tại phòng thực nghiệm LabVale',
            'github_url' => 'https://github.com/labvale-rd/amr-01-core',
            'is_featured' => 1,
            'sort_order' => 1
        ],
        [
            'area_slug' => 'drone-uav',
            'title' => 'Khung kiểm nghiệm bay Quadcopter Q-250 cấu hình mã nguồn mở',
            'slug' => 'quadcopter-q250-testbed',
            'status' => 'Đang nghiên cứu',
            'summary' => 'Xây dựng mẫu quadcopter cỡ 250mm phục vụ kiểm thử động lực học, đo rung động IMU và thử nghiệm thuật toán điều khiển PID trên phần sụn ArduPilot.',
            'content' => "### Mục tiêu nghiên cứu\n\nDự án **Q-250 Testbed** hướng đến việc xây dựng một môi trường bay thử nghiệm an toàn, chi phí hợp lý để phân tích độ nhạy của các tham số điều khiển bay. Dự án giúp nhóm nghiên cứu LabVale nắm vững nguyên lý hoạt động của các bộ lọc số khử nhiễu từ động cơ đến cảm biến gia tốc kế/con quay hồi chuyển.\n\n### Thông số kỹ thuật thực nghiệm\n- **Khung:** Sợi carbon 3K đường chéo motor-to-motor 250mm.\n- **Động cơ:** Động cơ không chổi than 2205 - 2300KV.\n- **Bộ điều tốc (ESC):** 4-in-1 30A hỗ trợ giao thức DShot600.\n- **Bộ điều khiển bay:** STM32F405 Flight Controller tích hợp cảm biến IMU ICM42688P và phong vũ kế BMP280.\n- **Pin:** LiPo 4S 1500mAh 100C.\n\n### Tiến độ thực tế\n- Đã thực hiện kiểm tra động lực tĩnh trên bàn cân lực kéo.\n- Hoàn thành cấu hình bộ lọc notch filter động (Dynamic Notch Filter) theo dõi tần số quay của động cơ để triệt tiêu dao động cơ học tác động lên IMU.\n- Chuẩn bị bước thử nghiệm bay treo (hover flight) trong lồng lưới bảo hộ.",
            'tech_stack' => 'ArduPilot, BLDC Motor, DShot600, STM32, Telemetry',
            'image' => 'project-vtol.jpg',
            'image_caption' => 'Mẫu quadcopter Q-250 trên giá cân chỉnh phần cứng',
            'github_url' => 'https://github.com/labvale-rd/q250-flight-logs',
            'is_featured' => 1,
            'sort_order' => 2
        ],
        [
            'area_slug' => 'artificial-intelligence',
            'title' => 'Hệ thống thị giác máy tính Edge AI nhận diện vật thể thời gian thực',
            'slug' => 'edge-ai-object-detection-module',
            'status' => 'Đang nghiên cứu',
            'summary' => 'Nghiên cứu tối ưu hóa mô hình YOLO phiên bản Nano và triển khai trên máy tính nhúng với bộ tăng tốc NPU, đạt tốc độ suy luận trên 30 khung hình/giây.',
            'content' => "### Đặt vấn đề\n\nĐối với các thiết bị di động như robot hoặc drone, việc gửi luồng video lên máy chủ đám mây để xử lý hình ảnh tạo ra độ trễ lớn (network latency) và phụ thuộc hoàn toàn vào kết nối mạng. Dự án này tập trung vào việc đưa toàn bộ khả năng nhận diện hình ảnh xuống xử lý trực tiếp tại thiết bị (Edge AI).\n\n### Phương pháp thực hiện\n- **Mô hình gốc:** YOLOv8 Nano được huấn luyện trên tập dữ liệu vật cản và ký hiệu chỉ dẫn trong nhà xưởng.\n- **Tối ưu hóa:** Sử dụng kỹ thuật Post-Training Quantization (PTQ) để chuyển đổi trọng số mô hình từ FP32 xuống INT8.\n- **Bộ tăng tốc phần cứng:** Chạy thực nghiệm trên chip nhúng tích hợp NPU 1 TOPS và card Raspberry Pi 5.\n- **Giao tiếp đầu ra:** Tín hiệu nhận diện (tọa độ bounding box, nhãn, độ tin cậy) được nén và truyền qua giao tiếp UART/CAN với tần số 30Hz sang vi điều khiển điều khiển chuyển động.",
            'tech_stack' => 'PyTorch, ONNX, TensorRT, OpenCV, Edge TPU, C++',
            'image' => 'project-edge-ai.jpg',
            'image_caption' => 'Kết quả nhận diện vật thể thời gian thực trên bo mạch nhúng',
            'github_url' => 'https://github.com/labvale-rd/edge-vision-opt',
            'is_featured' => 1,
            'sort_order' => 3
        ],
        [
            'area_slug' => 'embedded-iot',
            'title' => 'Bàn đo lực kéo và hiệu suất động cơ không chổi than (ESC & BLDC Thrust Stand)',
            'slug' => 'bldc-thrust-stand-logger',
            'status' => 'Hoàn thành',
            'summary' => 'Thiết bị đo kiểm lực kéo, dòng tiêu thụ, điện áp và tốc độ vòng quay (RPM) của động cơ BLDC phục vụ tính toán hiệu suất khí động học cho UAV.',
            'content' => "### Mục đích chế tạo\n\nĐể lựa chọn chính xác cặp động cơ - cánh quạt tối ưu cho drone, kỹ sư không thể chỉ dựa vào bảng thông số quảng cáo của nhà sản xuất. LabVale đã thiết kế và chế tạo bàn đo tĩnh BLDC Thrust Stand nhằm thu thập dữ liệu khách quan tại phòng thí nghiệm.\n\n### Cấu trúc thiết bị\n- **Cảm biến lực:** Load cell thanh đòn 5kg kết hợp mạch khuếch đại 24-bit HX711, được hiệu chuẩn bằng quả cân chuẩn.\n- **Đo dòng và áp:** Cảm biến hiệu ứng Hall INA226 giao tiếp I2C với độ chính xác cao.\n- **Đo RPM:** Cảm biến quang hồng ngoại đếm số vòng quay của cánh quạt.\n- **Điều khiển tín hiệu PWM/DShot:** Vi điều khiển ESP32 quét tín hiệu ga từ 10% đến 100% theo các bước định sẵn, tự động ghi nhận dữ liệu vào thẻ nhớ micro-SD và truyền qua USB Serial sang máy tính.\n\n### Kết quả đạt được\n- Thiết bị đã hoạt động ổn định và được sử dụng để lập biểu đồ hiệu suất (g/W) cho hơn 6 loại cánh quạt kích thước từ 5 inch đến 9 inch.",
            'tech_stack' => 'ESP32, HX711, INA226, C++, Python GUI Data Plotter',
            'image' => 'project-esc.jpg',
            'image_caption' => 'Bàn đo lực kéo BLDC thực tế tại bàn kỹ thuật LabVale',
            'github_url' => 'https://github.com/labvale-rd/thrust-stand-esp32',
            'is_featured' => 0,
            'sort_order' => 4
        ]
    ];

    $pStmt = $db->prepare("INSERT INTO `projects` (`area_id`, `title`, `slug`, `status`, `summary`, `content`, `tech_stack`, `image`, `image_caption`, `github_url`, `is_featured`, `sort_order`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($projects as $p) {
        $pStmt->execute([
            $areaIdMap[$p['area_slug']] ?? null,
            $p['title'],
            $p['slug'],
            $p['status'],
            $p['summary'],
            $p['content'],
            $p['tech_stack'],
            $p['image'],
            $p['image_caption'],
            $p['github_url'],
            $p['is_featured'],
            $p['sort_order']
        ]);
    }

    // 5. Seed Technical Articles (Góc nghiên cứu)
    $articles = [
        [
            'area_slug' => 'drone-uav',
            'title' => 'Nguyên lý hoạt động của drone: Từ khí động học cánh quạt đến vòng lặp điều khiển PID',
            'slug' => 'nguyen-ly-hoat-dong-cua-drone',
            'summary' => 'Phân tích chi tiết nguyên lý nâng khí động học của quadcopter, sự cân bằng mô-men xoắn phản lực và cơ chế hoạt động của thuật toán PID trong kiểm soát góc nghiêng.',
            'content' => "Thiết bị bay không người lái dạng 4 cánh quạt (quadcopter) là một trong những hệ thống cơ điện tử điển hình kết hợp giữa vật lý khí động học và lý thuyết điều khiển tự động. Bài viết này trình bày chi tiết cách một drone duy trì trạng thái cân bằng và thực hiện các chuyển động không gian.\n\n### 1. Cơ chế tạo lực nâng và triệt tiêu mô-men xoắn\n\nKhác với trực thăng truyền thống cần cánh quạt đuôi để chống lại hiện tượng thân trực thăng xoay tròn theo cánh chính, quadcopter sử dụng 4 cánh quạt được bố trí đối xứng:\n- **Hai động cơ quay cùng chiều kim đồng hồ (CW).**\n- **Hai động cơ quay ngược chiều kim đồng hồ (CCW).**\n\nMỗi cánh quạt khi quay với tốc độ cao sẽ tạo ra hai lực vật lý:\n1. **Lực nâng hướng lên (Thrust):** Tỷ lệ thuận với bình phương tốc độ quay của động cơ (\$T \\propto \\omega^2\$). Tổng lực nâng của 4 động cơ phải lớn hơn trọng lượng toàn bộ drone (\$T_{total} > m \\cdot g\$) để thiết bị cất cánh.\n2. **Mô-men xoắn phản lực (Reaction Torque):** Theo định luật 3 Newton, không khí tác dụng một phản lực ngược hướng quay của cánh quạt lên thân drone. Khi 2 động cơ CW và 2 động cơ CCW quay với tốc độ bằng nhau, tổng mô-men xoắn bằng 0, giúp thân drone giữ nguyên hướng mũi mà không bị xoay tròn.\n\n### 2. Các trục chuyển động cơ bản\n\nDrone di chuyển trong không gian 3 chiều thông qua 4 lệnh điều khiển chính:\n- **Throttle (Độ cao):** Tăng hoặc giảm đồng thời tốc độ cả 4 động cơ để nâng hoặc hạ độ cao.\n- **Pitch (Gật đầu - Tiến/Lùi):** Tăng tốc 2 động cơ phía sau và giảm tốc 2 động cơ phía trước để drone nghiêng về phía trước, biến một phần lực nâng thành lực đẩy tiến.\n- **Roll (Nghiêng trái/phải):** Tăng tốc 2 động cơ bên trái và giảm tốc 2 động cơ bên phải để drone nghiêng và di chuyển sang phải.\n- **Yaw (Xoay hướng):** Tăng tốc 2 động cơ quay cùng chiều (ví dụ CW) và giảm tốc 2 động cơ ngược chiều (CCW). Sự chênh lệch mô-men phản lực làm thân drone tự xoay quanh trục thẳng đứng.\n\n### 3. Vòng lặp điều khiển phản hồi PID\n\nDo không khí luôn có gió xoáy và sai lệch cơ khí giữa các động cơ, quadcopter vốn là một hệ thống không ổn định tự nhiên (naturally unstable). Để drone giữ được thăng bằng, bộ điều khiển bay (Flight Controller) phải thực hiện vòng lặp điều khiển phản hồi với chu kỳ cực ngắn (thường từ 250Hz đến 4kHz).\n\n```text\n[Cảm biến IMU: Con quay hồi chuyển & Gia tốc kế]\n             │\n             ▼\n[Tính toán sai số giữa góc đặt và góc thực tế (Error = Setpoint - Actual)]\n             │\n             ▼\n[Bộ lọc PID: Khâu tỷ lệ P + Khâu tích phân I + Khâu vi phân D]\n             │\n             ▼\n[Bộ trộn tín hiệu (Mixer) -> Điều khiển xung DShot đến từng ESC / Động cơ]\n```\n\n- **Khâu P (Proportional):** Phản ứng tức thì với sai số hiện tại. P càng lớn, phản hồi cân bằng càng nhanh nhưng nếu quá cao sẽ gây rung lắc dữ dội.\n- **Khâu I (Integral):** Tích lũy sai số theo thời gian để loại trừ sai số xác lập, giúp drone không bị trôi góc khi có lực cản kéo dài.\n- **Khâu D (Derivative):** Dự đoán xu hướng thay đổi của sai số dựa trên tốc độ biến thiên, đóng vai trò như một bộ giảm chấn (damper) chống lại hiện tượng vọt lố (overshoot).\n\nViệc hiểu sâu các nguyên lý này là tiền đề bắt buộc trước khi bước vào chế tạo và kiểm thử bất kỳ mẫu UAV nào tại LabVale.",
            'image' => 'article-drone-principles.jpg',
            'reading_time' => '7 phút',
            'author' => 'Nhóm Nghiên Cứu LabVale',
            'tags' => 'UAV, Drone, Khí động học, PID Controller, ArduPilot'
        ],
        [
            'area_slug' => 'robotics',
            'title' => 'Cấu trúc một robot tự hành: Từ cảm biến phần cứng đến kiến trúc phần mềm',
            'slug' => 'cau-truc-mot-robot-tu-hanh',
            'summary' => 'Tổng quan kiến trúc phân tầng của một robot tự hành trong nhà (AMR): tầng cơ khí, tầng nhúng điều khiển, tầng nhận thức môi trường và tầng ra quyết định.',
            'content' => "Xây dựng một robot tự hành không chỉ đơn thuần là gắn bánh xe vào một bảng mạch. Đó là sự phối hợp chặt chẽ giữa 4 tầng kiến trúc công nghệ:\n\n### 1. Tầng cơ khí và cơ điện tử (Mechanical & Actuation Layer)\n\nTầng này bao gồm khung sườn, hệ thống treo, bánh xe vi sai và cụm động cơ. Điểm cốt lõi là việc lựa chọn tỷ số truyền hộp số sao cho vừa đảm bảo lực kéo (Torque) đủ lớn để chở tải, vừa duy trì tốc độ tuyến tính mong muốn. Cảm biến góc quay (Quadrature Encoder) được gắn trực tiếp trên trục động cơ để đo đếm chính xác từng bước quay.\n\n### 2. Tầng nhúng điều khiển thời gian thực (Low-level Embedded Controller)\n\nTầng này thường sử dụng vi điều khiển (MCU) họ ARM Cortex-M4 (như STM32F4). Trách nhiệm chính:\n- Nhận phản hồi xung encoder qua phần cứng bộ đếm (Hardware Timer Encoder Mode).\n- Chạy thuật toán PID vòng kín điều khiển vận tốc cho từng bánh xe ở tần số 100Hz.\n- Tính toán sai số chuyển động bánh xe (Wheel Odometry).\n- Đảm bảo cơ chế ngắt an toàn khẩn cấp (Hardware Emergency Stop) khi mất tín hiệu kết nối.\n\n### 3. Tầng nhận thức môi trường (Perception Layer)\n\nĐược đảm nhiệm bởi Single Board Computer (như Raspberry Pi hoặc Jetson). Dữ liệu từ cảm biến LiDAR 2D và Camera đo chiều sâu được thu thập để xây dựng bản đồ không gian và xác định chướng ngại vật.\n\n### 4. Tầng định vị và lập kế hoạch đường đi (Localization & Planning Layer)\n\nỨng dụng các thuật toán chuyên sâu:\n- **AMCL (Adaptive Monte Carlo Localization):** Dùng bộ lọc hạt để xác định vị trí robot trong bản đồ đã có.\n- **Global Planner (A* / Dijkstra):** Tìm đường đi ngắn nhất từ vị trí hiện tại đến đích.\n- **Local Planner (TEB / DWA):** Tính toán vận tốc tức thời để tránh các vật cản bất ngờ phát sinh trên đường đi mà không bị va chạm.",
            'image' => 'article-autonomous-robot.jpg',
            'reading_time' => '8 phút',
            'author' => 'Nhóm Nghiên Cứu LabVale',
            'tags' => 'Robotics, AMR, ROS 2, SLAM, Hardware Architecture'
        ],
        [
            'area_slug' => 'artificial-intelligence',
            'title' => 'Computer Vision là gì? Ứng dụng thị giác máy tính trong thực tế kỹ thuật',
            'slug' => 'computer-vision-la-gi',
            'summary' => 'Giải thích bản chất của thị giác máy tính, từ ma trận điểm ảnh số đến mạng nơ-ron tích chập (CNN) và các bài toán phân loại, phát hiện vật thể trong công nghiệp.',
            'content' => "Thị giác máy tính (Computer Vision) là lĩnh vực trí tuệ nhân tạo nghiên cứu cách thức giúp máy móc hiểu và trích xuất thông tin có ý nghĩa từ hình ảnh hoặc video kỹ thuật số.\n\n### 1. Máy tính nhìn hình ảnh như thế nào?\n\nĐối với con người, một bức ảnh chụp là phong cảnh hoặc đồ vật. Nhưng đối với máy tính, một bức ảnh màu chỉ đơn giản là một ma trận số 3 chiều (Tensor) có kích thước `H x W x C`:\n- `H`: Chiều cao (số điểm ảnh theo chiều dọc).\n- `W`: Chiều rộng (số điểm ảnh theo chiều ngang).\n- `C`: Số kênh màu (thường là 3 kênh: Đỏ - Red, Xanh lá - Green, Xanh dương - Blue), mỗi điểm ảnh mang giá trị số từ 0 đến 255.\n\n### 2. Các tác vụ chính trong Thị giác máy tính\n\n- **Phân loại ảnh (Image Classification):** Trả lời câu hỏi *Trong ảnh có vật gì?* (Ví dụ: Đây là linh kiện đạt chuẩn hay linh kiện lỗi).\n- **Phát hiện vật thể (Object Detection):** Trả lời câu hỏi *Vật thể nằm ở đâu và là vật gì?* Bằng cách xác định khung bao tọa độ (Bounding Box) xung quanh đối tượng.\n- **Phân đoạn ngữ nghĩa (Semantic Segmentation):** Gán nhãn cho từng pixel trong bức ảnh (phục vụ xe tự hành phân biệt mặt đường, vỉa hè và chướng ngại vật).\n\n### 3. Thách thức khi triển khai trên thiết bị nhúng (Edge Computing)\n\nTrong các ứng dụng thực tế tại LabVale, mô hình thị giác phải chạy trực tiếp trên thiết bị mà không có kết nối GPU mạnh mẽ. Do đó, việc ứng dụng các kỹ thuật như Lượng tử hóa mô hình (Quantization INT8), Cắt tỉa trọng số (Pruning) và sử dụng các kiến trúc mạng gọn nhẹ (như MobileNet, YOLO-Nano) là chìa khóa để đạt được tốc độ xử lý thời gian thực với lượng tiêu hao điện năng tối thiểu.",
            'image' => 'article-computer-vision.jpg',
            'reading_time' => '6 phút',
            'author' => 'Nhóm Nghiên Cứu LabVale',
            'tags' => 'Computer Vision, Deep Learning, Edge AI, OpenCV'
        ],
        [
            'area_slug' => 'embedded-iot',
            'title' => 'Các bước xây dựng một hệ thống nhúng: Từ sơ đồ nguyên lý đến phần sụn thực tế',
            'slug' => 'cac-buoc-xay-dung-he-thong-nhung',
            'summary' => 'Quy trình 5 bước kỹ thuật bài bản để phát triển một bo mạch nhúng vi điều khiển: xác định yêu cầu, thiết kế schematic, layout PCB, hàn thử nghiệm và viết firmware.',
            'content' => "Xây dựng một hệ thống nhúng ổn định đòi hỏi sự phối hợp nghiêm ngặt giữa kiến thức điện tử phần cứng và kỹ năng lập trình phần sụn (firmware). Tại LabVale, chúng tôi tuân thủ quy trình 5 bước kỹ thuật rõ ràng:\n\n### Bước 1: Xác định yêu cầu và chọn linh kiện cốt lõi\n- Liệt kê toàn bộ giao tiếp ngoại vi cần thiết (số chân PWM, kênh ADC, UART, SPI, I2C).\n- Tính toán điện áp và công suất tiêu thụ tối đa để chọn bộ hạ áp nguồn (LDO hoặc Buck Converter) phù hợp.\n- Chọn dòng vi điều khiển có xung nhịp và dung lượng Flash/RAM đáp ứng yêu cầu (ví dụ STM32F4 cho xử lý thuật toán nhanh hoặc ESP32 khi cần kết nối Wi-Fi/Bluetooth).\n\n### Bước 2: Thiết kế sơ đồ nguyên lý (Schematic Design)\n- Phân chia mạch thành các khối chức năng rõ ràng: Khối nguồn cấp, Khối vi điều khiển, Khối nạp chương trình (SWD), Khối giao tiếp và Khối bảo vệ (diode chống ngược cực, cầu chì tự phục hồi PTC).\n- Chú ý bố trí tụ lọc nguồn gốm 100nF đặt sát từng chân cấp nguồn \$V_{DD}\$ của chip để triệt tiêu xung nhiễu cao tần.\n\n### Bước 3: Thiết kế mạch in (PCB Layout)\n- Bố trí linh kiện theo dòng tín hiệu từ nguồn vào đến mạch xử lý.\n- Đảm bảo mặt phẳng nối đất (GND Plane) liên tục, tránh hiện tượng vòng lặp nối đất (Ground Loop).\n- Đường mạch công suất mang dòng lớn phải tính toán bề rộng vết đồng (Trace Width) phù hợp để tránh quá nhiệt.\n\n### Bước 4: Chế tạo mẫu thử và đo kiểm phần cứng (Hardware Bring-up)\n- Trước khi hàn chip chính, cấp nguồn kiểm tra điện áp tại các điểm đo (Test Points) đảm bảo đúng 3.3V / 5.0V.\n- Sử dụng máy hiện sóng (Oscilloscope) kiểm tra xung thạch anh dao động và độ gợn sóng (Ripple) của nguồn.\n\n### Bước 5: Phát triển phần sụn (Firmware Development)\n- Cấu hình cây xung nhịp (Clock Tree).\n- Viết driver tầng thấp (HAL/LL) cho các ngoại vi.\n- Triển khai hệ điều hành thời gian thực (FreeRTOS) để quản lý các tác vụ độc lập, giúp hệ thống không bao giờ bị nghẽn trong các vòng lặp chờ vô tận.",
            'image' => 'article-embedded-steps.jpg',
            'reading_time' => '7 phút',
            'author' => 'Nhóm Nghiên Cứu LabVale',
            'tags' => 'Embedded, PCB, STM32, Firmware, KiCAD'
        ],
        [
            'area_slug' => 'robotics',
            'title' => 'Những công nghệ nền tảng trong Robotics: Cơ học, Điện tử và Thuật toán điều khiển',
            'slug' => 'nhung-cong-nghe-nen-tang-trong-robotics',
            'summary' => 'Tìm hiểu các trụ cột cốt lõi tạo nên ngành kỹ thuật robot hiện đại: động học thuận/nghịch, mô hình hóa động lực học, cảm biến đo đạc và kiến trúc phần mềm phân tán.',
            'content' => "Robotics là điểm giao thoa giữa cơ học, điện tử học, khoa học máy tính và toán học ứng dụng. Để một cỗ máy cơ học có thể hoạt động chính xác trong môi trường thực, kỹ sư cần nắm vững các công nghệ nền tảng sau:\n\n### 1. Động học robot (Kinematics)\n- **Động học thuận (Forward Kinematics):** Tính toán vị trí và hướng của điểm cuối thao tác (hoặc thân robot) khi biết các góc quay của từng khớp hoặc số vòng quay của bánh xe.\n- **Động học nghịch (Inverse Kinematics):** Bài toán ngược lại: khi muốn robot đi đến một tọa độ xác định trong không gian, các động cơ phải quay với góc bao nhiêu và tốc độ như thế nào.\n\n### 2. Cảm biến và tích hợp dữ liệu (Sensor Fusion)\nKhông một cảm biến đơn lẻ nào là hoàn hảo: cảm biến gia tốc bị nhiễu do rung động cơ học, còn con quay hồi chuyển lại bị trôi dạt (drift) theo thời gian. Kỹ thuật ghép nối cảm biến (như bộ lọc bù Complementary Filter hay bộ lọc Kalman Mở rộng - EKF) kết hợp ưu điểm của từng cảm biến để ước lượng góc nghiêng và vị trí với độ tin cậy cao nhất.\n\n### 3. Hệ điều hành Robot (ROS - Robot Operating System)\nROS không phải là một hệ điều hành theo nghĩa đen như Linux, mà là một khung phần mềm trung gian (Middleware) cung cấp cơ chế giao tiếp Publish/Subscribe giữa các tiến trình độc lập (Nodes). Nhờ ROS, các mô-đun phần mềm (đọc LiDAR, tính odometry, điều hướng đường đi) có thể được phát triển và kiểm thử độc lập mà không làm ảnh hưởng đến cấu trúc tổng thể.",
            'image' => 'article-robotics-foundations.jpg',
            'reading_time' => '6 phút',
            'author' => 'Nhóm Nghiên Cứu LabVale',
            'tags' => 'Robotics, Kinematics, Sensor Fusion, ROS, Kalman Filter'
        ]
    ];

    $artStmt = $db->prepare("INSERT INTO `articles` (`area_id`, `title`, `slug`, `summary`, `content`, `image`, `reading_time`, `author`, `tags`, `is_published`, `views`, `published_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0, NOW())");
    foreach ($articles as $art) {
        $artStmt->execute([
            $areaIdMap[$art['area_slug']] ?? null,
            $art['title'],
            $art['slug'],
            $art['summary'],
            $art['content'],
            $art['image'],
            $art['reading_time'],
            $art['author'],
            $art['tags']
        ]);
    }

    // 6. Seed Lab News & Updates
    $newsList = [
        [
            'title' => 'Khởi động dự án nghiên cứu thực nghiệm công nghệ độc lập LabVale',
            'slug' => 'khoi-dong-du-an-nghien-cuu-thuc-nghiem-labvale',
            'summary' => 'LabVale chính thức thiết lập không gian nghiên cứu thử nghiệm ban đầu tại Việt Nam, tập trung vào 6 lĩnh vực công nghệ nền tảng với tinh thần trung thực khoa học.',
            'content' => "LabVale được hình thành từ mong muốn xây dựng một không gian nghiên cứu công nghệ độc lập, nơi các ý tưởng kỹ thuật được hiện thực hóa qua các mẫu thử nghiệm có thể đo đạc và kiểm chứng được.\n\nTrong giai đoạn đầu, LabVale tập trung vào việc hoàn thiện trang thiết bị đo kiểm cơ bản (máy hiện sóng, bộ nguồn điều chỉnh, trạm đo lực tĩnh) và thiết lập các chuẩn mực làm việc kỹ thuật nghiêm túc. Chúng tôi tin rằng giá trị thực sự đến từ sự kiên trì trong từng thực nghiệm nhỏ chứ không phải từ những tuyên bố hình thức.",
            'image' => 'hero-lab.jpg'
        ],
        [
            'title' => 'Thử nghiệm thành công cảm biến LiDAR 360 độ trên nền tảng xe tự hành AMR-01',
            'slug' => 'thu-nghiem-thanh-cong-lidar-tren-xe-tu-hanh-amr-01',
            'summary' => 'Ghi nhận dữ liệu quét đám mây điểm 2D đầu tiên từ cảm biến LiDAR kết hợp với thông số odometry bánh xe, hoàn thành bước đo đạc lập bản đồ sơ bộ phòng nghiên cứu.',
            'content' => "Trong tuần làm việc vừa qua, nhóm nghiên cứu LabVale đã hoàn tất bài thử nghiệm tích hợp đầu tiên giữa cảm biến quét laser 2D RPLIDAR và bo mạch điều khiển động cơ vi sai.\n\nDữ liệu đám mây điểm trả về ở tần số quét 5.5Hz phản ánh chính xác vị trí các bức tường và góc khuất trong phòng thử nghiệm. Sai số vị trí sau một chu trình di chuyển khép kín 10 mét đo được dưới 3.5cm, đạt yêu cầu đề ra cho giai đoạn thử nghiệm phần cứng ban đầu.",
            'image' => 'project-rover.jpg'
        ],
        [
            'title' => 'Hoàn thiện bàn kiểm tra hiệu suất động cơ không chổi than BLDC Thrust Stand',
            'slug' => 'hoan-thien-ban-kiem-tra-hieu-suat-dong-co-bldc',
            'summary' => 'Thiết bị đo lực tĩnh tự chế tạo tại phòng lab đã hoàn tất quy trình chuẩn hóa sai số với quả cân chuẩn và bắt đầu đo kiểm bộ động cơ dùng cho UAV.',
            'content' => "Thiết bị đo lực kéo BLDC Thrust Stand do LabVale tự thiết kế đã hoàn thành việc lập trình thuật toán bù phi tuyến tính cho cảm biến load cell và cảm biến dòng INA226.\n\nNhờ thiết bị này, mọi thay đổi về cấu hình cánh quạt và góc bước xoắn của các mẫu thử nghiệm UAV trong tương lai sẽ có số liệu đo đạc thực tế để đối chiếu, giúp tối ưu hóa hiệu suất bay và thời lượng pin một cách khoa học.",
            'image' => 'project-esc.jpg'
        ]
    ];

    $nStmt = $db->prepare("INSERT INTO `news` (`title`, `slug`, `summary`, `content`, `image`, `is_published`, `published_at`) VALUES (?, ?, ?, ?, ?, 1, NOW())");
    foreach ($newsList as $n) {
        $nStmt->execute([$n['title'], $n['slug'], $n['summary'], $n['content'], $n['image']]);
    }
}
