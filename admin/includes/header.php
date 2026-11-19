<?php
/**
 * V-NEWS CMS - Admin Header & Shell
 * Compliant with AntiSlopUI Design Contract (DESIGN_SYSTEM.md)
 * Surface Profile: §B2 Dashboard / Admin / Precision Command Center
 */
require_once dirname(__DIR__, 2) . '/config/config.php';
require_login();

$user = current_user();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - ' : '' ?>Bảng Quản Trị V-NEWS CMS</title>
    
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Tailwind CSS with Precision Tokens -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace']
                    },
                    colors: {
                        slate: {
                            50: '#F8FAFC',
                            100: '#F1F5F9',
                            200: '#E2E8F0',
                            300: '#CBD5E1',
                            400: '#94A3B8',
                            500: '#64748B',
                            600: '#475569',
                            700: '#334155',
                            800: '#1E293B',
                            900: '#0F172A',
                            950: '#090D16'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .tabular-nums { font-variant-numeric: tabular-nums; }
        .cmd-item:hover, .cmd-item.active { background-color: rgba(241, 245, 249, 1); }
        .dark .cmd-item:hover, .dark .cmd-item.active { background-color: rgba(30, 41, 59, 1); }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 min-h-screen flex antialiased">

    <!-- SIDEBAR BÊN TRÁI (Fixed Editorial Rail) -->
    <aside id="admin-sidebar" class="w-64 bg-slate-900 text-slate-300 flex-shrink-0 flex flex-col justify-between hidden md:flex border-r border-slate-800 z-40 h-screen sticky top-0 transition-transform duration-200">
        <div class="flex flex-col h-full">
            
            <!-- Brand Header -->
            <div class="h-14 px-5 border-b border-slate-800/80 flex items-center justify-between">
                <a href="<?= url('admin/index.php') ?>" class="flex items-center space-x-2.5 group">
                    <div class="w-7 h-7 rounded-md bg-slate-800 text-slate-100 flex items-center justify-center font-bold text-xs border border-slate-700 group-hover:border-slate-500 transition-colors">
                        V
                    </div>
                    <div class="flex items-baseline space-x-1.5">
                        <span class="text-sm font-semibold tracking-tight text-white">V-NEWS</span>
                        <span class="text-[10px] font-mono uppercase px-1 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700/60 leading-none">CMS</span>
                    </div>
                </a>
                <button id="close-sidebar-btn" class="md:hidden text-slate-400 hover:text-white p-1 rounded-md" aria-label="Đóng menu">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
                
                <!-- Nhóm 1: Tổng Quan -->
                <div>
                    <div class="px-2.5 pb-2 text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                        Tổng Quan
                    </div>
                    <nav class="space-y-1">
                        <a href="<?= url('admin/index.php') ?>" class="flex items-center gap-2.5 px-2.5 py-2 rounded-md text-xs font-medium transition-colors duration-150 <?= $current_page === 'index.php' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' ?>">
                            <i class="fas fa-chart-pie w-4 text-center text-slate-400"></i>
                            <span>Bảng Điều Khiển</span>
                        </a>
                    </nav>
                </div>

                <!-- Nhóm 2: Nội Dung & Biên Tập -->
                <div>
                    <div class="px-2.5 pb-2 text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                        Biên Tập Tin Bài
                    </div>
                    <nav class="space-y-1">
                        <a href="<?= url('admin/articles.php') ?>" class="flex items-center gap-2.5 px-2.5 py-2 rounded-md text-xs font-medium transition-colors duration-150 <?= $current_page === 'articles.php' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' ?>">
                            <i class="fas fa-newspaper w-4 text-center text-slate-400"></i>
                            <span>Tất Cả Bài Viết</span>
                        </a>

                        <a href="<?= url('admin/article_edit.php') ?>" class="flex items-center gap-2.5 px-2.5 py-2 rounded-md text-xs font-medium transition-colors duration-150 <?= $current_page === 'article_edit.php' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' ?>">
                            <i class="fas fa-pen-to-square w-4 text-center text-slate-400"></i>
                            <span>Viết Bài Mới</span>
                        </a>

                        <a href="<?= url('admin/categories.php') ?>" class="flex items-center gap-2.5 px-2.5 py-2 rounded-md text-xs font-medium transition-colors duration-150 <?= $current_page === 'categories.php' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' ?>">
                            <i class="fas fa-folder-tree w-4 text-center text-slate-400"></i>
                            <span>Chuyên Mục</span>
                        </a>

                        <a href="<?= url('admin/comments.php') ?>" class="flex items-center gap-2.5 px-2.5 py-2 rounded-md text-xs font-medium transition-colors duration-150 <?= $current_page === 'comments.php' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' ?>">
                            <i class="fas fa-comments w-4 text-center text-slate-400"></i>
                            <span>Bình Luận Độc Giả</span>
                        </a>
                    </nav>
                </div>

                <!-- Nhóm 3: Hệ Thống -->
                <?php if (is_admin()): ?>
                <div>
                    <div class="px-2.5 pb-2 text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                        Quản Trị Hệ Thống
                    </div>
                    <nav class="space-y-1">
                        <a href="<?= url('admin/users.php') ?>" class="flex items-center gap-2.5 px-2.5 py-2 rounded-md text-xs font-medium transition-colors duration-150 <?= $current_page === 'users.php' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' ?>">
                            <i class="fas fa-users-gear w-4 text-center text-slate-400"></i>
                            <span>Tài Khoản Quản Trị</span>
                        </a>

                        <a href="<?= url('admin/settings.php') ?>" class="flex items-center gap-2.5 px-2.5 py-2 rounded-md text-xs font-medium transition-colors duration-150 <?= $current_page === 'settings.php' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' ?>">
                            <i class="fas fa-sliders w-4 text-center text-slate-400"></i>
                            <span>Cài Đặt Website</span>
                        </a>
                    </nav>
                </div>
                <?php endif; ?>

                <!-- Nhóm 4: Hạ Tầng & Tên Miền -->
                <div>
                    <div class="px-2.5 pb-2 text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                        Hạ Tầng Xuất Bản
                    </div>
                    <nav class="space-y-1">
                        <a href="<?= url('admin/domain_guide.php') ?>" class="flex items-center justify-between px-2.5 py-2 rounded-md text-xs font-medium transition-colors duration-150 <?= $current_page === 'domain_guide.php' ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60' ?>">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <i class="fas fa-globe w-4 text-center text-slate-400"></i>
                                <span class="truncate">Gắn Tên Miền Riêng</span>
                            </div>
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        </a>
                    </nav>
                </div>

            </div>

            <!-- Sidebar User Profile Footer -->
            <div class="p-3 border-t border-slate-800/80 bg-slate-950/40">
                <div class="flex items-center justify-between p-2 rounded-md bg-slate-900 border border-slate-800">
                    <div class="flex items-center space-x-2.5 min-w-0">
                        <div class="w-7 h-7 rounded-md bg-slate-800 text-slate-200 flex items-center justify-center font-bold text-xs border border-slate-700 flex-shrink-0">
                            <?= mb_substr($user['full_name'], 0, 1) ?>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-xs font-medium text-white truncate"><?= htmlspecialchars($user['full_name']) ?></span>
                            <span class="block text-[10px] text-slate-400 capitalize font-mono"><?= htmlspecialchars($user['role']) ?></span>
                        </div>
                    </div>
                    <a href="<?= url('admin/logout.php') ?>" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-md hover:bg-slate-800 transition-colors" title="Đăng xuất">
                        <i class="fas fa-arrow-right-from-bracket text-xs"></i>
                    </a>
                </div>
            </div>

        </div>
    </aside>

    <!-- CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <!-- TOPBAR (Strict 56px / h-14, 1px Hairline Border, Zero Elevation Clutter) -->
        <header class="h-14 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 md:px-8 flex items-center justify-between sticky top-0 z-30">
            
            <div class="flex items-center space-x-3">
                <!-- Mobile Menu Button -->
                <button id="mobile-toggle-btn" class="md:hidden p-2 rounded-md text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Mở menu">
                    <i class="fas fa-bars"></i>
                </button>
                
                <!-- Clean Breadcrumbs -->
                <nav class="flex items-center space-x-2 text-xs" aria-label="Breadcrumb">
                    <span class="text-slate-400">Quản Trị</span>
                    <i class="fas fa-chevron-right text-[9px] text-slate-300 dark:text-slate-600"></i>
                    <span class="font-medium text-slate-900 dark:text-white"><?= htmlspecialchars($page_title ?? 'Bảng Điều Khiển') ?></span>
                </nav>
            </div>

            <!-- Right Tools: Command Palette, Live Status, Site Link, Theme Toggle -->
            <div class="flex items-center space-x-2.5 sm:space-x-3">
                
                <!-- Command Palette Shortcut Button (Linear Style) -->
                <button id="open-cmd-palette-btn" class="hidden sm:inline-flex items-center gap-2 px-2.5 py-1.5 rounded-md border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 text-xs text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors duration-150" title="Mở thanh tìm kiếm lệnh nhanh">
                    <i class="fas fa-magnifying-glass text-[10px]"></i>
                    <span class="text-[11px] font-medium">Tìm nhanh</span>
                    <kbd class="font-mono text-[9px] bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 px-1 py-0.5 rounded text-slate-400">Ctrl K</kbd>
                </button>

                <!-- Live newsroom status dot -->
                <div class="hidden lg:inline-flex items-center gap-2 px-2.5 py-1 rounded-md border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/40 text-[11px] font-mono text-slate-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>ONLINE</span>
                </div>

                <!-- Xem website -->
                <a href="<?= url() ?>" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1.5 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60 transition-colors duration-150">
                    <i class="fas fa-arrow-up-right-from-square text-[10px] text-slate-500 dark:text-slate-400"></i>
                    <span class="hidden sm:inline">Xem Website</span>
                </a>

                <!-- Theme Toggle Button -->
                <button class="theme-toggle-btn p-2 rounded-md text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors duration-150" aria-label="Đổi giao diện Sáng/Tối">
                    <i class="fas fa-moon hidden dark:inline-block text-amber-300 text-xs"></i>
                    <i class="fas fa-sun inline-block dark:hidden text-amber-500 text-xs"></i>
                </button>
            </div>
        </header>

        <!-- COMMAND PALETTE MODAL (Linear Style) -->
        <div id="cmd-palette-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-start justify-center pt-20 px-4">
            <div id="cmd-palette-dialog" class="w-full max-w-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg shadow-xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                <div class="p-3 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3">
                    <i class="fas fa-search text-slate-400 text-xs pl-1"></i>
                    <input type="text" id="cmd-palette-input" placeholder="Tìm tác vụ hoặc trang quản trị..." 
                           class="w-full bg-transparent text-xs text-slate-900 dark:text-white focus:outline-none placeholder-slate-400">
                    <kbd class="text-[10px] font-mono text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded border border-slate-200 dark:border-slate-700">ESC</kbd>
                </div>
                <div id="cmd-palette-list" class="max-h-64 overflow-y-auto p-2 space-y-1 text-xs">
                    <a href="<?= url('admin/article_edit.php') ?>" class="cmd-item flex items-center justify-between p-2 rounded-md text-slate-700 dark:text-slate-200 transition-colors">
                        <span class="flex items-center gap-2.5">
                            <i class="fas fa-pen-to-square w-4 text-center text-slate-400"></i>
                            <span>Soạn thảo bài viết mới</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">Tác vụ</span>
                    </a>
                    <a href="<?= url('admin/articles.php') ?>" class="cmd-item flex items-center justify-between p-2 rounded-md text-slate-700 dark:text-slate-200 transition-colors">
                        <span class="flex items-center gap-2.5">
                            <i class="fas fa-newspaper w-4 text-center text-slate-400"></i>
                            <span>Quản lý tất cả bài viết</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">Trang</span>
                    </a>
                    <a href="<?= url('admin/categories.php') ?>" class="cmd-item flex items-center justify-between p-2 rounded-md text-slate-700 dark:text-slate-200 transition-colors">
                        <span class="flex items-center gap-2.5">
                            <i class="fas fa-folder-tree w-4 text-center text-slate-400"></i>
                            <span>Quản lý chuyên mục tin tức</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">Trang</span>
                    </a>
                    <a href="<?= url('admin/comments.php') ?>" class="cmd-item flex items-center justify-between p-2 rounded-md text-slate-700 dark:text-slate-200 transition-colors">
                        <span class="flex items-center gap-2.5">
                            <i class="fas fa-comments w-4 text-center text-slate-400"></i>
                            <span>Kiểm duyệt bình luận độc giả</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">Trang</span>
                    </a>
                    <a href="<?= url('admin/domain_guide.php') ?>" class="cmd-item flex items-center justify-between p-2 rounded-md text-slate-700 dark:text-slate-200 transition-colors">
                        <span class="flex items-center gap-2.5">
                            <i class="fas fa-globe w-4 text-center text-slate-400"></i>
                            <span>Tài liệu trỏ DNS & Tên miền riêng</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">Hạ tầng</span>
                    </a>
                    <a href="<?= url('admin/settings.php') ?>" class="cmd-item flex items-center justify-between p-2 rounded-md text-slate-700 dark:text-slate-200 transition-colors">
                        <span class="flex items-center gap-2.5">
                            <i class="fas fa-sliders w-4 text-center text-slate-400"></i>
                            <span>Cài đặt tiêu đề & Logo website</span>
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">Hệ thống</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT CANVAS -->
        <main class="p-6 md:p-8 flex-1 max-w-7xl w-full mx-auto">
            <?= display_flash() ?>