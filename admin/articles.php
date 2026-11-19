<?php
/**
 * LabVale Admin - Quản lý Bài viết nghiên cứu (Góc nghiên cứu)
 */
require_once __DIR__ . '/../includes/auth.php';
Auth::requireLogin();

$db = Database::getConnection();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

$areas = $db->query("SELECT id, name FROM `research_areas` ORDER BY sort_order ASC")->fetchAll();

// Handle Form Submission
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        flash('error', 'Token CSRF không hợp lệ.');
        header('Location: ' . url('/admin/articles'));
        exit;
    }

    $postAction = $_POST['action'] ?? '';

    // DELETE
    if ($postAction === 'delete') {
        $delId = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("DELETE FROM `articles` WHERE id = ?");
        $stmt->execute([$delId]);
        flash('success', 'Đã xóa bài viết thành công.');
        header('Location: ' . url('/admin/articles'));
        exit;
    }

    // SAVE
    if ($postAction === 'save') {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $areaId = !empty($_POST['area_id']) ? (int)$_POST['area_id'] : null;
        $summary = trim($_POST['summary'] ?? '');
        $content = trim($_POST['content'] ?? '');
        
        // Handle file upload or image text/URL
        $uploaded = handle_uploaded_image('image_file');
        if ($uploaded !== null) {
            $image = $uploaded;
        } else {
            $image = trim($_POST['image'] ?? 'article-drone-principles.jpg');
            if (empty($image)) $image = 'article-drone-principles.jpg';
        }

        $readingTime = trim($_POST['reading_time'] ?? '6 phút');
        $author = trim($_POST['author'] ?? 'Nhóm Nghiên Cứu LabVale');
        $tags = trim($_POST['tags'] ?? '');
        $isPublished = !empty($_POST['is_published']) ? 1 : 0;

        if (empty($slug)) {
            $slug = slugify($title);
        }

        if (empty($title)) {
            flash('error', 'Tiêu đề bài viết không được để trống.');
            header('Location: ' . url('/admin/articles?action=' . ($id ? 'edit&id=' . $id : 'create')));
            exit;
        }

        if ($id > 0) {
            $uStmt = $db->prepare("
                UPDATE `articles` 
                SET `area_id` = ?, `title` = ?, `slug` = ?, `summary` = ?, `content` = ?, 
                    `image` = ?, `reading_time` = ?, `author` = ?, `tags` = ?, `is_published` = ?
                WHERE id = ?
            ");
            $uStmt->execute([$areaId, $title, $slug, $summary, $content, $image, $readingTime, $author, $tags, $isPublished, $id]);
            flash('success', 'Đã cập nhật bài viết nghiên cứu.');
        } else {
            $iStmt = $db->prepare("
                INSERT INTO `articles` 
                (`area_id`, `title`, `slug`, `summary`, `content`, `image`, `reading_time`, `author`, `tags`, `is_published`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $iStmt->execute([$areaId, $title, $slug, $summary, $content, $image, $readingTime, $author, $tags, $isPublished]);
            flash('success', 'Đã xuất bản bài viết nghiên cứu mới.');
        }

        header('Location: ' . url('/admin/articles'));
        exit;
    }
}

$adminNav = 'articles';

if ($action === 'create' || $action === 'edit') {
    $article = [
        'id' => 0,
        'title' => '',
        'slug' => '',
        'area_id' => '',
        'summary' => '',
        'content' => '',
        'image' => 'article-drone-principles.jpg',
        'reading_time' => '6 phút',
        'author' => 'Nhóm Nghiên Cứu LabVale',
        'tags' => '',
        'is_published' => 1
    ];

    if ($action === 'edit' && $id > 0) {
        $aStmt = $db->prepare("SELECT * FROM `articles` WHERE id = ?");
        $aStmt->execute([$id]);
        $article = $aStmt->fetch();
        if (!$article) {
            flash('error', 'Bài viết không tồn tại.');
            header('Location: ' . url('/admin/articles'));
            exit;
        }
    }

    $adminTitle = $id > 0 ? 'Hiệu chỉnh bài viết: ' . $article['title'] : 'Viết bài nghiên cứu mới';
    require_once __DIR__ . '/views/header.php';
    ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title"><?= e($adminTitle) ?></div>
            <a href="<?= url('/admin/articles') ?>" class="btn btn-secondary btn-sm">&larr; Quay lại danh sách</a>
        </div>
        <div style="padding: 2rem;">
            <form action="<?= url('/admin/articles' . ($id ? '?id=' . $id : '')) ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save">

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem;">
                    <div>
                        <div class="form-group">
                            <label for="title" class="form-label">Tiêu đề bài viết *</label>
                            <input type="text" id="title" name="title" class="form-control" required value="<?= e($article['title']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="slug" class="form-label">Slug URL</label>
                            <input type="text" id="slug" name="slug" class="form-control" placeholder="Tự sinh nếu để trống" value="<?= e($article['slug']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="summary" class="form-label">Tóm tắt ngắn *</label>
                            <textarea id="summary" name="summary" class="form-control" rows="3" required><?= e($article['summary']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="content" class="form-label">Nội dung bài viết * (Soạn thảo bằng TinyMCE)</label>
                            <textarea id="content" name="content" class="form-control" rows="15"><?= htmlspecialchars($article['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                    </div>

                    <div>
                        <div class="form-group">
                            <label for="area_id" class="form-label">Chuyên mục / Lĩnh vực</label>
                            <select id="area_id" name="area_id" class="form-control">
                                <option value="">-- Chọn lĩnh vực --</option>
                                <?php foreach ($areas as $a): ?>
                                    <option value="<?= $a['id'] ?>" <?= $article['area_id'] == $a['id'] ? 'selected' : '' ?>>
                                        <?= e($a['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group" style="background: #F8FAFC; border: 1px solid var(--admin-border); border-radius: 8px; padding: 1.25rem;">
                            <label class="form-label" style="font-weight: 600; color: var(--admin-navy);">
                                Ảnh bài viết (Dán link ảnh ngoài HOẶC tải file từ máy tính)
                            </label>
                            
                            <label for="image" style="font-size: 0.85rem; color: var(--admin-muted); display: block; margin-bottom: 0.35rem;">
                                Cách 1: Dán link ảnh ngoài (https://...) hoặc tên file
                            </label>
                            <input type="text" id="image" name="image" class="form-control" value="<?= e($article['image']) ?>" placeholder="https://example.com/photo.jpg hoặc tên file" oninput="updateArticleImgPreview(this.value)">

                            <label for="image_file" style="font-size: 0.85rem; color: var(--admin-muted); display: block; margin-top: 0.75rem; margin-bottom: 0.35rem;">
                                Cách 2: Tải file mới từ máy tính (tùy chọn)
                            </label>
                            <input type="file" id="image_file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg,.gif" onchange="previewArticleFile(this)">

                            <div style="margin-top: 1rem; display: flex; align-items: center; gap: 1rem;">
                                <span style="font-size: 0.85rem; color: var(--admin-muted);">Xem trước:</span>
                                <div style="width: 130px; height: 85px; border-radius: 6px; overflow: hidden; border: 1px solid var(--admin-border); background: #FFFFFF;">
                                    <img id="article_preview" src="<?= image_url($article['image'], 'article-drone-principles.jpg') ?>" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='<?= asset('images/hero-lab.jpg') ?>';">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reading_time" class="form-label">Thời lượng đọc</label>
                            <input type="text" id="reading_time" name="reading_time" class="form-control" placeholder="Ví dụ: 7 phút" value="<?= e($article['reading_time']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="author" class="form-label">Tác giả</label>
                            <input type="text" id="author" name="author" class="form-control" value="<?= e($article['author']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="tags" class="form-label">Từ khóa (phân cách bởi dấu phẩy)</label>
                            <input type="text" id="tags" name="tags" class="form-control" placeholder="Robotics, SLAM, ROS 2" value="<?= e($article['tags']) ?>">
                        </div>

                        <div class="form-group" style="margin-top: 1.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem;">
                                <input type="checkbox" name="is_published" value="1" <?= $article['is_published'] ? 'checked' : '' ?>>
                                <span>Công khai bài viết</span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                            Lưu bài viết
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- TinyMCE WYSIWYG Editor (Community Edition - No API key required) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: '#content',
                    height: 480,
                    menubar: 'edit insert view format table tools',
                    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table wordcount',
                    toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link table | code fullscreen preview',
                    content_style: "@import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600&display=swap'); body { font-family: 'Be Vietnam Pro', -apple-system, BlinkMacSystemFont, sans-serif; font-size: 15px; line-height: 1.7; color: #172033; padding: 12px; }",
                    branding: false,
                    promotion: false,
                    statusbar: true,
                    setup: function (editor) {
                        editor.on('change', function () {
                            editor.save();
                        });
                    }
                });

                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function() {
                        tinymce.triggerSave();
                    });
                }
            }
        });

        function updateArticleImgPreview(val) {
            const preview = document.getElementById('article_preview');
            if (!preview) return;
            val = (val || '').trim();
            if (!val) {
                preview.src = '<?= asset('images/article-drone-principles.jpg') ?>';
                return;
            }
            if (val.startsWith('http://') || val.startsWith('https://') || val.startsWith('//') || val.startsWith('data:image')) {
                preview.src = val;
            } else if (val.startsWith('assets/')) {
                preview.src = '<?= url('') ?>/' + val;
            } else {
                preview.src = '<?= asset('images/') ?>' + val.replace(/^\/+/, '');
            }
        }

        function previewArticleFile(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('article_preview');
                    if (preview) preview.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <?php
    require_once __DIR__ . '/views/footer.php';
    exit;
}

// List Action
$adminTitle = 'Quản lý Bài viết kỹ thuật';
require_once __DIR__ . '/views/header.php';

$articles = $db->query("
    SELECT art.*, a.name AS area_name 
    FROM `articles` art 
    LEFT JOIN `research_areas` a ON art.area_id = a.id 
    ORDER BY art.id DESC
")->fetchAll();
?>

<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">Danh sách bài viết trong Góc nghiên cứu</div>
        <a href="<?= url('/admin/articles?action=create') ?>" class="btn btn-primary btn-sm">+ Viết bài mới</a>
    </div>

    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Tiêu đề bài viết</th>
                    <th>Chuyên mục</th>
                    <th>Thời lượng</th>
                    <th>Trạng thái</th>
                    <th style="text-align: right; width: 140px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($articles)): ?>
                    <?php foreach ($articles as $art): ?>
                        <tr>
                            <td><?= $art['id'] ?></td>
                            <td>
                                <strong><?= e($art['title']) ?></strong>
                            </td>
                            <td><?= e($art['area_name'] ?? 'Chung') ?></td>
                            <td><?= e($art['reading_time']) ?></td>
                            <td>
                                <?php if ($art['is_published']): ?>
                                    <span style="font-size: 0.75rem; background-color: #ECFDF5; color: #047857; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600;">Công khai</span>
                                <?php else: ?>
                                    <span style="font-size: 0.75rem; background-color: #F1F5F9; color: #64748B; padding: 0.2rem 0.5rem; border-radius: 4px;">Nháp</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div class="admin-actions-cell" style="justify-content: flex-end;">
                                    <a href="<?= url('/admin/articles?action=edit&id=' . $art['id']) ?>" class="btn btn-secondary btn-sm">Sửa</a>
                                    <form action="<?= url('/admin/articles') ?>" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này không?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $art['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--admin-muted);">Chưa có bài viết nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/views/footer.php'; ?>
