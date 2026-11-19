<?php
/**
 * V-NEWS CMS - Bảng Điều Khiển Quản Trị (Dashboard)
 * Compliant with AntiSlopUI Design Contract (DESIGN_SYSTEM.md)
 * Surface Profile: §B2 Dashboard / Admin / Precision Command Center
 */
$page_title = 'Bảng Điều Khiển';
require_once __DIR__ . '/includes/header.php';

// 1. Thống kê tổng quan
$stats = [
    'articles'   => 0,
    'published'  => 0,
    'drafts'     => 0,
    'categories' => 0,
    'comments'   => 0,
    'views'      => 0
];

try {
    $stats['articles']   = (int)$pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    $stats['published']  = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'published'")->fetchColumn();
    $stats['drafts']     = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status != 'published'")->fetchColumn();
    $stats['categories'] = (int)$pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    $stats['comments']   = (int)$pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
    $stats['views']      = (int)$pdo->query("SELECT COALESCE(SUM(views_count), 0) FROM articles")->fetchColumn();
} catch (Exception $e) {}

// 2. Thống kê xuất bản 7 ngày qua (Pure PHP Compatible across SQLite & MySQL)
$seven_days = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $seven_days[$d] = [
        'label' => date('d/m', strtotime("-$i days")),
        'day_name' => date('D', strtotime("-$i days")),
        'count' => 0
    ];
}

try {
    $recent_dates = $pdo->query("SELECT created_at FROM articles WHERE status = 'published'")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($recent_dates as $dt) {
        $d = date('Y-m-d', strtotime($dt));
        if (isset($seven_days[$d])) {
            $seven_days[$d]['count']++;
        }
    }
} catch (Exception $e) {}

// Chuẩn bị tọa độ cho biểu đồ SVG Area Chart (viewBox: 0 0 500 100)
$max_val = max(1, max(array_column($seven_days, 'count')));
$chart_points = [];
$idx = 0;
$total_days = count($seven_days);
foreach ($seven_days as $item) {
    $x = 25 + $idx * (450 / max(1, $total_days - 1));
    $y = 85 - ($item['count'] / $max_val) * 65;
    $chart_points[] = ['x' => $x, 'y' => $y, 'label' => $item['label'], 'count' => $item['count']];
    $idx++;
}

$svg_line_path = '';
$svg_area_path = '';
if (!empty($chart_points)) {
    $svg_line_path = "M " . $chart_points[0]['x'] . " " . $chart_points[0]['y'];
    for ($i = 1; $i < count($chart_points); $i++) {
        $svg_line_path .= " L " . $chart_points[$i]['x'] . " " . $chart_points[$i]['y'];
    }
    $last_x = $chart_points[count($chart_points) - 1]['x'];
    $first_x = $chart_points[0]['x'];
    $svg_area_path = $svg_line_path . " L " . $last_x . " 95 L " . $first_x . " 95 Z";
}

// 3. Lấy 8 bài viết mới nhất
$recent_articles = [];
try {
    $stmt = $pdo->query("
        SELECT a.*, c.name as category_name, c.color as category_color, u.full_name as author_name
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        LEFT JOIN users u ON a.author_id = u.id
        ORDER BY a.id DESC LIMIT 8
    ");
    $recent_articles = $stmt->fetchAll();
} catch (Exception $e) {}

// 4. Lấy 4 bình luận mới nhất
$recent_comments = [];
try {
    $stmt = $pdo->query("
        SELECT c.*, a.title as article_title, a.slug as article_slug
        FROM comments c
        LEFT JOIN articles a ON c.article_id = a.id
        ORDER BY c.id DESC LIMIT 4
    ");
    $recent_comments = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- TIÊU ĐỀ TRANG & NÚT THAO TÁC CHÍNH -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-5 border-b border-slate-200 dark:border-slate-800">
    <div>
        <div class="flex items-center gap-2">
            <h1 class="text-lg font-semibold tracking-tight text-slate-900 dark:text-white">
                Tổng Quan Hệ Thống Xuất Bản
            </h1>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 ring-1 ring-inset ring-emerald-600/20">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>Active</span>
            </span>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Trung tâm kiểm soát xuất bản tin bài và theo dõi độc giả thời gian thực.
        </p>
    </div>
    
    <div class="flex items-center space-x-2.5">
        <a href="<?= url('admin/articles.php') ?>" class="px-3 py-2 text-xs font-medium rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60 transition-colors duration-150">
            Quản Lý Bài Viết
        </a>
        <a href="<?= url('admin/article_edit.php') ?>" class="px-3.5 py-2 text-xs font-medium rounded-md bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 transition-colors duration-150 inline-flex items-center gap-1.5">
            <i class="fas fa-plus text-[11px]"></i>
            <span>Viết Bài Mới</span>
        </a>
    </div>
</div>

<!-- ROW 1: 4 KPI STATS CARDS (Inverted Pyramid + Micro Sparklines) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    
    <!-- KPI 1: Bài Viết -->
    <div class="p-5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 transition-colors duration-150 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Bài Viết Đã Đăng</span>
                <div class="w-7 h-7 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center text-xs">
                    <i class="fas fa-newspaper"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl font-semibold tabular-nums text-slate-900 dark:text-white tracking-tight">
                    <?= number_format($stats['articles']) ?>
                </div>
                <!-- Micro Sparkline SVG -->
                <svg class="w-16 h-6 text-emerald-600 dark:text-emerald-400" viewBox="0 0 64 24" fill="none" aria-hidden="true">
                    <path d="M 2 18 L 16 16 L 30 19 L 44 11 L 62 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
            <span class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 ring-1 ring-inset ring-emerald-600/20 tabular-nums">
                <?= $stats['published'] ?> xuất bản
            </span>
            <span class="text-slate-400 tabular-nums"><?= $stats['drafts'] ?> bản nháp</span>
        </div>
    </div>

    <!-- KPI 2: Lượt Đọc -->
    <div class="p-5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 transition-colors duration-150 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Tổng Lượt Đọc Bài</span>
                <div class="w-7 h-7 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center text-xs">
                    <i class="fas fa-eye"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl font-semibold tabular-nums text-slate-900 dark:text-white tracking-tight">
                    <?= number_format($stats['views']) ?>
                </div>
                <!-- Micro Sparkline SVG -->
                <svg class="w-16 h-6 text-slate-600 dark:text-slate-400" viewBox="0 0 64 24" fill="none" aria-hidden="true">
                    <path d="M 2 20 L 16 15 L 30 17 L 44 8 L 62 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
            <span class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 ring-1 ring-inset ring-slate-300/30 tabular-nums">
                <i class="fas fa-chart-simple text-[9px]"></i> Tương tác
            </span>
            <span class="text-slate-400 tabular-nums">~<?= $stats['articles'] > 0 ? round($stats['views'] / $stats['articles']) : 0 ?> lượt/bài</span>
        </div>
    </div>

    <!-- KPI 3: Chuyên Mục -->
    <div class="p-5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 transition-colors duration-150 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Chuyên Mục Tin Tức</span>
                <div class="w-7 h-7 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center text-xs">
                    <i class="fas fa-folder-tree"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl font-semibold tabular-nums text-slate-900 dark:text-white tracking-tight">
                    <?= number_format($stats['categories']) ?>
                </div>
                <!-- Micro Sparkline SVG -->
                <svg class="w-16 h-6 text-slate-400" viewBox="0 0 64 24" fill="none" aria-hidden="true">
                    <path d="M 2 12 L 18 12 L 34 8 L 48 8 L 62 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
            <span class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 ring-1 ring-inset ring-slate-300/30 tabular-nums">
                <?= $stats['categories'] ?> danh mục
            </span>
            <span class="text-slate-400">phân loại nội dung</span>
        </div>
    </div>

    <!-- KPI 4: Ý Kiến Bạn Đọc -->
    <div class="p-5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 transition-colors duration-150 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Ý Kiến Bạn Đọc</span>
                <div class="w-7 h-7 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center text-xs">
                    <i class="fas fa-comments"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl font-semibold tabular-nums text-slate-900 dark:text-white tracking-tight">
                    <?= number_format($stats['comments']) ?>
                </div>
                <!-- Micro Sparkline SVG -->
                <svg class="w-16 h-6 text-amber-500 dark:text-amber-400" viewBox="0 0 64 24" fill="none" aria-hidden="true">
                    <path d="M 2 19 L 16 19 L 30 14 L 46 14 L 62 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
            <span class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 font-medium bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 ring-1 ring-inset ring-amber-600/20 tabular-nums">
                <?= $stats['comments'] ?> phản hồi
            </span>
            <span class="text-slate-400">tương tác độc giả</span>
        </div>
    </div>

</div>

<!-- ROW 2: PUBLICATION VELOCITY 7-DAY CHART (Pure SVG Precision Widget - §B2 Compliant) -->
<div class="mb-6 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-4 mb-3 border-b border-slate-100 dark:border-slate-800">
        <div>
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-chart-line text-slate-500 text-xs"></i>
                <span>Tần Suất Xuất Bản Tin Bài 7 Ngày Qua</span>
            </h2>
            <p class="text-[11px] text-slate-400 mt-0.5">
                Biểu đồ đo lường tốc độ cập nhật thông tin và nhịp độ làm việc của ban biên tập
            </p>
        </div>
        <div class="flex items-center gap-4 text-xs font-mono">
            <span class="text-slate-500">Đỉnh điểm: <strong class="text-slate-900 dark:text-white tabular-nums"><?= $max_val ?> bài/ngày</strong></span>
            <span class="text-slate-300 dark:text-slate-700">|</span>
            <span class="text-slate-500">Tuần này: <strong class="text-slate-900 dark:text-white tabular-nums"><?= array_sum(array_column($seven_days, 'count')) ?> bài</strong></span>
        </div>
    </div>

    <!-- SVG Area Chart Canvas -->
    <div class="relative w-full overflow-hidden pt-2">
        <svg class="w-full h-32 overflow-visible" viewBox="0 0 500 100" preserveAspectRatio="none" role="img" aria-label="Biểu đồ xuất bản 7 ngày qua">
            <defs>
                <linearGradient id="areaGradient" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#0F172A" stop-opacity="0.18" class="dark:stop-color-slate-400"/>
                    <stop offset="100%" stop-color="#0F172A" stop-opacity="0.0"/>
                </linearGradient>
            </defs>

            <!-- Hairline Grid Guidelines -->
            <line x1="25" y1="20" x2="475" y2="20" stroke="currentColor" stroke-dasharray="3 3" class="text-slate-100 dark:text-slate-800" stroke-width="1"/>
            <line x1="25" y1="52" x2="475" y2="52" stroke="currentColor" stroke-dasharray="3 3" class="text-slate-100 dark:text-slate-800" stroke-width="1"/>
            <line x1="25" y1="85" x2="475" y2="85" stroke="currentColor" class="text-slate-200 dark:text-slate-800" stroke-width="1"/>

            <!-- Area Fill -->
            <?php if (!empty($svg_area_path)): ?>
            <path d="<?= $svg_area_path ?>" fill="url(#areaGradient)" />
            <path d="<?= $svg_line_path ?>" fill="none" stroke="currentColor" stroke-width="2" class="text-slate-900 dark:text-slate-200" stroke-linecap="round" stroke-linejoin="round"/>
            <?php endif; ?>

            <!-- Interactive Data Points -->
            <?php foreach ($chart_points as $pt): ?>
            <g class="group cursor-pointer">
                <circle cx="<?= $pt['x'] ?>" cy="<?= $pt['y'] ?>" r="3.5" class="fill-white dark:fill-slate-900 stroke-slate-900 dark:stroke-slate-200 stroke-2 group-hover:r-5 transition-all duration-150" />
                <title><?= $pt['label'] ?>: <?= $pt['count'] ?> bài viết</title>
            </g>
            <?php endforeach; ?>
        </svg>

        <!-- X-Axis Labels -->
        <div class="flex justify-between px-2 pt-2 text-[10px] font-mono text-slate-400">
            <?php foreach ($seven_days as $item): ?>
            <span class="tabular-nums text-center"><?= $item['label'] ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ROW 3: BENTO CONTENT GRID (8-Col Data Table + 4-Col Side Widgets) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- CỘT 8: BẢNG BÀI VIẾT MỚI XUẤT BẢN -->
    <div class="lg:col-span-8 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden flex flex-col justify-between">
        
        <div>
            <!-- Header Bảng: Lọc trạng thái & Tìm kiếm nhanh -->
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-slate-900 dark:text-white">
                        Bài Viết Mới Xuất Bản
                    </span>
                    <span class="text-[11px] font-mono text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded tabular-nums">
                        <?= count($recent_articles) ?>
                    </span>
                </div>

                <!-- Bộ lọc trạng thái tabs & Ô tìm nhanh client-side -->
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <i class="fas fa-search absolute left-2.5 top-2 text-slate-400 text-[10px]"></i>
                        <input type="text" id="table-search-input" placeholder="Lọc tiêu đề..." 
                               class="pl-7 pr-2.5 py-1 text-xs rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-slate-400 w-36 sm:w-44 transition-all">
                    </div>
                    <a href="<?= url('admin/articles.php') ?>" class="text-xs font-medium text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors duration-150 inline-flex items-center gap-1 whitespace-nowrap pl-2">
                        <span>Tất cả</span>
                        <i class="fas fa-chevron-right text-[9px]"></i>
                    </a>
                </div>
            </div>

            <!-- Bảng Dữ Liệu Chuyên Sâu -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="dashboard-articles-table">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider text-[11px] font-medium border-b border-slate-200 dark:border-slate-800">
                            <th class="py-3 px-4 font-medium">Tiêu đề bài viết</th>
                            <th class="py-3 px-4 font-medium">Chuyên mục</th>
                            <th class="py-3 px-4 font-medium text-right">Lượt đọc</th>
                            <th class="py-3 px-4 font-medium text-center">Trạng thái</th>
                            <th class="py-3 px-4 font-medium text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-[13px]">
                        <?php if (empty($recent_articles)): ?>
                        <tr>
                            <td colspan="5" class="py-10 text-center text-xs text-slate-400 italic">
                                <div class="w-10 h-10 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-2 text-base">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                                <span>Chưa có bài viết nào trong hệ thống.</span>
                                <div class="mt-2">
                                    <a href="<?= url('admin/article_edit.php') ?>" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-900 dark:text-white hover:underline">
                                        <i class="fas fa-plus text-[10px]"></i> Viết bài đầu tiên ngay
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($recent_articles as $art): ?>
                            <tr class="article-row hover:bg-slate-50/75 dark:hover:bg-slate-800/40 transition-colors duration-150">
                                
                                <!-- Tiêu đề + Thời gian + Tác giả -->
                                <td class="py-3.5 px-4 max-w-sm">
                                    <a href="<?= url('article.php?slug=' . urlencode($art['slug'])) ?>" target="_blank" class="article-title font-medium text-slate-900 dark:text-slate-100 hover:text-slate-600 dark:hover:text-slate-300 line-clamp-1">
                                        <?= htmlspecialchars($art['title']) ?>
                                    </a>
                                    <div class="flex items-center gap-2 text-[11px] text-slate-400 tabular-nums mt-0.5">
                                        <span><?= date('d/m/Y - H:i', strtotime($art['created_at'])) ?></span>
                                        <span>•</span>
                                        <span><?= htmlspecialchars($art['author_name'] ?? 'Tòa soạn') ?></span>
                                    </div>
                                </td>

                                <!-- Chuyên mục -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 ring-1 ring-inset ring-slate-300/40">
                                        <?= htmlspecialchars($art['category_name'] ?? 'Chung') ?>
                                    </span>
                                </td>

                                <!-- Lượt xem -->
                                <td class="py-3.5 px-4 text-right font-mono font-medium text-slate-700 dark:text-slate-300 tabular-nums text-xs">
                                    <?= number_format($art['views_count']) ?>
                                </td>

                                <!-- Trạng thái -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <?php if ($art['status'] === 'published'): ?>
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 ring-1 ring-inset ring-emerald-600/20">
                                            Đã đăng
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-medium bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 ring-1 ring-inset ring-slate-300/30">
                                            Bản nháp
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Thao tác hàng -->
                                <td class="py-3.5 px-4 text-right space-x-1 whitespace-nowrap">
                                    <a href="<?= url('admin/article_edit.php?id=' . $art['id']) ?>" class="inline-flex items-center justify-center w-7 h-7 rounded-md border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors duration-150" title="Chỉnh sửa bài viết">
                                        <i class="fas fa-pen text-[10px]"></i>
                                    </a>
                                    <a href="<?= url('article.php?slug=' . urlencode($art['slug'])) ?>" target="_blank" class="inline-flex items-center justify-center w-7 h-7 rounded-md border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors duration-150" title="Xem trước trên website">
                                        <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                    </a>
                                </td>

                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table Footer Pagination shortcut -->
        <div class="p-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-400 bg-slate-50/50 dark:bg-slate-900/50">
            <span>Hiển thị <?= min(8, count($recent_articles)) ?> bài viết gần nhất</span>
            <a href="<?= url('admin/articles.php') ?>" class="font-medium text-slate-700 dark:text-slate-300 hover:underline">
                Xem toàn bộ <?= $stats['articles'] ?> bài viết &rarr;
            </a>
        </div>

    </div>

    <!-- CỘT 4: BÌNH LUẬN & TELEMETRY HẠ TẦNG -->
    <div class="lg:col-span-4 space-y-6">

        <!-- Widget 1: Hàng Đợi Kiểm Duyệt Bình Luận (Review Queue) -->
        <div class="rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                        Ý Kiến Bạn Đọc
                    </h3>
                    <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-600/20 tabular-nums">
                        <?= $stats['comments'] ?>
                    </span>
                </div>
                <a href="<?= url('admin/comments.php') ?>" class="text-xs font-medium text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors duration-150">
                    Kiểm duyệt &rarr;
                </a>
            </div>

            <div class="space-y-3">
                <?php if (empty($recent_comments)): ?>
                    <p class="text-xs text-slate-400 italic py-4 text-center">Chưa có bình luận nào gửi đến tòa soạn.</p>
                <?php else: ?>
                    <?php foreach ($recent_comments as $c): ?>
                    <div class="p-3 rounded-md bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 text-xs">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="font-medium text-slate-900 dark:text-white truncate pr-2">
                                <?= htmlspecialchars($c['author_name']) ?>
                            </span>
                            <span class="text-[10px] text-slate-400 tabular-nums whitespace-nowrap">
                                <?= time_ago($c['created_at']) ?>
                            </span>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300 line-clamp-2 leading-relaxed">
                            <?= htmlspecialchars($c['content']) ?>
                        </p>
                        <a href="<?= url('article.php?slug=' . urlencode($c['article_slug'] ?? '')) ?>" target="_blank" class="block mt-1.5 text-[11px] text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 truncate">
                            <i class="fas fa-file-lines mr-1 text-[10px]"></i>
                            <?= htmlspecialchars($c['article_title'] ?? 'Bài viết') ?>
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Widget 2: Trạng Thái Hạ Tầng & Tên Miền (Telemetry) -->
        <div class="rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-server text-slate-400 text-xs"></i>
                    <span>Hạ Tầng Triển Khai</span>
                </h3>
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" title="Hệ thống trực tuyến"></span>
            </div>

            <div class="space-y-2 text-xs">
                <div class="flex items-center justify-between p-2 rounded-md bg-slate-50 dark:bg-slate-800/40">
                    <span class="text-slate-500">Máy chủ</span>
                    <span class="font-mono text-slate-800 dark:text-slate-200 font-medium">InfinityFree (PHP 8.3)</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-md bg-slate-50 dark:bg-slate-800/40">
                    <span class="text-slate-500">Cơ sở dữ liệu</span>
                    <span class="font-mono text-slate-800 dark:text-slate-200 font-medium">MySQL Dual-Mode</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-md bg-slate-50 dark:bg-slate-800/40">
                    <span class="text-slate-500">Tên miền phụ</span>
                    <span class="font-mono text-slate-800 dark:text-slate-200 font-medium">gennew.rf.gd</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-md bg-slate-50 dark:bg-slate-800/40">
                    <span class="text-slate-500">Tên miền riêng</span>
                    <span class="font-mono text-slate-800 dark:text-slate-200 font-medium text-emerald-600 dark:text-emerald-400">novagen.io.vn</span>
                </div>
            </div>

            <a href="<?= url('admin/domain_guide.php') ?>" class="mt-3.5 w-full py-2 rounded-md border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/60 text-slate-700 dark:text-slate-200 text-xs font-medium flex items-center justify-center gap-1.5 transition-colors duration-150">
                <i class="fas fa-book-bookmark text-[11px] text-slate-400"></i>
                <span>Xem Tài Liệu Cấu Hình DNS</span>
            </a>
        </div>

        <!-- Widget 3: Phím Tắt Thao Tác Nhanh (Quick Actions) -->
        <div class="rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-3">
                Thao Tác Nhanh
            </h3>
            <div class="space-y-2">
                <a href="<?= url('admin/article_edit.php') ?>" class="w-full px-3 py-2 rounded-md bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-xs font-medium flex items-center justify-between transition-colors duration-150">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-pen-to-square text-[11px]"></i>
                        <span>Soạn thảo bài viết mới</span>
                    </span>
                    <i class="fas fa-plus text-[10px]"></i>
                </a>

                <a href="<?= url('admin/categories.php') ?>" class="w-full px-3 py-2 rounded-md border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-medium flex items-center justify-between transition-colors duration-150">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-folder-plus text-[11px] text-slate-400"></i>
                        <span>Thêm chuyên mục mới</span>
                    </span>
                    <i class="fas fa-chevron-right text-[9px] text-slate-400"></i>
                </a>

                <?php if (is_admin()): ?>
                <a href="<?= url('admin/settings.php') ?>" class="w-full px-3 py-2 rounded-md border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-medium flex items-center justify-between transition-colors duration-150">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-gear text-[11px] text-slate-400"></i>
                        <span>Cài đặt tiêu đề & Logo</span>
                    </span>
                    <i class="fas fa-chevron-right text-[9px] text-slate-400"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<!-- Client-side Fast Table Filter Script -->
<script>
    const tableSearchInput = document.getElementById('table-search-input');
    const articleRows = document.querySelectorAll('.article-row');

    if (tableSearchInput) {
        tableSearchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            articleRows.forEach(row => {
                const title = row.querySelector('.article-title')?.textContent.toLowerCase() || '';
                if (!query || title.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>