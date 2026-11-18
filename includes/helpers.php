<?php
/**
 * LabVale - Helper Functions
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Generate full URL path respecting base path
 */
function url(string $path = ''): string {
    $cleanPath = '/' . ltrim($path, '/');
    if ($cleanPath === '/') {
        return BASE_PATH === '' ? '/' : BASE_PATH . '/';
    }
    return BASE_PATH . $cleanPath;
}

/**
 * Generate asset URL with automatic timestamp cache-busting
 */
function asset(string $path = ''): string {
    $cleanPath = ltrim($path, '/');
    $fullUrl = url('assets/' . $cleanPath);
    $realPath = __DIR__ . '/../assets/' . $cleanPath;
    if (file_exists($realPath)) {
        return $fullUrl . '?v=' . filemtime($realPath);
    }
    return $fullUrl;
}

/**
 * Generate absolute asset URL (essential for Open Graph, Facebook, Twitter, Zalo)
 */
function asset_absolute(string $path = ''): string {
    $cleanPath = ltrim($path, '/');
    $fullUrl = rtrim(APP_URL, '/') . '/assets/' . $cleanPath;
    $realPath = __DIR__ . '/../assets/' . $cleanPath;
    if (file_exists($realPath)) {
        return $fullUrl . '?v=' . filemtime($realPath);
    }
    return $fullUrl;
}

/**
 * Escape string for safe HTML output
 */
function e(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * CSRF Token Generator
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Generate CSRF hidden input
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Verify CSRF Token
 */
function verify_csrf_token(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Flash message helper
 */
function flash(string $key, ?string $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return;
    }
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

/**
 * Format date in clean Vietnamese format
 */
function format_date(?string $dateStr): string {
    if (!$dateStr) return '';
    $timestamp = strtotime($dateStr);
    if (!$timestamp) return '';
    $months = [
        1 => 'tháng 1', 2 => 'tháng 2', 3 => 'tháng 3', 4 => 'tháng 4',
        5 => 'tháng 5', 6 => 'tháng 6', 7 => 'tháng 7', 8 => 'tháng 8',
        9 => 'tháng 9', 10 => 'tháng 10', 11 => 'tháng 11', 12 => 'tháng 12'
    ];
    $day = date('j', $timestamp);
    $monthNum = (int)date('n', $timestamp);
    $year = date('Y', $timestamp);
    return sprintf('%d %s, %d', $day, $months[$monthNum] ?? "tháng $monthNum", $year);
}

/**
 * Slugify Vietnamese string
 */
function slugify(string $text): string {
    $text = mb_strtolower(trim($text), 'UTF-8');
    
    // Map Vietnamese diacritics
    $charMap = [
        'à'=>'a','á'=>'a','ả'=>'a','ã'=>'a','ạ'=>'a','ă'=>'a','ằ'=>'a','ắ'=>'a','ẳ'=>'a','ẵ'=>'a','ặ'=>'a',
        'â'=>'a','ầ'=>'a','ấ'=>'a','ẩ'=>'a','ẫ'=>'a','ậ'=>'a',
        'đ'=>'d',
        'è'=>'e','é'=>'e','ẻ'=>'e','ẽ'=>'e','ẹ'=>'e','ê'=>'e','ề'=>'e','ế'=>'e','ể'=>'e','ễ'=>'e','ệ'=>'e',
        'ì'=>'i','í'=>'i','ỉ'=>'i','ĩ'=>'i','ị'=>'i',
        'ò'=>'o','ó'=>'o','ỏ'=>'o','õ'=>'o','ọ'=>'o','ô'=>'o','ồ'=>'o','ố'=>'o','ổ'=>'o','ỗ'=>'o','ộ'=>'o',
        'ơ'=>'o','ờ'=>'o','ớ'=>'o','ở'=>'o','ỡ'=>'o','ợ'=>'o',
        'ù'=>'u','ú'=>'u','ủ'=>'u','ũ'=>'u','ụ'=>'u','ư'=>'u','ừ'=>'u','ứ'=>'u','ử'=>'u','ữ'=>'u','ự'=>'u',
        'ỳ'=>'y','ý'=>'y','ỷ'=>'y','ỹ'=>'y','ỵ'=>'y',
    ];
    $text = strtr($text, $charMap);
    $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

/**
 * Truncate text cleanly
 */
function truncate_text(string $text, int $limit = 140, string $end = '...'): string {
    $plain = strip_tags($text);
    if (mb_strlen($plain, 'UTF-8') <= $limit) {
        return $plain;
    }
    $truncated = mb_substr($plain, 0, $limit, 'UTF-8');
    $lastSpace = mb_strrpos($truncated, ' ', 0, 'UTF-8');
    if ($lastSpace !== false) {
        $truncated = mb_substr($truncated, 0, $lastSpace, 'UTF-8');
    }
    return $truncated . $end;
}

/**
 * Fetch a site setting by key
 */
function get_setting(string $key, string $default = ''): string {
    static $settingsCache = null;
    if ($settingsCache === null) {
        try {
            $db = Database::getConnection();
            $stmt = $db->query("SELECT `key_name`, `value` FROM `settings`");
            $rows = $stmt->fetchAll();
            $settingsCache = [];
            foreach ($rows as $row) {
                $settingsCache[$row['key_name']] = $row['value'];
            }
        } catch (Exception $e) {
            $settingsCache = [];
        }
    }
    return $settingsCache[$key] ?? $default;
}

/**
 * Sanitize rich text HTML (from CKEditor or markup)
 */
function sanitize_html(string $html): string {
    // 1. Remove dangerous executable tags
    $html = preg_replace('#<(script|style|iframe|object|embed|applet|form)[^>]*>.*?</\1>#is', '', $html);
    $html = preg_replace('#<(script|style|iframe|object|embed|applet|form)[^>]*>#is', '', $html);

    // 2. Remove all inline event handlers (onclick, onload, onerror, etc.)
    $html = preg_replace('#\s+on[a-zA-Z]+\s*=\s*(["\']).*?\1#is', '', $html);
    $html = preg_replace('#\s+on[a-zA-Z]+\s*=\s*[^>\s]+#is', '', $html);

    // 3. Prevent javascript: or vbscript: links
    $html = preg_replace('#(href|src)\s*=\s*(["\'])\s*(javascript|vbscript|data):.*?\2#is', '$1="#"', $html);

    return $html;
}

/**
 * Render structured technical article content with markdown support
 */
function render_article_content(string $content): string {
    // If it already contains HTML tags like <p>, <h3>, <code> from CKEditor
    if (strip_tags($content) !== $content) {
        return sanitize_html($content);
    }

    // Convert markdown headings, code blocks, lists
    $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $content));
    $html = '';
    $inCodeBlock = false;
    $inList = false;

    foreach ($lines as $line) {
        $trimmed = trim($line);

        // Code block toggle
        if (str_starts_with($trimmed, '```')) {
            if ($inCodeBlock) {
                $html .= "</code></pre>\n";
                $inCodeBlock = false;
            } else {
                $lang = trim(substr($trimmed, 3));
                $html .= '<pre class="code-block"><code class="language-' . e($lang) . '">';
                $inCodeBlock = true;
            }
            continue;
        }

        if ($inCodeBlock) {
            $html .= e($line) . "\n";
            continue;
        }

        // Unordered list
        if (preg_match('/^[-*]\s+(.+)$/', $trimmed, $m)) {
            if (!$inList) {
                $html .= "<ul class=\"article-list\">\n";
                $inList = true;
            }
            $html .= '<li>' . parse_inline_markdown($m[1]) . "</li>\n";
            continue;
        } else if ($inList) {
            $html .= "</ul>\n";
            $inList = false;
        }

        if ($trimmed === '') {
            continue;
        }

        // Headings
        if (preg_match('/^(#{1,4})\s+(.+)$/', $trimmed, $m)) {
            $level = strlen($m[1]);
            $html .= sprintf('<h%d id="%s">%s</h%d>', $level + 1, slugify($m[2]), parse_inline_markdown($m[2]), $level + 1) . "\n";
            continue;
        }

        // Blockquote
        if (str_starts_with($trimmed, '> ')) {
            $quote = substr($trimmed, 2);
            $html .= '<blockquote class="article-quote"><p>' . parse_inline_markdown($quote) . '</p></blockquote>' . "\n";
            continue;
        }

        // Paragraph
        $html .= '<p>' . parse_inline_markdown($trimmed) . "</p>\n";
    }

    if ($inCodeBlock) {
        $html .= "</code></pre>\n";
    }
    if ($inList) {
        $html .= "</ul>\n";
    }

    return $html;
}

function parse_inline_markdown(string $text): string {
    // Bold
    $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
    // Italic
    $text = preg_replace('/\*([^\*]+?)\*/', '<em>$1</em>', $text);
    // Inline code
    $text = preg_replace('/`([^`]+)`/', '<code class="inline-code">$1</code>', $text);
    return $text;
}
