<?php
/**
 * V-NEWS CMS - Hệ thống xác thực và phân quyền người dùng
 */

// Kiểm tra trạng thái đăng nhập
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Lấy thông tin người dùng hiện tại
function current_user() {
    if (!is_logged_in()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
        'full_name' => $_SESSION['full_name'] ?? 'Quản trị viên',
        'email' => $_SESSION['email'] ?? '',
        'role' => $_SESSION['role'] ?? 'guest',
        'avatar' => $_SESSION['avatar'] ?? ''
    ];
}

// Kiểm tra quyền Quản trị tối cao (Admin)
function is_admin() {
    return is_logged_in() && (($_SESSION['role'] ?? '') === 'admin');
}

// Kiểm tra quyền Biên tập viên hoặc Admin
function is_editor() {
    if (!is_logged_in()) return false;
    $role = $_SESSION['role'] ?? '';
    return in_array($role, ['admin', 'editor']);
}

// Bắt buộc đăng nhập (Redirect đến trang login nếu chưa đăng nhập)
function require_login() {
    if (!is_logged_in()) {
        set_flash('warning', 'Vui lòng đăng nhập để tiếp tục truy cập!');
        header('Location: ' . url('admin/login.php'));
        exit;
    }
}

// Bắt buộc quyền Quản trị viên
function require_admin() {
    require_login();
    if (!is_admin()) {
        set_flash('error', 'Bạn không có quyền quản trị cấp cao để thực hiện hành động này!');
        header('Location: ' . url('admin/index.php'));
        exit;
    }
}

// Đăng nhập người dùng vào phiên session
function login_user($user) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['avatar'] = $user['avatar'] ?? '';
}

// Đăng xuất phiên làm việc
function logout_user() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
