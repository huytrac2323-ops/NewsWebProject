<?php
/**
 * V-NEWS CMS - Editorial Sidebar Template
 */
global $pdo;

// Lấy tin đọc nhiều nhất
$trending_articles = [];
try {
    $stmt = $pdo->query("
        SELECT a.id, a.title, a.slug, a.thumbnail, a.views_count, a.created_at, c.name as category_name
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        WHERE a.status = 'published'
        ORDER BY a.views_count DESC, a.id DESC
        LIMIT 5
    ");
    $trending_articles = $stmt->fetchAll();
} catch (Exception $e) {
    $trending_articles = [];
}

// Lấy danh sách chuyên mục
$sidebar_categories = [];
try {
    $stmt = $pdo->query("
        SELECT c.*, COUNT(a.id) as count 
        FROM categories c
        LEFT JOIN articles a ON c.id = a.category_id AND a.status = 'published'
        GROUP BY c.id
        ORDER BY count DESC, c.sort_order ASC
    ");
    $sidebar_categories = $stmt->fetchAll();
} catch (Exception $e) {
    $sidebar_categories = [];
}
?>

<aside class="space-y-10">

    <!-- WIDGET 1: TIN ĐỌC NHIỀU NHẤT (THE MOST READ - ĐÁNH SỐ SERIF SANG TRỌNG) -->
    <div>
        <div class="pb-2 mb-6 border-b-2 border-neutral-900 dark:border-neutral-200 flex items-center justify-between">
            <h3 class="font-headline text-base font-black uppercase tracking-wider text-neutral-900 dark:text-white">
                Đọc Nhiều Nhất
            </h3>
            <span class="text-[10px] uppercase tracking-widest text-neutral-400 font-semibold">24H Qua</span>
        </div>

        <div class="space-y-5">
            <?php $rank = 1; foreach ($trending_articles as $trend): ?>
            <article class="flex items-start space-x-4 group pb-4 border-b border-neutral-100 dark:border-neutral-800/80">
                <span class="font-headline text-3xl font-black text-neutral-300 dark:text-neutral-700 group-hover:text-red-700 transition-colors leading-none flex-shrink-0 w-8">
                    0<?= $rank ?>
                </span>
                <div class="flex-1 min-w-0">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 mb-0.5 block">
                        <?= htmlspecialchars($trend['category_name']) ?>
                    </span>
                    <h4 class="font-headline text-sm font-bold text-neutral-900 dark:text-neutral-200 group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors line-clamp-2 leading-snug">
                        <a href="<?= url('article.php?slug=' . urlencode($trend['slug'])) ?>">
                            <?= htmlspecialchars($trend['title']) ?>
                        </a>
                    </h4>
                    <span class="text-[11px] text-neutral-400 mt-1 block">
                        <?= number_format($trend['views_count']) ?> lượt đọc
                    </span>
                </div>
            </article>
            <?php $rank++; endforeach; ?>
        </div>
    </div>

    <!-- WIDGET 2: BẢN TIN BUỔI SÁNG (EDITORIAL NEWSLETTER - PHONG CÁCH BÁO IN) -->
    <div class="p-6 bg-neutral-900 text-white border-t-4 border-red-700">
        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-red-400 block mb-2">Điểm tin mỗi sáng</span>
        <h4 class="font-headline text-xl font-black leading-tight mb-2">
            Nhận bản tin phân tích độc quyền
        </h4>
        <p class="font-editorial text-xs text-neutral-300 leading-relaxed mb-4">
            Cung cấp bức tranh toàn cảnh về công nghệ, khởi nghiệp và kinh tế số được chọn lọc kỹ lưỡng bởi ban biên tập.
        </p>
        <form onsubmit="event.preventDefault(); showToast('Cảm ơn bạn đã đăng ký nhận bản tin!', 'success'); this.reset();" class="space-y-2">
            <input type="email" placeholder="Nhập địa chỉ email của bạn..." required 
                   class="w-full px-3 py-2 text-xs bg-neutral-800 text-white placeholder-neutral-400 border border-neutral-700 focus:outline-none focus:border-white rounded-none">
            <button type="submit" class="w-full py-2.5 text-xs font-black uppercase tracking-widest bg-red-700 hover:bg-red-800 text-white transition-colors">
                Đăng ký ngay
            </button>
        </form>
    </div>

    <!-- WIDGET 3: CHUYÊN MỤC BÁO CHÍ (EDITORIAL CATEGORIES) -->
    <div>
        <div class="pb-2 mb-4 border-b border-neutral-900 dark:border-neutral-200">
            <h3 class="font-headline text-sm font-black uppercase tracking-wider text-neutral-900 dark:text-white">
                Chuyên Mục Báo
            </h3>
        </div>
        <ul class="divide-y divide-neutral-100 dark:divide-neutral-800 text-xs font-semibold">
            <?php foreach ($sidebar_categories as $scat): ?>
            <li class="py-2.5 flex items-center justify-between group">
                <a href="<?= url('category.php?slug=' . urlencode($scat['slug'])) ?>" class="text-neutral-700 dark:text-neutral-300 group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors">
                    <?= htmlspecialchars($scat['name']) ?>
                </a>
                <span class="font-mono text-[11px] text-neutral-400"><?= $scat['count'] ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

</aside>
