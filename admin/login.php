<?php
/**
 * V-NEWS CMS - Đăng nhập Quản Trị Viên (Admin Login)
 * Compliant with AntiSlopUI Design Contract (DESIGN_SYSTEM.md)
 */
require_once dirname(__DIR__) . '/config/config.php';

// Nếu đã đăng nhập thì chuyển hướng ngay vào Dashboard
if (is_logged_in()) {
    header('Location: ' . url('admin/index.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Mã bảo mật CSRF không hợp lệ!';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $error = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
                $stmt->execute([$username, $username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    login_user($user);
                    set_flash('success', 'Chào mừng bạn quay trở lại, ' . $user['full_name'] . '!');
                    header('Location: ' . url('admin/index.php'));
                    exit;
                } else {
                    $error = 'Tên đăng nhập hoặc mật khẩu không chính xác!';
                }
            } catch (Exception $e) {
                $error = 'Lỗi hệ thống: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Quản Trị - V-NEWS CMS</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace']
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 antialiased">

    <div class="max-w-sm w-full bg-slate-900 border border-slate-800 rounded-lg p-7">
        
        <!-- Header & Logo -->
        <div class="text-center mb-6">
            <div class="w-10 h-10 rounded-md bg-slate-800 border border-slate-700 text-white flex items-center justify-center font-bold text-base mx-auto mb-3">
                V
            </div>
            <h1 class="text-base font-semibold text-white tracking-tight">Hệ Thống Quản Trị V-NEWS</h1>
            <p class="text-xs text-slate-400 mt-1">Đăng nhập tài khoản biên tập viên & quản trị</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 rounded-md bg-rose-950/40 border border-rose-800/60 text-rose-300 text-xs flex items-center gap-2">
            <i class="fas fa-circle-exclamation text-rose-400"></i>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <!-- Form Đăng nhập -->
        <form action="<?= url('admin/login.php') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Tên đăng nhập hoặc Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 text-xs">
                        <i class="far fa-user"></i>
                    </span>
                    <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? 'admin') ?>" required 
                           class="w-full pl-9 pr-3 py-2 rounded-md bg-slate-950 border border-slate-700 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-slate-500 transition-colors">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Mật khẩu</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 text-xs">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" value="admin123" required 
                           class="w-full pl-9 pr-3 py-2 rounded-md bg-slate-950 border border-slate-700 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-slate-500 transition-colors">
                </div>
            </div>

            <!-- Gợi ý tài khoản Demo -->
            <div class="p-3 rounded-md bg-slate-950 border border-slate-800 text-[11px] text-slate-400 space-y-1">
                <div class="font-medium text-slate-300">Tài khoản quản trị viên:</div>
                <div class="font-mono text-slate-400">
                    User: <span class="text-slate-200">admin</span> | Pass: <span class="text-slate-200">admin123</span>
                </div>
            </div>

            <button type="submit" class="w-full py-2.5 rounded-md bg-slate-100 hover:bg-white text-slate-900 font-semibold text-xs transition-colors duration-150">
                Đăng Nhập Quản Trị
            </button>
        </form>

        <div class="mt-5 text-center">
            <a href="<?= url() ?>" class="text-xs text-slate-400 hover:text-slate-200 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Trở về trang báo V-NEWS
            </a>
        </div>
    </div>

</body>
</html>