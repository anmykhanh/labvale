<?php
/**
 * LabVale Admin - Đăng xuất
 */
require_once __DIR__ . '/../includes/auth.php';
Auth::logout();
flash('success', 'Bạn đã đăng xuất an toàn khỏi hệ thống quản trị.');
header('Location: ' . url('/admin/login'));
exit;
