<?php
/**
 * LabVale Admin - Quản lý Dự án
 */
require_once __DIR__ . '/../includes/auth.php';
Auth::requireLogin();

$db = Database::getConnection();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Fetch all research areas for dropdown
$areas = $db->query("SELECT id, name FROM `research_areas` ORDER BY sort_order ASC")->fetchAll();

// Handle Form Submission (Create / Edit)
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        flash('error', 'Token CSRF không hợp lệ.');
        header('Location: ' . url('/admin/projects'));
        exit;
    }

    $postAction = $_POST['action'] ?? '';

    // DELETE Action
    if ($postAction === 'delete') {
        $delId = (int)($_POST['id'] ?? 0);
        $delStmt = $db->prepare("DELETE FROM `projects` WHERE id = ?");
        $delStmt->execute([$delId]);
        flash('success', 'Đã xóa dự án thành công.');
        header('Location: ' . url('/admin/projects'));
        exit;
    }

    // SAVE Action (Create or Update)
    if ($postAction === 'save') {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $areaId = !empty($_POST['area_id']) ? (int)$_POST['area_id'] : null;
        $status = $_POST['status'] ?? 'Đang nghiên cứu';
        $summary = trim($_POST['summary'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $techStack = trim($_POST['tech_stack'] ?? '');
        
        // Handle file upload or image text/URL
        $uploaded = handle_uploaded_image('image_file');
        if ($uploaded !== null) {
            $image = $uploaded;
        } else {
            $image = trim($_POST['image'] ?? 'project-rover.jpg');
            if (empty($image)) $image = 'project-rover.jpg';
        }

        $imageCaption = trim($_POST['image_caption'] ?? '');
        $githubUrl = trim($_POST['github_url'] ?? '');
        $isFeatured = !empty($_POST['is_featured']) ? 1 : 0;

        if (empty($slug)) {
            $slug = slugify($title);
        }

        if (empty($title)) {
            flash('error', 'Tiêu đề dự án không được để trống.');
            header('Location: ' . url('/admin/projects?action=' . ($id ? 'edit&id=' . $id : 'create')));
            exit;
        }

        if ($id > 0) {
            // Update
            $uStmt = $db->prepare("
                UPDATE `projects` 
                SET `area_id` = ?, `title` = ?, `slug` = ?, `status` = ?, `summary` = ?, 
                    `content` = ?, `tech_stack` = ?, `image` = ?, `image_caption` = ?, 
                    `github_url` = ?, `is_featured` = ?
                WHERE id = ?
            ");
            $uStmt->execute([$areaId, $title, $slug, $status, $summary, $content, $techStack, $image, $imageCaption, $githubUrl, $isFeatured, $id]);
            flash('success', 'Đã cập nhật thông tin dự án thành công.');
        } else {
            // Insert
            $iStmt = $db->prepare("
                INSERT INTO `projects` 
                (`area_id`, `title`, `slug`, `status`, `summary`, `content`, `tech_stack`, `image`, `image_caption`, `github_url`, `is_featured`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $iStmt->execute([$areaId, $title, $slug, $status, $summary, $content, $techStack, $image, $imageCaption, $githubUrl, $isFeatured]);
            flash('success', 'Đã tạo dự án nghiên cứu mới thành công.');
        }

        header('Location: ' . url('/admin/projects'));
        exit;
    }
}

// Prepare View
$adminNav = 'projects';

if ($action === 'create' || $action === 'edit') {
    $project = [
        'id' => 0,
        'title' => '',
        'slug' => '',
        'area_id' => '',
        'status' => 'Đang nghiên cứu',
        'summary' => '',
        'content' => '',
        'tech_stack' => '',
        'image' => 'project-rover.jpg',
        'image_caption' => '',
        'github_url' => '',
        'is_featured' => 0
    ];

    if ($action === 'edit' && $id > 0) {
        $pStmt = $db->prepare("SELECT * FROM `projects` WHERE id = ?");
        $pStmt->execute([$id]);
        $project = $pStmt->fetch();
        if (!$project) {
            flash('error', 'Dự án không tồn tại.');
            header('Location: ' . url('/admin/projects'));
            exit;
        }
    }

    $adminTitle = $id > 0 ? 'Hiệu chỉnh dự án: ' . $project['title'] : 'Thêm dự án nghiên cứu mới';
    require_once __DIR__ . '/views/header.php';
    ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-title"><?= e($adminTitle) ?></div>
            <a href="<?= url('/admin/projects') ?>" class="btn btn-secondary btn-sm">&larr; Quay lại danh sách</a>
        </div>
        <div style="padding: 2rem;">
            <form action="<?= url('/admin/projects' . ($id ? '?id=' . $id : '')) ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save">

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem;">
                    <div>
                        <div class="form-group">
                            <label for="title" class="form-label">Tên dự án *</label>
                            <input type="text" id="title" name="title" class="form-control" required value="<?= e($project['title']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="slug" class="form-label">Slug liên kết</label>
                            <input type="text" id="slug" name="slug" class="form-control" placeholder="Tự sinh nếu để trống" value="<?= e($project['slug']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="summary" class="form-label">Tóm tắt ngắn (1-2 câu)</label>
                            <textarea id="summary" name="summary" class="form-control" rows="3" required><?= e($project['summary']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="content" class="form-label">Nội dung kỹ thuật chi tiết (Soạn thảo bằng TinyMCE)</label>
                            <textarea id="content" name="content" class="form-control" rows="12"><?= htmlspecialchars($project['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                    </div>

                    <div>
                        <div class="form-group">
                            <label for="area_id" class="form-label">Lĩnh vực nghiên cứu</label>
                            <select id="area_id" name="area_id" class="form-control">
                                <option value="">-- Chọn lĩnh vực --</option>
                                <?php foreach ($areas as $a): ?>
                                    <option value="<?= $a['id'] ?>" <?= $project['area_id'] == $a['id'] ? 'selected' : '' ?>>
                                        <?= e($a['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="status" class="form-label">Trạng thái thực tế</label>
                            <select id="status" name="status" class="form-control">
                                <?php foreach (['Ý tưởng', 'Đang nghiên cứu', 'Đang phát triển', 'Hoàn thành'] as $st): ?>
                                    <option value="<?= $st ?>" <?= $project['status'] === $st ? 'selected' : '' ?>>
                                        <?= $st ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tech_stack" class="form-label">Tech Stack (ngăn cách bởi dấu phẩy)</label>
                            <input type="text" id="tech_stack" name="tech_stack" class="form-control" placeholder="ROS 2, STM32, C++, OpenCV" value="<?= e($project['tech_stack']) ?>">
                        </div>

                        <div class="form-group" style="background: #F8FAFC; border: 1px solid var(--admin-border); border-radius: 8px; padding: 1.25rem;">
                            <label class="form-label" style="font-weight: 600; color: var(--admin-navy);">
                                Ảnh dự án (Dán link ảnh ngoài HOẶC tải file từ máy tính)
                            </label>
                            
                            <label for="image" style="font-size: 0.85rem; color: var(--admin-muted); display: block; margin-bottom: 0.35rem;">
                                Cách 1: Dán link ảnh ngoài (https://...) hoặc tên file
                            </label>
                            <input type="text" id="image" name="image" class="form-control" value="<?= e($project['image']) ?>" placeholder="https://example.com/photo.jpg hoặc tên file" oninput="updateProjectImgPreview(this.value)">

                            <label for="image_file" style="font-size: 0.85rem; color: var(--admin-muted); display: block; margin-top: 0.75rem; margin-bottom: 0.35rem;">
                                Cách 2: Tải file mới từ máy tính (tùy chọn)
                            </label>
                            <input type="file" id="image_file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg,.gif" onchange="previewProjectFile(this)">

                            <div style="margin-top: 1rem; display: flex; align-items: center; gap: 1rem;">
                                <span style="font-size: 0.85rem; color: var(--admin-muted);">Xem trước:</span>
                                <div style="width: 130px; height: 85px; border-radius: 6px; overflow: hidden; border: 1px solid var(--admin-border); background: #FFFFFF;">
                                    <img id="project_preview" src="<?= image_url($project['image'], 'project-rover.jpg') ?>" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='<?= asset('images/hero-lab.jpg') ?>';">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="image_caption" class="form-label">Chú thích ảnh</label>
                            <input type="text" id="image_caption" name="image_caption" class="form-control" value="<?= e($project['image_caption']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="github_url" class="form-label">Kho GitHub (nếu có)</label>
                            <input type="url" id="github_url" name="github_url" class="form-control" placeholder="https://github.com/..." value="<?= e($project['github_url']) ?>">
                        </div>

                        <div class="form-group" style="margin-top: 1.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem;">
                                <input type="checkbox" name="is_featured" value="1" <?= $project['is_featured'] ? 'checked' : '' ?>>
                                <span>Hiển thị nổi bật ở trang chủ</span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                            Lưu thông tin dự án
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
                    height: 440,
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

        function updateProjectImgPreview(val) {
            const preview = document.getElementById('project_preview');
            if (!preview) return;
            val = (val || '').trim();
            if (!val) {
                preview.src = '<?= asset('images/project-rover.jpg') ?>';
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

        function previewProjectFile(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('project_preview');
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

// LIST Action (Default)
$adminTitle = 'Quản lý Dự án nghiên cứu';
require_once __DIR__ . '/views/header.php';

$projects = $db->query("
    SELECT p.*, a.name AS area_name 
    FROM `projects` p 
    LEFT JOIN `research_areas` a ON p.area_id = a.id 
    ORDER BY p.id DESC
")->fetchAll();
?>

<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">Danh sách các dự án thực nghiệm</div>
        <a href="<?= url('/admin/projects?action=create') ?>" class="btn btn-primary btn-sm">+ Thêm dự án mới</a>
    </div>

    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Tên dự án</th>
                    <th>Lĩnh vực</th>
                    <th>Trạng thái</th>
                    <th>Tech Stack</th>
                    <th style="text-align: right; width: 140px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($projects)): ?>
                    <?php foreach ($projects as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td>
                                <strong><?= e($p['title']) ?></strong>
                                <?php if ($p['is_featured']): ?>
                                    <span style="font-size: 0.7rem; background-color: #FEF3C7; color: #92400E; padding: 0.15rem 0.4rem; border-radius: 3px; margin-left: 0.35rem;">Nổi bật</span>
                                <?php endif; ?>
                            </td>
                            <td><?= e($p['area_name'] ?? 'Chưa gắn') ?></td>
                            <td>
                                <span class="project-status-tag" style="position: static; font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 4px;">
                                    <?= e($p['status']) ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 0.8rem; color: var(--admin-muted); font-family: monospace;">
                                    <?= e($p['tech_stack']) ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div class="admin-actions-cell" style="justify-content: flex-end;">
                                    <a href="<?= url('/admin/projects?action=edit&id=' . $p['id']) ?>" class="btn btn-secondary btn-sm">Sửa</a>
                                    <form action="<?= url('/admin/projects') ?>" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa dự án này không?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--admin-muted);">Chưa có dự án nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/views/footer.php'; ?>
