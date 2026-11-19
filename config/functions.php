<?php
/**
 * V-NEWS CMS - Các hàm tiện ích hỗ trợ toàn hệ thống
 */

// Chuyển tiếng Việt có dấu thành Slug chuẩn SEO URL
function slugify($text) {
    // Chuyển chữ hoa thành thường
    $text = mb_strtolower($text, 'UTF-8');
    
    // Bảng mã chuyển đổi ký tự tiếng Việt
    $unicode = [
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd' => 'đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
    ];
    
    foreach ($unicode as $nonAccent => $accent) {
        $text = preg_replace("/($accent)/i", $nonAccent, $text);
    }
    
    // Loại bỏ ký tự đặc biệt, chỉ giữ lại chữ, số và dấu gạch nối
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    // Thay thế khoảng trắng hoặc nhiều gạch nối liên tiếp thành 1 gạch nối
    $text = preg_replace('/[\s-]+/', '-', $text);
    // Cắt bỏ gạch nối ở đầu và cuối
    $text = trim($text, '-');
    
    return $text ?: 'post-' . time();
}

// Làm sạch dữ liệu tránh XSS
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

// Cắt ngắn văn bản theo số từ
function truncate_words($text, $limit = 25, $ellipsis = '...') {
    $text = strip_tags($text);
    $words = explode(' ', $text);
    if (count($words) > $limit) {
        return implode(' ', array_slice($words, 0, $limit)) . $ellipsis;
    }
    return $text;
}

// Hiển thị thời gian thân thiện (VD: 5 phút trước, 2 giờ trước)
function time_ago($datetime) {
    $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
    $difference = time() - $timestamp;
    
    if ($difference < 60) {
        return 'Vừa xong';
    } elseif ($difference < 3600) {
        return floor($difference / 60) . ' phút trước';
    } elseif ($difference < 86400) {
        return floor($difference / 3600) . ' giờ trước';
    } elseif ($difference < 604800) {
        return floor($difference / 86400) . ' ngày trước';
    } else {
        return date('d/m/Y', $timestamp);
    }
}

// Định dạng ngày tháng tiếng Việt
function format_date_vn($datetime, $show_time = true) {
    $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
    $days = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
    $day_name = $days[date('w', $timestamp)];
    
    if ($show_time) {
        return $day_name . ', ' . date('d/m/Y - H:i', $timestamp);
    }
    return $day_name . ', ' . date('d/m/Y', $timestamp);
}

// Ước tính thời gian đọc bài viết
function reading_time($content) {
    $word_count = str_word_count(strip_tags($content));
    $minutes = ceil($word_count / 200); // Tốc độ đọc trung bình 200 từ/phút
    return max(1, $minutes) . ' phút đọc';
}

// CSRF Token Protection
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Flash Message (Thông báo chuyển trang)
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Hiển thị thông báo flash dạng HTML
function display_flash() {
    $flash = get_flash();
    if (!$flash) return '';
    
    $colorMap = [
        'success' => 'bg-emerald-50 text-emerald-800 border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
        'error' => 'bg-rose-50 text-rose-800 border-rose-300 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800',
        'warning' => 'bg-amber-50 text-amber-800 border-amber-300 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
        'info' => 'bg-sky-50 text-sky-800 border-sky-300 dark:bg-sky-950/60 dark:text-sky-300 dark:border-sky-800',
    ];
    
    $classes = $colorMap[$flash['type']] ?? $colorMap['info'];
    
    return '
    <div class="mb-6 p-4 rounded-xl border flex items-center justify-between ' . $classes . ' transition-all shadow-sm" role="alert">
        <div class="flex items-center space-x-3">
            <span class="font-medium text-sm">' . htmlspecialchars($flash['message']) . '</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-sm opacity-60 hover:opacity-100">&times;</button>
    </div>';
}

// Lấy cài đặt hệ thống từ Database
function get_setting($key, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return ($val !== false && $val !== null) ? $val : $default;
    } catch (Exception $e) {
        return $default;
    }
}

// Cập nhật cài đặt hệ thống
function update_setting($key, $value) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                               ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value");
        // Hỗ trợ cả cú pháp SQLite và MySQL
        $is_mysql = ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql');
        if ($is_mysql) {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                                   ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        }
        return $stmt->execute([$key, $value]);
    } catch (Exception $e) {
        return false;
    }
}
