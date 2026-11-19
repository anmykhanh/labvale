<?php
/**
 * LabVale Admin - Quản lý Tin tức & Nhật ký
 */
require_once __DIR__ . '/../includes/auth.php';
Auth::requireLogin();

$db = Database::getConnection();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Handle Form Submissions
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        flash('error', 'Token CSRF không hợp lệ.');
        header('Location: ' . url('/admin/news'));
        exit;
    }

    $postAction = $_POST['action'] ?? '';

    // DELETE
    if ($postAction === 'delete') {
        $delId = (int)($_POST['id'] ?? 0);
        $stmt = $db->prepare("DELETE FROM `news` WHERE id = ?");
        $stmt->execute([$delId]);
        flash('success', 'Đã xóa bản tin thành công.');
        header('Location: ' . url('/admin/news'));
        exit;
    }

    // SAVE
    if ($postAction === 'save') {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $summary = trim($_POST['summary'] ?? '');
        $content = trim($_POST['content'] ?? '');
        
        // Handle file upload or image text/URL
        $uploaded = handle_uploaded_image('image_file');
        if ($uploaded !== null) {
            $image = $uploaded;
        } else {
            $image = trim($_POST['image'] ?? 'hero-lab.jpg');
            if (empty($image)) $image = 'hero-lab.jpg';
        }

        $isPublished = !empty($_POST['is_published']) ? 1 : 0;

        if (empty($slug)) {
            $slug = slugify($title);
        }

        if (empty($title)) {
            flash('error', 'Tiêu đề bản tin không được để trống.');
            header('Location: ' . url('/admin/news?action=' . ($id ? 'edit&id=' . $id : 'create')));
            exit;
        }

        if ($id > 0) {
            $uStmt = $db->prepare("
                UPDATE `news` 
                SET `title` = ?, `slug` = ?, `summary` = ?, `content` = ?, `image` = ?, `is_published` = ?
                WHERE id = ?
            ");
            $uStmt->execute([$title, $slug, $summary, $content, $image, $isPublished, $id]);
            flash('success', 'Đã cập nhật bản tin.');
        } else {
            $iStmt = $db->prepare("
                INSERT INTO `news` 
                (`title`, `slug`, `summary`, `content`, `image`, `is_published`) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $iStmt->execute([$title, $slug, $summary, $content, $image, $isPublished]);
            flash('success', 'Đã đăng bản tin mới.');
        }

        header('Location: ' . url('/admin/news'));
        exit;
    }
}

$adminNav = 'news';

if ($action === 'create' || $action === 'edit') {
    $item = [
        'id' => 0,
        'title' => '',
        'slug' => '',
        'summary' => '',
        'content' => '',
        'image' => 'hero-lab.jpg',
        'is_published' => 1
    ];

    if ($action === 'edit' && $id > 0) {
        $stmt = $db->prepare("SELECT * FROM `news` WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();
        if (!$item) {
            flash('error', 'Bản tin không tồn tại.');
            header('Location: ' . url('/admin/news'));
            exit;
        }
    }

    $adminTitle = $id > 0 ? 'Hiệu chỉnh bản tin: ' . $item['title'] : 'Đăng bản tin mới';
    require_once __DIR__ . '/views/header.php';
    ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title"><?= e($adminTitle) ?></div>
            <a href="<?= url('/admin/news') ?>" class="btn btn-secondary btn-sm">&larr; Quay lại danh sách</a>
        </div>
        <div style="padding: 2rem;">
            <form action="<?= url('/admin/news' . ($id ? '?id=' . $id : '')) ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save">

                <div class="form-group">
                    <label for="title" class="form-label">Tiêu đề bản tin *</label>
                    <input type="text" id="title" name="title" class="form-control" required value="<?= e($item['title']) ?>">
                </div>

                <div class="form-group">
                    <label for="slug" class="form-label">Slug liên kết</label>
                    <input type="text" id="slug" name="slug" class="form-control" placeholder="Tự sinh nếu để trống" value="<?= e($item['slug']) ?>">
                </div>

                <div class="form-group">
                    <label for="summary" class="form-label">Tóm tắt nội dung *</label>
                    <textarea id="summary" name="summary" class="form-control" rows="3" required><?= e($item['summary']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="content" class="form-label">Nội dung chi tiết * (Soạn thảo bằng TinyMCE)</label>
                    <textarea id="content" name="content" class="form-control" rows="10"><?= htmlspecialchars($item['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <!-- Image Settings with Remote URL & Direct Upload -->
                <div class="form-group" style="background: #F8FAFC; border: 1px solid var(--admin-border); border-radius: 8px; padding: 1.25rem;">
                    <label class="form-label" style="font-weight: 600; color: var(--admin-navy);">
                        Hình ảnh bản tin (Dán link ảnh ngoài HOẶC tải file từ máy tính)
                    </label>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; align-items: start; margin-top: 0.5rem;">
                        <div>
                            <label for="image" style="font-size: 0.85rem; color: var(--admin-muted); display: block; margin-bottom: 0.35rem;">
                                Cách 1: Dán link ảnh ngoài (https://...) hoặc tên file nội bộ
                            </label>
                            <input type="text" id="image" name="image" class="form-control" value="<?= e($item['image']) ?>" placeholder="https://example.com/photo.jpg hoặc tên file" oninput="updateImagePreview(this.value)">
                            <div class="form-help" style="margin-top: 0.35rem;">
                                Có thể dán trực tiếp link ảnh từ bất kỳ trang nào (Unsplash, Google, Wikimedia, v.v.).
                            </div>
                        </div>

                        <div>
                            <label for="image_file" style="font-size: 0.85rem; color: var(--admin-muted); display: block; margin-bottom: 0.35rem;">
                                Cách 2: Tải file mới từ máy tính (tùy chọn)
                            </label>
                            <input type="file" id="image_file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg,.gif" onchange="previewSelectedFile(this)">
                            <div class="form-help" style="margin-top: 0.35rem;">
                                Nếu chọn file, hệ thống sẽ ưu tiên tải file này lên máy chủ.
                            </div>
                        </div>
                    </div>

                    <!-- Live Image Preview Box -->
                    <div style="margin-top: 1rem; display: flex; align-items: center; gap: 1rem;">
                        <span style="font-size: 0.85rem; color: var(--admin-muted);">Xem trước ảnh:</span>
                        <div style="width: 140px; height: 90px; border-radius: 6px; overflow: hidden; border: 1px solid var(--admin-border); background: #FFFFFF; display: flex; align-items: center; justify-content: center;">
                            <img id="img_preview" src="<?= image_url($item['image'], 'hero-lab.jpg') ?>" alt="Xem trước" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='<?= asset('images/hero-lab.jpg') ?>';">
                        </div>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem;">
                        <input type="checkbox" name="is_published" value="1" <?= $item['is_published'] ? 'checked' : '' ?>>
                        <span>Công khai bản tin</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">
                    Lưu bản tin
                </button>
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
                    height: 420,
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

        function updateImagePreview(val) {
            const preview = document.getElementById('img_preview');
            if (!preview) return;
            val = (val || '').trim();
            if (!val) {
                preview.src = '<?= asset('images/hero-lab.jpg') ?>';
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

        function previewSelectedFile(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('img_preview');
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
$adminTitle = 'Quản lý Tin tức & Nhật ký';
require_once __DIR__ . '/views/header.php';

$news = $db->query("SELECT * FROM `news` ORDER BY id DESC")->fetchAll();
?>

<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">Danh sách tin tức &amp; nhật ký nghiên cứu</div>
        <a href="<?= url('/admin/news?action=create') ?>" class="btn btn-primary btn-sm">+ Đăng bản tin mới</a>
    </div>

    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Tiêu đề</th>
                    <th>Ngày đăng</th>
                    <th>Trạng thái</th>
                    <th style="text-align: right; width: 140px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($news)): ?>
                    <?php foreach ($news as $n): ?>
                        <tr>
                            <td><?= $n['id'] ?></td>
                            <td>
                                <strong><?= e($n['title']) ?></strong>
                            </td>
                            <td><?= format_date($n['published_at']) ?></td>
                            <td>
                                <?php if ($n['is_published']): ?>
                                    <span style="font-size: 0.75rem; background-color: #ECFDF5; color: #047857; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 600;">Công khai</span>
                                <?php else: ?>
                                    <span style="font-size: 0.75rem; background-color: #F1F5F9; color: #64748B; padding: 0.2rem 0.5rem; border-radius: 4px;">Nháp</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right;">
                                <div class="admin-actions-cell" style="justify-content: flex-end;">
                                    <a href="<?= url('/admin/news?action=edit&id=' . $n['id']) ?>" class="btn btn-secondary btn-sm">Sửa</a>
                                    <form action="<?= url('/admin/news') ?>" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa bản tin này?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--admin-muted);">Chưa có bản tin nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/views/footer.php'; ?>
