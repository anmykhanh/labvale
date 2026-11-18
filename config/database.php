<?php
/**
 * LabVale - Database Connection
 * Uses PDO with Prepared Statements
 */

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // If database doesn't exist, try connecting to MySQL server to create it
                if ($e->getCode() === 1049) {
                    $serverDsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', DB_HOST, DB_PORT);
                    $serverPdo = new PDO($serverDsn, DB_USER, DB_PASS, $options);
                    $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
                } else {
                    die('Lỗi kết nối cơ sở dữ liệu: ' . htmlspecialchars($e->getMessage()));
                }
            }
        }
        return self::$instance;
    }
}
