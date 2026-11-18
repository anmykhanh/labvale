<?php
/**
 * LabVale - Authentication & Security Management
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

class Auth {
    /**
     * Check if user is logged in as admin
     */
    public static function check(): bool {
        return !empty($_SESSION['admin_user']) && isset($_SESSION['admin_user']['id']);
    }

    /**
     * Require admin login or redirect to login page
     */
    public static function requireLogin(): void {
        if (!self::check()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? url('admin');
            header('Location: ' . url('admin/login'));
            exit;
        }

        // Inactivity timeout: 2 hours
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 7200)) {
            self::logout();
            flash('error', 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
            header('Location: ' . url('admin/login'));
            exit;
        }
        $_SESSION['last_activity'] = time();
    }

    /**
     * Attempt login
     */
    public static function attempt(string $username, string $password): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM `users` WHERE `username` = :user OR `email` = :email LIMIT 1");
        $stmt->execute([':user' => $username, ':email' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Prevent session fixation
            session_regenerate_id(true);

            $_SESSION['admin_user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'role' => $user['role']
            ];
            $_SESSION['last_activity'] = time();

            // Update last login
            $upStmt = $db->prepare("UPDATE `users` SET `last_login_at` = NOW() WHERE `id` = :id");
            $upStmt->execute([':id' => $user['id']]);

            return true;
        }

        return false;
    }

    /**
     * Current user
     */
    public static function user(): ?array {
        return $_SESSION['admin_user'] ?? null;
    }

    /**
     * Logout
     */
    public static function logout(): void {
        unset($_SESSION['admin_user'], $_SESSION['last_activity']);
    }
}
