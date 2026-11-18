<?php
/**
 * LabVale Admin - Quản lý Hình ảnh thực tế
 */
require_once __DIR__ . '/../includes/auth.php';
Auth::requireLogin();

$db = Database::getConnection();
$uploadDir = ROOT_DIR . '/assets/images';

// Handle Upload
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        flash('error', 'Token CSRF không hợp lệ.');
        header('Location: ' . url('/admin/media'));
        exit;
    }

    if (!empty($_FILES['image_file']['name'])) {
        $file = $_FILES['image_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'Lỗi tải file lên máy chủ (mã: ' . $file['error'] . ').');
            header('Location: ' . url('/admin/media'));
            exit;
        }

        // Validate Size (Max 10MB)
        if ($file['size'] > 10 * 1024 * 1024) {
            flash('error', 'Kích thước file vượt quá giới hạn 10MB.');
            header('Location: ' . url('/admin/media'));
            exit;
        }

        // Validate Extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        if (!in_array($ext, $allowedExts, true)) {
            flash('error', 'Chỉ chấp nhận định dạng ảnh thật: JPG, JPEG, PNG, WEBP, SVG.');
            header('Location: ' . url('/admin/media'));
            exit;
        }

        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
        if (!in_array($mime, $allowedMimes, true)) {
            flash('error', 'Nội dung file không đúng định dạng ảnh hợp lệ.');
            header('Location: ' . url('/admin/media'));
            exit;
        }

        // Generate sanitized unique filename
        $baseName = slugify(pathinfo($file['name'], PATHINFO_FILENAME));
        $finalName = $baseName . '-' . time() . '.' . $ext;
        $destPath = $uploadDir . '/' . $finalName;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            // Record in media table
            $stmt = $db->prepare("INSERT INTO `media` (`filename`, `original_name`, `file_path`, `file_size`, `mime_type`, `alt_text`) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$finalName, $file['name'], 'assets/images/' . $finalName, $file['size'], $mime, $baseName]);

            flash('success', 'Đã tải lên ảnh thành công: ' . $finalName);
        } else {
            flash('error', 'Không thể lưu file vào thư mục assets/images.');
        }

        header('Location: ' . url('/admin/media'));
        exit;
    }
}

$adminNav = 'media';
$adminTitle = 'Thư viện hình ảnh thực tế';
require_once __DIR__ . '/views/header.php';

// Scan existing files in assets/images
$existingImages = [];
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    foreach ($files as $f) {
        if ($f !== '.' && $f !== '..' && preg_match('/\.(jpg|jpeg|png|webp|svg)$/i', $f)) {
            $path = $uploadDir . '/' . $f;
            $existingImages[] = [
                'name' => $f,
                'size' => filesize($path),
                'mtime' => filemtime($path)
            ];
        }
    }
}
usort($existingImages, fn($a, $b) => $b['mtime'] <=> $a['mtime']);
?>

<!-- Upload Card -->
<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">Tải lên hình ảnh chụp thực tế</div>
    </div>
    <div style="padding: 1.5rem;">
        <form action="<?= url('/admin/media') ?>" method="POST" enctype="multipart/form-data" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
            <?= csrf_field() ?>
            <div style="flex: 1; min-width: 280px;">
                <label for="image_file" class="form-label">Chọn tệp hình ảnh (Ảnh chụp thực tế, tối đa 10MB)</label>
                <input type="file" id="image_file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg" required>
            </div>
            <button type="submit" class="btn btn-primary">
                Tải lên thư viện
            </button>
        </form>
        <div class="form-help" style="margin-top: 0.75rem;">
            Quy định: Tuyệt đối không dùng ảnh do AI tạo ra hoặc ảnh CGI 3D. Chỉ dùng ảnh chụp robot, drone, mạch điện tử hoặc bàn thí nghiệm thực tế.
        </div>
    </div>
</div>

<!-- Image Gallery Grid -->
<div class="admin-card">
    <div class="admin-card-header">
        <div class="admin-card-title">Kho ảnh hiện tại (<?= count($existingImages) ?> tệp)</div>
    </div>
    <div style="padding: 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.5rem;">
            <?php foreach ($existingImages as $img): 
                $fileUrl = asset('images/' . $img['name']);
            ?>
                <div style="border: 1px solid var(--admin-border); border-radius: 6px; overflow: hidden; background: #FFFFFF; display: flex; flex-direction: column;">
                    <div style="height: 140px; background-color: #F8FAFC; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <img src="<?= $fileUrl ?>" alt="<?= e($img['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                    </div>
                    <div style="padding: 0.75rem; font-size: 0.8rem; display: flex; flex-direction: column; gap: 0.35rem; flex: 1; justify-content: space-between;">
                        <div>
                            <strong style="display: block; word-break: break-all; color: var(--admin-navy);"><?= e($img['name']) ?></strong>
                            <span style="color: var(--admin-muted);"><?= round($img['size'] / 1024, 1) ?> KB</span>
                        </div>
                        <div style="margin-top: 0.5rem;">
                            <button type="button" class="btn btn-secondary btn-sm" style="width: 100%; font-size: 0.75rem;" onclick="copyFileName('<?= e($img['name']) ?>', this)">
                                Sao chép tên file
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function copyFileName(name, btn) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(name).then(() => {
            const old = btn.textContent;
            btn.textContent = 'Đã chép!';
            setTimeout(() => btn.textContent = old, 1500);
        });
    } else {
        prompt('Tên file:', name);
    }
}
</script>

<?php require_once __DIR__ . '/views/footer.php'; ?>
