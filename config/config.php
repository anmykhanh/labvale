<?php
/**
 * LabVale - Independent Technology R&D
 * Configuration File
 */

// Error reporting: E_ALL in development
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Database credentials
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'labvale_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Session security
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

// Auto-detect base URL for flexible hosting (works in root domain or subfolder)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));

// If executed from within the /admin subdirectory, strip /admin so base path always points to the app root
if (str_ends_with($script_dir, '/admin')) {
    $script_dir = substr($script_dir, 0, -6);
} elseif (preg_match('#^(.*?)/admin(/.*)?$#', $script_dir, $m)) {
    $script_dir = $m[1];
}

$base_path = rtrim($script_dir, '/');

// Base URL without trailing slash
define('APP_URL', $protocol . $host . $base_path);
define('BASE_PATH', $base_path);
define('ROOT_DIR', dirname(__DIR__));

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');
