<?php
/**
 * V-NEWS CMS - Trang Chủ Báo Điện Tử (Editorial Journal Frontpage)
 * Thiết kế chuẩn phong cách Báo chí Quốc tế (The New York Times / The Verge)
 */
require_once __DIR__ . '/config/config.php';

// 1. Lấy bài viết Nổi Bật Chính & Phụ
$featured_posts = [];
try {
    $stmt = $pdo->query("
        SELECT a.*, c.name as category_name, c.slug as category_slug, c.color as category_color, u.full_name as author_name
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        LEFT JOIN users u ON a.author_id = u.id
        WHERE a.status = 'published'
        ORDER BY a.is_featured DESC, a.id DESC LIMIT 6
    ");
    $featured_posts = $stmt->fetchAll();
} catch (Exception $e) {
    $featured_posts = [];
}

$main_lead = $featured_posts[0] ?? null;
$secondary_leads = array_slice($featured_posts, 1, 2);
$tertiary_leads = array_slice($featured_posts, 3, 3);

// 2. Dòng sự kiện / Tin mới nhất (The Chronological Feed)
$timeline_news = [];
try {
    $stmt = $pdo->query("
        SELECT a.id, a.title, a.slug, a.created_at, c.name as category_name 
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        WHERE a.status = 'published'
        ORDER BY a.id DESC LIMIT 7
    ");
    $timeline_news = $stmt->fetchAll();
} catch (Exception $e) {
    $timeline_news = [];
}

// 3. Tin theo chuyên mục
$grid_articles = [];
try {
    $stmt = $pdo->query("
        SELECT a.*, c.name as category_name, c.slug as category_slug, u.full_name as author_name
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        LEFT JOIN users u ON a.author_id = u.id
        WHERE a.status = 'published'
        ORDER BY a.views_count DESC LIMIT 8
    ");
    $grid_articles = $stmt->fetchAll();
} catch (Exception $e) {
    $grid_articles = [];
}

require_once ROOT_PATH . '/templates/header.php';
?>

<!-- SECTION 1: EDITORIAL HEADLINE GRID (BỐ CỤC 3 CỘT BÁO CHÍ CHUẨN MỰC) -->
<?php if ($main_lead): ?>
<section class="pb-8 sm:pb-10 mb-8 sm:mb-12 border-b border-neutral-300 dark:border-neutral-800">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- CỘT 1: TIN ĐINH / LEAD STORY (Chiếm 6 cột - Trọng tâm mặt báo) -->
        <div class="lg:col-span-6 lg:border-r border-neutral-200 dark:border-neutral-800 lg:pr-8">
            <article class="group">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="text-[11px] font-black uppercase tracking-widest text-red-700 dark:text-red-400">
                        <?= htmlspecialchars($main_lead['category_name']) ?>
                    </span>
                    <span class="text-neutral-400 text-xs">•</span>
                    <span class="text-xs text-neutral-500 font-medium">Tiêu điểm hôm nay</span>
                </div>

                <h2 class="font-headline text-xl sm:text-3xl lg:text-4xl font-black text-neutral-900 dark:text-white leading-[1.2] mb-3 group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors break-words">
                    <a href="<?= url('article.php?slug=' . urlencode($main_lead['slug'])) ?>">
                        <?= htmlspecialchars($main_lead['title']) ?>
                    </a>
                </h2>

                <p class="font-editorial text-neutral-600 dark:text-neutral-300 text-sm sm:text-base leading-relaxed mb-4 line-clamp-3">
                    <?= htmlspecialchars($main_lead['excerpt']) ?>
                </p>

                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-neutral-500 mb-4 font-medium">
                    <span class="text-neutral-900 dark:text-neutral-200 font-bold">Bởi <?= htmlspecialchars($main_lead['author_name'] ?? 'Tòa soạn') ?></span>
                    <span>•</span>
                    <span class="tabular-nums"><?= time_ago($main_lead['created_at']) ?></span>
                    <span>•</span>
                    <span><?= reading_time($main_lead['content']) ?></span>
                </div>

                <a href="<?= url('article.php?slug=' . urlencode($main_lead['slug'])) ?>" class="block overflow-hidden bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-800">
                    <img src="<?= htmlspecialchars(upload_url($main_lead['thumbnail'])) ?>" 
                         alt="<?= htmlspecialchars($main_lead['title']) ?>" 
                         class="w-full h-56 sm:h-80 lg:h-96 object-cover group-hover:scale-[1.02] transition-transform duration-500">
                </a>
            </article>
        </div>

        <!-- CỘT 2: CÁC TIN TIÊU ĐIỂM KẾ TIẾP (Chiếm 3 cột) -->
        <div class="lg:col-span-3 lg:border-r border-neutral-200 dark:border-neutral-800 lg:pr-8 flex flex-col divide-y divide-neutral-200 dark:divide-neutral-800 space-y-6 pt-6 lg:pt-0 border-t lg:border-t-0">
            <?php foreach ($secondary_leads as $sec): ?>
            <article class="pt-6 first:pt-0 group">
                <span class="text-[10px] font-black uppercase tracking-widest text-neutral-500 dark:text-neutral-400 mb-1.5 block">
                    <?= htmlspecialchars($sec['category_name']) ?>
                </span>
                
                <h3 class="font-headline text-base sm:text-xl font-bold text-neutral-900 dark:text-white leading-snug group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors mb-2 break-words">
                    <a href="<?= url('article.php?slug=' . urlencode($sec['slug'])) ?>">
                        <?= htmlspecialchars($sec['title']) ?>
                    </a>
                </h3>

                <p class="font-editorial text-neutral-600 dark:text-neutral-400 text-xs sm:text-sm line-clamp-2 leading-relaxed mb-3">
                    <?= htmlspecialchars($sec['excerpt']) ?>
                </p>

                <?php if (!empty($sec['thumbnail'])): ?>
                <div class="h-36 overflow-hidden bg-neutral-100 dark:bg-neutral-800 mb-2.5">
                    <img src="<?= htmlspecialchars(upload_url($sec['thumbnail'])) ?>" 
                         alt="<?= htmlspecialchars($sec['title']) ?>" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <?php endif; ?>

                <span class="text-[11px] text-neutral-400 font-medium block tabular-nums">
                    <?= time_ago($sec['created_at']) ?>
                </span>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- CỘT 3: DÒNG THỜI SỰ THEO MỐC GIỜ (THE LIVE FEED - Chiếm 3 cột) -->
        <div class="lg:col-span-3 flex flex-col justify-between pt-6 lg:pt-0 border-t lg:border-t-0 border-neutral-200 dark:border-neutral-800">
            <div>
                <div class="flex items-center space-x-2 pb-2 mb-4 border-b-2 border-neutral-900 dark:border-neutral-100">
                    <span class="w-2 h-2 rounded-full bg-red-600 animate-ping"></span>
                    <h3 class="text-xs font-black uppercase tracking-widest text-neutral-900 dark:text-white">
                        Dòng Thời Sự 24H
                    </h3>
                </div>

                <div class="space-y-4">
                    <?php foreach ($timeline_news as $tl): ?>
                    <article class="group pb-3 border-b border-neutral-100 dark:border-neutral-800/80">
                        <div class="flex items-center space-x-2 text-[11px] text-neutral-400 mb-1">
                            <span class="font-mono font-bold text-red-700 dark:text-red-400 tabular-nums"><?= date('H:i', strtotime($tl['created_at'])) ?></span>
                            <span>•</span>
                            <span class="uppercase font-semibold text-[10px]"><?= htmlspecialchars($tl['category_name']) ?></span>
                        </div>
                        <h4 class="font-headline text-xs sm:text-sm font-bold text-neutral-900 dark:text-neutral-200 group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors leading-snug break-words">
                            <a href="<?= url('article.php?slug=' . urlencode($tl['slug'])) ?>">
                                <?= htmlspecialchars($tl['title']) ?>
                            </a>
                        </h4>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Box Thông tin Tòa soạn / Đăng ký tên miền riêng -->
            <div class="mt-6 p-4 bg-neutral-100 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800">
                <span class="text-[10px] font-black uppercase tracking-widest text-neutral-500 block mb-1">Tòa soạn thông báo</span>
                <p class="text-xs text-neutral-700 dark:text-neutral-300 leading-relaxed font-editorial">
                    Hệ thống đã sẵn sàng kết nối tên miền riêng. Mọi thắc mắc và đóng góp bài vở xin gửi về ban biên tập.
                </p>
                <a href="<?= url('contact.php') ?>" class="inline-block mt-2 text-xs font-bold text-red-700 dark:text-red-400 underline uppercase tracking-wider text-[10px]">
                    Gửi tin bài &rarr;
                </a>
            </div>
        </div>

    </div>
</section>
<?php endif; ?>

<!-- SECTION 2: CHUYÊN ĐỀ NỔI BẬT & CỘT SIDEBAR -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">

    <!-- CỘT CHÍNH (8 CỘT): CÁC BÀI VIẾT NỔI BẬT & CHUYÊN ĐỀ -->
    <div class="lg:col-span-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-2 mb-6 border-b-2 border-neutral-900 dark:border-neutral-200 gap-1">
            <h3 class="font-headline text-lg sm:text-2xl font-black text-neutral-900 dark:text-white uppercase tracking-tight">
                Phân Tích & Bài Viết Chuyên Sâu
            </h3>
            <span class="text-xs text-neutral-500 font-editorial italic">Tổng hợp từ các ban chuyên đề</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 sm:gap-x-8 gap-y-8 sm:gap-y-10">
            <?php foreach ($grid_articles as $post): ?>
            <article class="group flex flex-col justify-between border-b border-neutral-200 dark:border-neutral-800 pb-6">
                <div>
                    <a href="<?= url('article.php?slug=' . urlencode($post['slug'])) ?>" class="block h-48 sm:h-52 overflow-hidden bg-neutral-100 dark:bg-neutral-800 mb-3 border border-neutral-200 dark:border-neutral-800">
                        <img src="<?= htmlspecialchars(upload_url($post['thumbnail'])) ?>" 
                             alt="<?= htmlspecialchars($post['title']) ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </a>

                    <div class="flex items-center space-x-2 text-[11px] text-neutral-400 mb-1.5 font-medium">
                        <span class="font-black uppercase tracking-wider text-red-700 dark:text-red-400">
                            <?= htmlspecialchars($post['category_name']) ?>
                        </span>
                        <span>•</span>
                        <span class="tabular-nums"><?= date('d/m/Y', strtotime($post['created_at'])) ?></span>
                    </div>

                    <h4 class="font-headline text-base sm:text-lg font-bold text-neutral-900 dark:text-white group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors line-clamp-2 leading-snug mb-2 break-words">
                        <a href="<?= url('article.php?slug=' . urlencode($post['slug'])) ?>">
                            <?= htmlspecialchars($post['title']) ?>
                        </a>
                    </h4>

                    <p class="font-editorial text-xs sm:text-sm text-neutral-600 dark:text-neutral-400 line-clamp-2 leading-relaxed">
                        <?= htmlspecialchars($post['excerpt']) ?>
                    </p>
                </div>

                <div class="mt-4 pt-3 border-t border-neutral-100 dark:border-neutral-800/80 flex items-center justify-between text-xs text-neutral-500">
                    <span>Bởi <?= htmlspecialchars($post['author_name'] ?? 'Tòa soạn') ?></span>
                    <a href="<?= url('article.php?slug=' . urlencode($post['slug'])) ?>" class="font-bold text-neutral-900 dark:text-white group-hover:text-red-700 transition-colors inline-flex items-center">
                        Đọc tiếp &rarr;
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- CỘT PHỤ (4 CỘT): SIDEBAR BÁO CHÍ -->
    <div class="lg:col-span-4 lg:border-l border-neutral-200 dark:border-neutral-800 lg:pl-8 pt-8 lg:pt-0 border-t lg:border-t-0">
        <?php require ROOT_PATH . '/templates/sidebar.php'; ?>
    </div>

</div>

<?php require_once ROOT_PATH . '/templates/footer.php'; ?>
