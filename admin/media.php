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

    // 1. Process Upload File
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
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif'];
        if (!in_array($ext, $allowedExts, true)) {
            flash('error', 'Chỉ chấp nhận định dạng ảnh thật: JPG, JPEG, PNG, WEBP, SVG, GIF.');
            header('Location: ' . url('/admin/media'));
            exit;
        }

        // Safe MIME type detection with fallbacks
        $mime = '';
        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mime = @finfo_file($finfo, $file['tmp_name']);
                @finfo_close($finfo);
            }
        }
        if (empty($mime) && function_exists('mime_content_type')) {
            $mime = @mime_content_type($file['tmp_name']);
        }
        if (empty($mime) && function_exists('exif_imagetype')) {
            $imgType = @exif_imagetype($file['tmp_name']);
            $mimeMap = [
                IMAGETYPE_JPEG => 'image/jpeg',
                IMAGETYPE_PNG => 'image/png',
                IMAGETYPE_WEBP => 'image/webp',
                IMAGETYPE_GIF => 'image/gif'
            ];
            $mime = $mimeMap[$imgType] ?? '';
        }
        if (empty($mime) && ($info = @getimagesize($file['tmp_name']))) {
            $mime = $info['mime'] ?? '';
        }
        if ($ext === 'svg' && (empty($mime) || in_array($mime, ['text/plain', 'text/xml', 'image/svg+xml']))) {
            $mime = 'image/svg+xml';
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'image/gif'];
        if (!empty($mime) && !in_array($mime, $allowedMimes, true)) {
            flash('error', 'Nội dung file không đúng định dạng ảnh hợp lệ (' . e($mime) . ').');
            header('Location: ' . url('/admin/media'));
            exit;
        }

        // Generate sanitized unique filename
        $baseName = slugify(pathinfo($file['name'], PATHINFO_FILENAME));
        if (empty($baseName)) $baseName = 'image';
        $finalName = $baseName . '-' . time() . '.' . $ext;
        $destPath = $uploadDir . '/' . $finalName;

        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            try {
                $stmt = $db->prepare("INSERT INTO `media` (`filename`, `original_name`, `file_path`, `file_size`, `mime_type`, `alt_text`) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$finalName, $file['name'], 'assets/images/' . $finalName, $file['size'], $mime ?: ('image/' . $ext), $baseName]);
            } catch (Exception $e) {}

            flash('success', 'Đã tải lên ảnh thành công: ' . $finalName);
        } else {
            flash('error', 'Không thể lưu file vào thư mục assets/images.');
        }

        header('Location: ' . url('/admin/media'));
        exit;
    }

    // 2. Process Remote Image Download (URL)
    if (!empty($_POST['remote_image_url'])) {
        $remoteUrl = trim($_POST['remote_image_url']);
        if (filter_var($remoteUrl, FILTER_VALIDATE_URL)) {
            $parsedPath = parse_url($remoteUrl, PHP_URL_PATH);
            $ext = strtolower(pathinfo($parsedPath ?: '', PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'svg', 'gif'])) {
                $ext = 'jpg';
            }

            $imgData = @file_get_contents($remoteUrl, false, stream_context_create([
                'http' => ['timeout' => 15, 'user_agent' => 'Mozilla/5.0 LabValeMediaBot/1.0']
            ]));

            if ($imgData && strlen($imgData) > 500) {
                $baseName = 'remote-' . time();
                $finalName = $baseName . '.' . $ext;
                $destPath = $uploadDir . '/' . $finalName;

                if (file_put_contents($destPath, $imgData)) {
                    try {
                        $stmt = $db->prepare("INSERT INTO `media` (`filename`, `original_name`, `file_path`, `file_size`, `mime_type`, `alt_text`) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$finalName, $remoteUrl, 'assets/images/' . $finalName, strlen($imgData), 'image/' . $ext, 'remote-image']);
                    } catch (Exception $e) {}

                    flash('success', 'Đã tải ảnh từ link ngoài về máy chủ thành công: ' . $finalName);
                } else {
                    flash('error', 'Không thể ghi ảnh vào thư mục máy chủ.');
                }
            } else {
                flash('error', 'Không thể tải ảnh từ liên kết này (link có thể chặn bot hoặc không truy cập được).');
            }
        } else {
            flash('error', 'Liên kết URL ảnh không hợp lệ.');
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

<!-- Upload & Remote Image Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Upload Local File -->
    <div class="admin-card" style="margin-bottom: 0;">
        <div class="admin-card-header">
            <div class="admin-card-title">1. Tải ảnh lên từ máy tính</div>
        </div>
        <div style="padding: 1.5rem;">
            <form action="<?= url('/admin/media') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="image_file" class="form-label">Chọn tệp hình ảnh (JPG, PNG, WEBP, SVG - tối đa 10MB)</label>
                    <input type="file" id="image_file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg,.gif" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Tải lên máy chủ
                </button>
            </form>
            <div class="form-help" style="margin-top: 0.75rem;">
                Ảnh sẽ được lưu trực tiếp vào thư mục <code>assets/images/</code>.
            </div>
        </div>
    </div>

    <!-- Download From Remote URL -->
    <div class="admin-card" style="margin-bottom: 0;">
        <div class="admin-card-header">
            <div class="admin-card-title">2. Tải ảnh từ liên kết ngoài (URL)</div>
        </div>
        <div style="padding: 1.5rem;">
            <form action="<?= url('/admin/media') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label for="remote_image_url" class="form-label">Dán link ảnh ngoài (https://...)</label>
                    <input type="url" id="remote_image_url" name="remote_image_url" class="form-control" placeholder="https://example.com/photo.jpg" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Tải về thư viện máy chủ
                </button>
            </form>
            <div class="form-help" style="margin-top: 0.75rem;">
                Hệ thống sẽ tự động tải file ảnh từ liên kết về lưu trữ nội bộ tại máy chủ.
            </div>
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
