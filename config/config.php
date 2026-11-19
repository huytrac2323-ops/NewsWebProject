<?php
/**
 * V-NEWS CMS - Cấu hình hệ thống
 * Thiết kế chuẩn SEO, dễ dàng triển khai với tên miền riêng (Custom Domain)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cấu hình múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Bật báo lỗi khi đang phát triển (Tắt trên production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Xác định đường dẫn thư mục gốc
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('UPLOAD_PATH', STORAGE_PATH . '/uploads');

// Tự động nhận diện Tên miền & Base URL (Chạy mượt trên cả localhost lẫn tên miền thật)
function get_base_url() {
    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    
    $protocol = $is_https ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Lấy thư mục con nếu chạy dưới dạng http://localhost/NewsWebProject
    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    // Bỏ /admin nếu đang ở trong thư mục admin
    $script_dir = preg_replace('#/admin.*$#', '', $script_dir);
    $base_dir = rtrim($script_dir, '/');
    
    return $protocol . $host . $base_dir;
}

define('BASE_URL', get_base_url());

// Hàm tạo URL tuyệt đối
function url($path = '') {
    $path = ltrim($path, '/');
    return rtrim(BASE_URL, '/') . ($path ? '/' . $path : '');
}

// Hàm lấy đường dẫn tài nguyên assets
function asset($path = '') {
    return url('assets/' . ltrim($path, '/'));
}

// Hàm lấy đường dẫn ảnh upload
function upload_url($filename = '') {
    if (empty($filename)) {
        return 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=800&auto=format&fit=crop&q=80';
    }
    // Nếu là URL bên ngoài (Unsplash / CDN)
    if (filter_var($filename, FILTER_VALIDATE_URL)) {
        return $filename;
    }
    return url('storage/uploads/' . ltrim($filename, '/'));
}

// Nạp các hàm trợ giúp
require_once CONFIG_PATH . '/functions.php';
require_once CONFIG_PATH . '/database.php';
require_once CONFIG_PATH . '/auth.php';
