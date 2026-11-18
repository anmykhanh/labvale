<?php
/**
 * LabVale - Front Controller & Master Router
 * Independent Technology R&D Project
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/schema.php';

// Ensure database and schema are initialized
init_database();

$db = Database::getConnection();

// Parse requested URI relative to BASE_PATH
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'] ?? '/';

// Strip base path if running in a subdirectory (e.g. /lotusvip/labvale or /labvale)
if (BASE_PATH !== '' && str_starts_with($path, BASE_PATH)) {
    $path = substr($path, strlen(BASE_PATH));
}
$path = '/' . trim($path, '/');

// Handle XML Sitemap
if ($path === '/sitemap.xml') {
    require __DIR__ . '/sitemap.php';
    exit;
}

// Handle Robots.txt
if ($path === '/robots.txt') {
    header('Content-Type: text/plain; charset=utf-8');
    readfile(__DIR__ . '/robots.txt');
    exit;
}

// ==============================================================================
// ADMIN ROUTES
// ==============================================================================
if ($path === '/admin' || str_starts_with($path, '/admin/')) {
    $adminSub = trim(substr($path, strlen('/admin')), '/');

    switch ($adminSub) {
        case '':
            require __DIR__ . '/admin/dashboard.php';
            break;
        case 'login':
            require __DIR__ . '/admin/login.php';
            break;
        case 'logout':
            require __DIR__ . '/admin/logout.php';
            break;
        case 'projects':
            require __DIR__ . '/admin/projects.php';
            break;
        case 'research-areas':
            require __DIR__ . '/admin/research-areas.php';
            break;
        case 'articles':
            require __DIR__ . '/admin/articles.php';
            break;
        case 'news':
            require __DIR__ . '/admin/news.php';
            break;
        case 'contacts':
            require __DIR__ . '/admin/contacts.php';
            break;
        case 'media':
            require __DIR__ . '/admin/media.php';
            break;
        case 'settings':
            require __DIR__ . '/admin/settings.php';
            break;
        default:
            require __DIR__ . '/views/404.php';
            break;
    }
    exit;
}

// ==============================================================================
// PUBLIC ROUTES
// ==============================================================================

// 1. Trang chủ — /
if ($path === '/') {
    // 6 Research Areas
    $researchAreas = $db->query("SELECT * FROM `research_areas` WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

    // Featured Projects
    $featuredProjects = $db->query("
        SELECT p.*, a.name AS area_name, a.slug AS area_slug 
        FROM `projects` p 
        LEFT JOIN `research_areas` a ON p.area_id = a.id 
        WHERE p.is_featured = 1 
        ORDER BY p.sort_order ASC, p.id DESC 
        LIMIT 4
    ")->fetchAll();

    // If no featured projects, fetch latest
    if (empty($featuredProjects)) {
        $featuredProjects = $db->query("
            SELECT p.*, a.name AS area_name, a.slug AS area_slug 
            FROM `projects` p 
            LEFT JOIN `research_areas` a ON p.area_id = a.id 
            ORDER BY p.id DESC 
            LIMIT 4
        ")->fetchAll();
    }

    // Recent Technical Articles
    $recentArticles = $db->query("
        SELECT art.*, a.name AS area_name, a.slug AS area_slug 
        FROM `articles` art 
        LEFT JOIN `research_areas` a ON art.area_id = a.id 
        WHERE art.is_published = 1 
        ORDER BY art.id DESC 
        LIMIT 3
    ")->fetchAll();

    require __DIR__ . '/views/home.php';
    exit;
}

// 2. Giới thiệu — /about
if ($path === '/about') {
    require __DIR__ . '/views/about.php';
    exit;
}

// 3. Lĩnh vực nghiên cứu — /research-areas
if ($path === '/research-areas') {
    $areas = $db->query("SELECT * FROM `research_areas` WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();
    require __DIR__ . '/views/research-areas.php';
    exit;
}

// 4. Chi tiết lĩnh vực — /research-areas/{slug}
if (preg_match('#^/research-areas/([a-z0-9\-]+)$#', $path, $matches)) {
    $slug = $matches[1];
    $stmt = $db->prepare("SELECT * FROM `research_areas` WHERE slug = ? AND is_active = 1");
    $stmt->execute([$slug]);
    $area = $stmt->fetch();

    if (!$area) {
        require __DIR__ . '/views/404.php';
        exit;
    }

    // Related Projects
    $pStmt = $db->prepare("
        SELECT p.*, a.name AS area_name, a.slug AS area_slug 
        FROM `projects` p 
        LEFT JOIN `research_areas` a ON p.area_id = a.id 
        WHERE p.area_id = ? 
        ORDER BY p.sort_order ASC, p.id DESC
    ");
    $pStmt->execute([$area['id']]);
    $relatedProjects = $pStmt->fetchAll();

    // Related Articles
    $aStmt = $db->prepare("
        SELECT art.*, a.name AS area_name, a.slug AS area_slug 
        FROM `articles` art 
        LEFT JOIN `research_areas` a ON art.area_id = a.id 
        WHERE art.area_id = ? AND art.is_published = 1 
        ORDER BY art.id DESC
    ");
    $aStmt->execute([$area['id']]);
    $relatedArticles = $aStmt->fetchAll();

    require __DIR__ . '/views/research-area-detail.php';
    exit;
}

// 5. Danh sách dự án — /projects
if ($path === '/projects') {
    $selectedArea = trim($_GET['area'] ?? '');
    $selectedStatus = trim($_GET['status'] ?? '');

    $whereClauses = [];
    $params = [];

    if (!empty($selectedArea)) {
        $whereClauses[] = "a.slug = ?";
        $params[] = $selectedArea;
    }

    if (!empty($selectedStatus)) {
        $whereClauses[] = "p.status = ?";
        $params[] = $selectedStatus;
    }

    $sql = "
        SELECT p.*, a.name AS area_name, a.slug AS area_slug 
        FROM `projects` p 
        LEFT JOIN `research_areas` a ON p.area_id = a.id
    ";

    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(" AND ", $whereClauses);
    }
    $sql .= " ORDER BY p.sort_order ASC, p.id DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $projects = $stmt->fetchAll();

    $areas = $db->query("SELECT slug, name FROM `research_areas` WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

    require __DIR__ . '/views/projects.php';
    exit;
}

// 6. Chi tiết dự án — /projects/{slug}
if (preg_match('#^/projects/([a-z0-9\-]+)$#', $path, $matches)) {
    $slug = $matches[1];
    $stmt = $db->prepare("
        SELECT p.*, a.name AS area_name, a.slug AS area_slug 
        FROM `projects` p 
        LEFT JOIN `research_areas` a ON p.area_id = a.id 
        WHERE p.slug = ?
    ");
    $stmt->execute([$slug]);
    $project = $stmt->fetch();

    if (!$project) {
        require __DIR__ . '/views/404.php';
        exit;
    }

    // Related projects in same area
    $relatedProjects = [];
    if (!empty($project['area_id'])) {
        $rStmt = $db->prepare("
            SELECT p.*, a.name AS area_name, a.slug AS area_slug 
            FROM `projects` p 
            LEFT JOIN `research_areas` a ON p.area_id = a.id 
            WHERE p.area_id = ? AND p.id != ? 
            ORDER BY p.id DESC 
            LIMIT 2
        ");
        $rStmt->execute([$project['area_id'], $project['id']]);
        $relatedProjects = $rStmt->fetchAll();
    }

    require __DIR__ . '/views/project-detail.php';
    exit;
}

// 7. Bài viết nghiên cứu — /research
if ($path === '/research') {
    $selectedArea = trim($_GET['area'] ?? '');

    $sql = "
        SELECT art.*, a.name AS area_name, a.slug AS area_slug 
        FROM `articles` art 
        LEFT JOIN `research_areas` a ON art.area_id = a.id 
        WHERE art.is_published = 1
    ";
    $params = [];

    if (!empty($selectedArea)) {
        $sql .= " AND a.slug = ?";
        $params[] = $selectedArea;
    }
    $sql .= " ORDER BY art.id DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll();

    $areas = $db->query("SELECT slug, name FROM `research_areas` WHERE is_active = 1 ORDER BY sort_order ASC")->fetchAll();

    require __DIR__ . '/views/research.php';
    exit;
}

// 8. Chi tiết bài viết nghiên cứu — /research/{slug}
if (preg_match('#^/research/([a-z0-9\-]+)$#', $path, $matches)) {
    $slug = $matches[1];
    $stmt = $db->prepare("
        SELECT art.*, a.name AS area_name, a.slug AS area_slug 
        FROM `articles` art 
        LEFT JOIN `research_areas` a ON art.area_id = a.id 
        WHERE art.slug = ? AND art.is_published = 1
    ");
    $stmt->execute([$slug]);
    $article = $stmt->fetch();

    if (!$article) {
        require __DIR__ . '/views/404.php';
        exit;
    }

    // Related articles
    $relatedArticles = [];
    if (!empty($article['area_id'])) {
        $rStmt = $db->prepare("
            SELECT art.*, a.name AS area_name, a.slug AS area_slug 
            FROM `articles` art 
            LEFT JOIN `research_areas` a ON art.area_id = a.id 
            WHERE art.area_id = ? AND art.id != ? AND art.is_published = 1 
            ORDER BY art.id DESC 
            LIMIT 3
        ");
        $rStmt->execute([$article['area_id'], $article['id']]);
        $relatedArticles = $rStmt->fetchAll();
    }

    require __DIR__ . '/views/research-detail.php';
    exit;
}

// 9. Tin tức và cập nhật — /news
if ($path === '/news') {
    $newsList = $db->query("SELECT * FROM `news` WHERE is_published = 1 ORDER BY published_at DESC, id DESC")->fetchAll();
    require __DIR__ . '/views/news.php';
    exit;
}

// 10. Chi tiết tin tức — /news/{slug}
if (preg_match('#^/news/([a-z0-9\-]+)$#', $path, $matches)) {
    $slug = $matches[1];
    $stmt = $db->prepare("SELECT * FROM `news` WHERE slug = ? AND is_published = 1");
    $stmt->execute([$slug]);
    $item = $stmt->fetch();

    if (!$item) {
        require __DIR__ . '/views/404.php';
        exit;
    }

    require __DIR__ . '/views/news-detail.php';
    exit;
}

// 11. Liên hệ — /contact
if ($path === '/contact') {
    $successMsg = null;
    $errorMsg = null;

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $errorMsg = 'Mã bảo mật CSRF không hợp lệ. Vui lòng tải lại trang và thử lại.';
        } else {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $organization = trim($_POST['organization'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';

            if (empty($name) || empty($email) || empty($subject) || empty($message)) {
                $errorMsg = 'Vui lòng điền đầy đủ các trường bắt buộc (*).';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errorMsg = 'Địa chỉ email không hợp lệ. Vui lòng kiểm tra lại.';
            } else {
                $cStmt = $db->prepare("
                    INSERT INTO `contacts` (`name`, `email`, `organization`, `subject`, `message`, `ip_address`) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $cStmt->execute([$name, $email, $organization, $subject, $message, $ipAddress]);

                $successMsg = 'Cảm ơn bạn! Thông điệp trao đổi của bạn đã được gửi thành công đến nhóm nghiên cứu LabVale. Chúng tôi sẽ phản hồi trong thời gian sớm nhất.';
                $_POST = []; // Reset form values
            }
        }
    }

    require __DIR__ . '/views/contact.php';
    exit;
}

// 12. Chính sách quyền riêng tư — /privacy
if ($path === '/privacy') {
    require __DIR__ . '/views/privacy.php';
    exit;
}

// 13. Default: Custom 404
require __DIR__ . '/views/404.php';
exit;
