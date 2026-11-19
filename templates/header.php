<?php
/**
 * V-NEWS CMS - Editorial Header Template
 * Anti-AI-Slop Taste-First Design (NYT / The Verge / Editorial Journal)
 */

global $pdo;

// Lấy danh mục
$nav_categories = [];
try {
    $stmt = $pdo->query("
        SELECT c.*, COUNT(a.id) as articles_count 
        FROM categories c
        LEFT JOIN articles a ON c.id = a.category_id AND a.status = 'published'
        GROUP BY c.id
        ORDER BY c.sort_order ASC, c.id ASC
    ");
    $nav_categories = $stmt->fetchAll();
} catch (Exception $e) {
    $nav_categories = [];
}

// Lấy tin nóng (Breaking News)
$breaking_news = [];
try {
    $stmt = $pdo->query("
        SELECT id, title, slug 
        FROM articles 
        WHERE status = 'published' AND is_breaking = 1 
        ORDER BY id DESC LIMIT 5
    ");
    $breaking_news = $stmt->fetchAll();
    if (empty($breaking_news)) {
        $stmt = $pdo->query("
            SELECT id, title, slug 
            FROM articles 
            WHERE status = 'published' 
            ORDER BY id DESC LIMIT 5
        ");
        $breaking_news = $stmt->fetchAll();
    }
} catch (Exception $e) {
    $breaking_news = [];
}

$site_name = get_setting('site_name', 'V-NEWS CHRONICLE');
$site_tagline = get_setting('site_tagline', 'Thời sự • Công nghệ • Kinh doanh • Đời sống số');
$page_title = isset($page_title) ? $page_title . ' — ' . $site_name : $site_name . ' | ' . $site_tagline;
$page_desc = isset($page_desc) ? $page_desc : get_setting('site_description', 'Nhật báo thông tin và phân tích chuyên sâu.');
$page_thumb = isset($page_thumb) ? $page_thumb : upload_url();
$canonical_url = isset($canonical_url) ? $canonical_url : (isset($_SERVER['REQUEST_URI']) ? url($_SERVER['REQUEST_URI']) : url());
?>
<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
    <link rel="canonical" href="<?= htmlspecialchars($canonical_url) ?>">
    
    <!-- OpenGraph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
    <meta property="og:image" content="<?= htmlspecialchars($page_thumb) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonical_url) ?>">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        editorial: {
                            crimson: '#b91c1c',
                            navy: '#0f2942',
                            gold: '#b45309',
                            dark: '#0c0f14',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- FontAwesome & Custom CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="bg-[#fafaf9] dark:bg-[#0c0f14] text-neutral-900 dark:text-neutral-100 min-h-screen flex flex-col antialiased transition-colors duration-200">

    <!-- TOP HEADER: DATE, EDITION, QUICK LINKS -->
    <div class="border-b border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#0c0f14] text-[11px] font-semibold text-neutral-500 dark:text-neutral-400 py-1.5 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-2">
            <div class="flex items-center space-x-2 sm:space-x-3 divide-x divide-neutral-200 dark:divide-neutral-800 min-w-0">
                <span class="tracking-wide uppercase text-neutral-700 dark:text-neutral-300 truncate">
                    <i class="far fa-calendar-alt mr-1 text-red-700"></i> <?= format_date_vn(time(), false) ?>
                </span>
                <span class="pl-2.5 hidden sm:inline tracking-widest uppercase text-[10px] text-neutral-400 whitespace-nowrap">
                    Ấn Bản Điện Tử 24/7
                </span>
            </div>

            <!-- Right Controls -->
            <div class="flex items-center space-x-2.5 sm:space-x-4 flex-shrink-0 text-xs">
                <a href="<?= url('rss.xml.php') ?>" class="hover:text-red-700 transition-colors flex items-center">
                    <i class="fas fa-rss mr-1 text-amber-600"></i> <span class="hidden sm:inline">RSS</span>
                </a>
                <span class="text-neutral-300 dark:text-neutral-700">•</span>
                <!-- Nút Dark Mode -->
                <button class="theme-toggle-btn hover:text-neutral-900 dark:hover:text-white transition-colors flex items-center space-x-1" title="Chuyển chế độ đọc">
                    <i class="fas fa-moon hidden dark:inline-block text-amber-300"></i>
                    <i class="fas fa-sun inline-block dark:hidden text-amber-600"></i>
                    <span class="hidden sm:inline">Chế độ đọc</span>
                </button>
                <span class="text-neutral-300 dark:text-neutral-700">•</span>
                <?php if (is_logged_in()): ?>
                    <a href="<?= url('admin/index.php') ?>" class="text-red-700 dark:text-red-400 font-bold flex items-center">
                        <i class="fas fa-shield-alt mr-1"></i> <span class="hidden sm:inline">Tòa Soạn</span> CMS
                    </a>
                <?php else: ?>
                    <a href="<?= url('admin/login.php') ?>" class="hover:text-neutral-900 dark:hover:text-white flex items-center">
                        <i class="fas fa-lock mr-1 text-neutral-400"></i> Đăng nhập
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- MAIN MASTHEAD (ĐẦU BÁO TRUYỀN THỐNG CAO CẤP) -->
    <header class="bg-white dark:bg-[#0c0f14] border-b border-neutral-300 dark:border-neutral-800 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                
                <!-- Left: Slogan / Sứ mệnh -->
                <div class="hidden md:block w-1/4 text-xs text-neutral-500 dark:text-neutral-400 leading-relaxed font-editorial italic">
                    "Tiếng nói của sự thật, phân tích công nghệ đa chiều & góc nhìn thời đại số."
                </div>

                <!-- Center: Newspaper Masthead Logo -->
                <div class="text-center px-2 w-full md:w-auto">
                    <a href="<?= url() ?>" class="inline-block group max-w-full">
                        <h1 class="font-headline text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-neutral-900 dark:text-white leading-none group-hover:opacity-90 transition-opacity">
                            THE V-NEWS
                        </h1>
                        <div class="flex items-center justify-center space-x-2 mt-2 max-w-full overflow-hidden">
                            <span class="hidden sm:inline-block h-[1px] w-8 bg-neutral-300 dark:bg-neutral-700"></span>
                            <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider text-neutral-500 dark:text-neutral-400 line-clamp-1">
                                <?= htmlspecialchars(get_setting('site_tagline', 'Thời sự • Công nghệ • Kinh doanh • Đời sống')) ?>
                            </span>
                            <span class="hidden sm:inline-block h-[1px] w-8 bg-neutral-300 dark:bg-neutral-700"></span>
                        </div>
                    </a>
                </div>

                <!-- Right: Search & Action -->
                <div class="w-full md:w-1/4 flex items-center justify-center md:justify-end space-x-3">
                    <form action="<?= url('search.php') ?>" method="GET" class="relative w-full max-w-xs sm:max-w-[220px]">
                        <input type="text" name="q" placeholder="Tìm kiếm tin bài..." 
                               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                               class="w-full pl-8 pr-3 py-1.5 rounded-none bg-neutral-50 dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 text-xs border border-neutral-300 dark:border-neutral-700 focus:outline-none focus:border-neutral-900 dark:focus:border-neutral-300">
                        <i class="fas fa-search absolute left-2.5 top-2 text-neutral-400 text-xs"></i>
                    </form>
                    <a href="<?= url('admin/index.php') ?>" class="hidden lg:inline-flex text-[11px] font-bold uppercase tracking-wider px-3 py-1.5 border border-neutral-900 dark:border-neutral-200 text-neutral-900 dark:text-white hover:bg-neutral-900 hover:text-white dark:hover:bg-white dark:hover:text-neutral-900 transition-colors whitespace-nowrap">
                        Quản Trị
                    </a>
                </div>
            </div>
        </div>

        <!-- CATEGORY NAVIGATION (THANH ĐIỀU HƯỚNG BÁO CHÍ) -->
        <div class="border-t-2 border-b border-neutral-900 dark:border-neutral-700 bg-white dark:bg-[#0c0f14] overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center space-x-4 sm:space-x-6 overflow-x-auto no-scrollbar py-2.5 text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                    <a href="<?= url() ?>" class="py-1 text-neutral-900 dark:text-white hover:text-red-700 dark:hover:text-red-400 transition-colors <?= empty($_GET['slug']) && basename($_SERVER['PHP_SELF']) === 'index.php' ? 'text-red-700 dark:text-red-400 border-b-2 border-red-700 pb-0.5' : '' ?>">
                        Trang Chủ
                    </a>

                    <?php foreach ($nav_categories as $cat): ?>
                    <a href="<?= url('category.php?slug=' . urlencode($cat['slug'])) ?>" 
                       class="py-1 text-neutral-700 dark:text-neutral-300 hover:text-red-700 dark:hover:text-red-400 transition-colors <?= (isset($_GET['slug']) && $_GET['slug'] === $cat['slug']) ? 'text-red-700 dark:text-red-400 border-b-2 border-red-700 pb-0.5' : '' ?>">
                        <?= htmlspecialchars($cat['name']) ?>
                    </a>
                    <?php endforeach; ?>

                    <a href="<?= url('contact.php') ?>" class="py-1 text-neutral-500 hover:text-neutral-900 dark:hover:text-white transition-colors">
                        Tòa Soạn
                    </a>

                    <!-- Domain badge -->
                    <div class="hidden xl:inline-flex items-center space-x-2 text-[10px] text-neutral-400 pl-4 border-l border-neutral-200 dark:border-neutral-800">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>TRỰC TUYẾN 24/7</span>
                    </div>
                </nav>
            </div>
        </div>

        <!-- BREAKING NEWS TICKER (TIN NÓNG ĐƯỜNG CHỈ) -->
        <?php if (!empty($breaking_news)): ?>
        <div class="bg-neutral-100 dark:bg-neutral-900/60 border-b border-neutral-200 dark:border-neutral-800 text-xs py-1.5 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center space-x-3">
                <span class="flex-shrink-0 text-[10px] font-black uppercase tracking-widest px-2 py-0.5 bg-red-700 text-white">
                    Tin Mới
                </span>
                <div class="ticker-wrap flex-1 min-w-0 overflow-hidden">
                    <div class="ticker-track flex flex-col">
                        <?php foreach ($breaking_news as $bn): ?>
                        <a href="<?= url('article.php?slug=' . urlencode($bn['slug'])) ?>" class="h-6 flex items-center text-neutral-700 dark:text-neutral-300 hover:text-red-700 dark:hover:text-red-400 font-medium truncate block">
                            <?= htmlspecialchars($bn['title']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </header>

    <!-- MAIN BODY CONTAINER -->
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <?= display_flash() ?>
