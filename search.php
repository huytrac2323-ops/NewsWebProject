<?php
/**
 * V-NEWS CMS - Tìm Kiếm Bài Báo (Search Results)
 */
require_once __DIR__ . '/config/config.php';

$query = trim($_GET['q'] ?? '');
$results = [];
$total_results = 0;

if (!empty($query)) {
    try {
        $searchTerm = '%' . $query . '%';
        $stmt = $pdo->prepare("
            SELECT a.*, c.name as category_name, c.slug as category_slug, u.full_name as author_name
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN users u ON a.author_id = u.id
            WHERE a.status = 'published' AND (a.title LIKE ? OR a.excerpt LIKE ? OR a.tags LIKE ?)
            ORDER BY a.id DESC LIMIT 20
        ");
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        $results = $stmt->fetchAll();
        $total_results = count($results);
    } catch (Exception $e) {
        $results = [];
    }
}

$page_title = 'Tìm kiếm: ' . ($query ?: 'Tất cả');
require_once ROOT_PATH . '/templates/header.php';
?>

<div class="mb-10 pb-6 border-b-2 border-neutral-900 dark:border-neutral-200">
    <div class="text-[11px] font-bold uppercase tracking-wider text-red-700 dark:text-red-400 mb-2">
        Kết Quả Tìm Kiếm
    </div>
    <h1 class="font-headline text-2xl sm:text-4xl font-black text-neutral-900 dark:text-white">
        <?= $total_results ?> kết quả cho: <span class="italic font-editorial font-normal">"<?= htmlspecialchars($query) ?>"</span>
    </h1>

    <form action="<?= url('search.php') ?>" method="GET" class="mt-4 flex gap-2 max-w-md">
        <input type="text" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="Tìm kiếm từ khóa khác..." 
               class="flex-1 px-3 py-2 bg-white dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-xs focus:outline-none focus:border-neutral-900">
        <button type="submit" class="px-4 py-2 bg-neutral-900 text-white dark:bg-white dark:text-neutral-900 text-xs font-bold uppercase tracking-wider">
            Tìm
        </button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
    <div class="lg:col-span-8">
        <?php if (empty($results)): ?>
        <div class="p-12 text-center border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#0c0f14]">
            <p class="font-editorial text-sm text-neutral-500 italic">Không tìm thấy bài viết phù hợp với từ khóa này.</p>
        </div>
        <?php else: ?>
        <div class="divide-y divide-neutral-200 dark:divide-neutral-800">
            <?php foreach ($results as $item): ?>
            <article class="py-6 first:pt-0 flex flex-col sm:flex-row gap-5 group">
                <a href="<?= url('article.php?slug=' . urlencode($item['slug'])) ?>" class="w-full sm:w-48 h-32 overflow-hidden flex-shrink-0 bg-neutral-100 dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-800">
                    <img src="<?= htmlspecialchars(upload_url($item['thumbnail'])) ?>" 
                         alt="<?= htmlspecialchars($item['title']) ?>" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </a>
                <div class="flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center space-x-2 text-[11px] text-neutral-400 mb-1">
                            <span class="font-bold text-red-700 dark:text-red-400 uppercase"><?= htmlspecialchars($item['category_name']) ?></span>
                            <span>•</span>
                            <span><?= date('d/m/Y', strtotime($item['created_at'])) ?></span>
                        </div>
                        <h3 class="font-headline text-base sm:text-lg font-bold text-neutral-900 dark:text-white group-hover:text-red-700 transition-colors line-clamp-2 leading-snug">
                            <a href="<?= url('article.php?slug=' . urlencode($item['slug'])) ?>">
                                <?= htmlspecialchars($item['title']) ?>
                            </a>
                        </h3>
                        <p class="font-editorial text-xs text-neutral-600 dark:text-neutral-400 mt-1 line-clamp-2 leading-relaxed">
                            <?= htmlspecialchars($item['excerpt']) ?>
                        </p>
                    </div>
                    <div class="mt-2 text-[11px] text-neutral-500">
                        Bởi <?= htmlspecialchars($item['author_name'] ?? 'Tòa soạn') ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="lg:col-span-4 lg:border-l border-neutral-200 dark:border-neutral-800 lg:pl-8 pt-8 lg:pt-0 border-t lg:border-t-0">
        <?php require ROOT_PATH . '/templates/sidebar.php'; ?>
    </div>
</div>

<?php require_once ROOT_PATH . '/templates/footer.php'; ?>
