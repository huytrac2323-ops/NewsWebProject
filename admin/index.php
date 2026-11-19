<?php
/**
 * V-NEWS CMS - Bảng Điều Khiển Quản Trị (Dashboard)
 * Compliant with AntiSlopUI Design Contract (DESIGN_SYSTEM.md)
 */
$page_title = 'Bảng Điều Khiển';
require_once __DIR__ . '/includes/header.php';

// 1. Thống kê tổng quan với try-catch
$stats = [
    'articles' => 0,
    'categories' => 0,
    'comments' => 0,
    'views' => 0
];

try {
    $stats['articles'] = (int)$pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    $stats['categories'] = (int)$pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    $stats['comments'] = (int)$pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
    $stats['views'] = (int)$pdo->query("SELECT COALESCE(SUM(views_count), 0) FROM articles")->fetchColumn();
} catch (Exception $e) {}

// 2. Lấy 6 bài viết mới nhất
$recent_articles = [];
try {
    $stmt = $pdo->query("
        SELECT a.*, c.name as category_name, c.color as category_color 
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        ORDER BY a.id DESC LIMIT 6
    ");
    $recent_articles = $stmt->fetchAll();
} catch (Exception $e) {}

// 3. Lấy 4 bình luận mới nhất
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
        <h1 class="text-lg font-semibold tracking-tight text-slate-900 dark:text-white">
            Tổng Quan Hệ Thống Xuất Bản
        </h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Theo dõi lưu lượng truy cập, tình trạng bài viết và phản hồi độc giả thời gian thực.
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

<!-- ROW 1: 4 KPI STATS CARDS (Kim Tự Tháp Ngược - Inverted Pyramid) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    
    <!-- KPI 1: Bài Viết -->
    <div class="p-5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 transition-colors duration-150">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Bài Viết Đã Đăng</span>
            <div class="w-8 h-8 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center text-xs">
                <i class="fas fa-newspaper"></i>
            </div>
        </div>
        <div class="text-2xl font-semibold tabular-nums text-slate-900 dark:text-white tracking-tight">
            <?= number_format($stats['articles']) ?>
        </div>
        <div class="mt-2.5 flex items-center gap-1.5">
            <span class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 text-[11px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 ring-1 ring-inset ring-emerald-600/20 tabular-nums">
                <i class="fas fa-check text-[9px]"></i> <?= $stats['articles'] ?> bài
            </span>
            <span class="text-[11px] text-slate-400">hoàn tất xuất bản</span>
        </div>
    </div>

    <!-- KPI 2: Lượt Xem -->
    <div class="p-5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 transition-colors duration-150">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Tổng Lượt Đọc Bài</span>
            <div class="w-8 h-8 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center text-xs">
                <i class="fas fa-eye"></i>
            </div>
        </div>
        <div class="text-2xl font-semibold tabular-nums text-slate-900 dark:text-white tracking-tight">
            <?= number_format($stats['views']) ?>
        </div>
        <div class="mt-2.5 flex items-center gap-1.5">
            <span class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 text-[11px] font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 ring-1 ring-inset ring-slate-300/30 tabular-nums">
                <i class="fas fa-chart-line text-[9px]"></i> Tương tác
            </span>
            <span class="text-[11px] text-slate-400">lưu lượng thực tế</span>
        </div>
    </div>

    <!-- KPI 3: Chuyên Mục -->
    <div class="p-5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 transition-colors duration-150">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Chuyên Mục Tin Tức</span>
            <div class="w-8 h-8 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center text-xs">
                <i class="fas fa-folder-tree"></i>
            </div>
        </div>
        <div class="text-2xl font-semibold tabular-nums text-slate-900 dark:text-white tracking-tight">
            <?= number_format($stats['categories']) ?>
        </div>
        <div class="mt-2.5 flex items-center gap-1.5">
            <span class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 text-[11px] font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 ring-1 ring-inset ring-slate-300/30 tabular-nums">
                <?= $stats['categories'] ?> danh mục
            </span>
            <span class="text-[11px] text-slate-400">phân loại nội dung</span>
        </div>
    </div>

    <!-- KPI 4: Bình Luận -->
    <div class="p-5 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 transition-colors duration-150">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Ý Kiến Độc Giả</span>
            <div class="w-8 h-8 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 flex items-center justify-center text-xs">
                <i class="fas fa-comments"></i>
            </div>
        </div>
        <div class="text-2xl font-semibold tabular-nums text-slate-900 dark:text-white tracking-tight">
            <?= number_format($stats['comments']) ?>
        </div>
        <div class="mt-2.5 flex items-center gap-1.5">
            <span class="inline-flex items-center gap-1 rounded-md px-1.5 py-0.5 text-[11px] font-medium bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 ring-1 ring-inset ring-amber-600/20 tabular-nums">
                <?= $stats['comments'] ?> phản hồi
            </span>
            <span class="text-[11px] text-slate-400">tương tác bạn đọc</span>
        </div>
    </div>

</div>

<!-- ROW 2: INFRASTRUCTURE & DOMAIN STATUS BAR -->
<div class="mb-6 p-4 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-xs">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span class="font-medium text-slate-700 dark:text-slate-200">Máy chủ:</span>
            <span class="font-mono text-slate-500">InfinityFree (PHP 8.x + MySQL)</span>
        </div>
        <div class="flex items-center gap-2">
            <i class="fas fa-globe text-slate-400"></i>
            <span class="font-medium text-slate-700 dark:text-slate-200">Tên miền:</span>
            <span class="font-mono text-slate-600 dark:text-slate-300">novagen.io.vn / gennew.rf.gd</span>
        </div>
        <div class="flex items-center gap-2">
            <i class="fas fa-shield-halved text-slate-400"></i>
            <span class="font-medium text-slate-700 dark:text-slate-200">Bảo mật:</span>
            <span class="text-slate-500">CSRF Active • Prepared Statements</span>
        </div>
    </div>
    <a href="<?= url('admin/domain_guide.php') ?>" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 dark:text-slate-200 hover:text-slate-900 dark:hover:text-white px-3 py-1.5 rounded-md border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors duration-150 whitespace-nowrap">
        <span>Tài liệu trỏ DNS Tên Miền</span>
        <i class="fas fa-arrow-right text-[10px]"></i>
    </a>
</div>

<!-- ROW 3: BENTO CONTENT GRID (8-Col Table + 4-Col Side Panel) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- Cột Bài Viết Mới Xuất Bản (8 Cột) -->
    <div class="lg:col-span-8 rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden">
        
        <!-- Header Bảng -->
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">
                    Bài Viết Mới Xuất Bản
                </h2>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    Các bài báo và tin tức gần đây được xuất bản lên trang chủ
                </p>
            </div>
            <a href="<?= url('admin/articles.php') ?>" class="text-xs font-medium text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors duration-150 inline-flex items-center gap-1">
                <span>Xem tất cả</span>
                <i class="fas fa-chevron-right text-[9px]"></i>
            </a>
        </div>

        <!-- Bảng Dữ Liệu -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider text-[11px] font-medium border-b border-slate-200 dark:border-slate-800">
                        <th class="py-3 px-4 font-medium">Tiêu đề bài viết</th>
                        <th class="py-3 px-4 font-medium">Chuyên mục</th>
                        <th class="py-3 px-4 font-medium text-right">Lượt xem</th>
                        <th class="py-3 px-4 font-medium text-center">Trạng thái</th>
                        <th class="py-3 px-4 font-medium text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-[13px]">
                    <?php if (empty($recent_articles)): ?>
                    <tr>
                        <td colspan="5" class="py-8 text-center text-xs text-slate-400 italic">
                            Chưa có bài viết nào trong hệ thống.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($recent_articles as $art): ?>
                        <tr class="hover:bg-slate-50/75 dark:hover:bg-slate-800/40 transition-colors duration-150">
                            
                            <!-- Tiêu đề + Thời gian -->
                            <td class="py-3.5 px-4 max-w-sm">
                                <a href="<?= url('article.php?slug=' . urlencode($art['slug'])) ?>" target="_blank" class="font-medium text-slate-900 dark:text-slate-100 hover:text-slate-600 dark:hover:text-slate-300 line-clamp-1">
                                    <?= htmlspecialchars($art['title']) ?>
                                </a>
                                <span class="text-[11px] text-slate-400 tabular-nums">
                                    <?= date('d/m/Y - H:i', strtotime($art['created_at'])) ?>
                                </span>
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

    <!-- Cột Phụ: Bình Luận & Thao Tác Nhanh (4 Cột) -->
    <div class="lg:col-span-4 space-y-6">

        <!-- Widget 1: Bình luận mới nhất -->
        <div class="rounded-lg border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                    Ý Kiến Bạn Đọc
                </h3>
                <a href="<?= url('admin/comments.php') ?>" class="text-xs font-medium text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors duration-150">
                    Quản lý &rarr;
                </a>
            </div>

            <div class="space-y-3">
                <?php if (empty($recent_comments)): ?>
                    <p class="text-xs text-slate-400 italic py-3 text-center">Chưa có bình luận nào.</p>
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

        <!-- Widget 2: Phím Tắt Thao Tác Nhanh (Quick Actions) -->
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>