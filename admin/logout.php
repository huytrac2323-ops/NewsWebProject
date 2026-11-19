<?php
/**
 * V-NEWS CMS - Đăng Xuất (Logout)
 */
require_once dirname(__DIR__) . '/config/config.php';

logout_user();
set_flash('info', 'Bạn đã đăng xuất an toàn khỏi hệ thống!');
header('Location: ' . url('admin/login.php'));
exit;
