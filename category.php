<?php
/**
 * V-NEWS CMS - Lưu Trữ Theo Chuyên Mục (Category Archive)
 */
require_once __DIR__ . '/config/config.php';

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('Location: ' . url());
    exit;
}

// 1. Tìm thông tin chuyên mục
$category = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    $category = $stmt->fetch();
} catch (Exception $e) {
    $category = null;
}

if (!$category) {
    set_flash('error', 'Chuyên mục không tồn tại.');
    header('Location: ' . url());
    exit;
}

// 2. Phân trang bài viết
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 6;
$offset = ($page - 1) * $limit;

$total_articles = 0;
$articles = [];

try {
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE category_id = ? AND status = 'published'");
    $stmtCount->execute([$category['id']]);
    $total_articles = (int)$stmtCount->fetchColumn();
    $total_pages = ceil($total_articles / $limit);

    $stmt = $pdo->prepare("
        SELECT a.*, u.full_name as author_name 
        FROM articles a
        LEFT JOIN users u ON a.author_id = u.id
        WHERE a.category_id = ? AND a.status = 'published'
        ORDER BY a.id DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->bindValue(1, $category['id'], PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $articles = $stmt->fetchAll();
} catch (Exception $e) {
    $articles = [];
    $total_pages = 1;
}

$page_title = 'Chuyên mục: ' . $category['name'];
$page_desc = $category['description'] ?? 'Tất cả bài viết thuộc chuyên mục ' . $category['name'];

require_once ROOT_PATH . '/templates/header.php';
?>

<!-- Editorial Category Header -->
<div class="mb-10 pb-6 border-b-2 border-neutral-900 dark:border-neutral-200">
    <div class="flex items-center space-x-2 text-[11px] font-bold uppercase tracking-wider text-red-700 dark:text-red-400 mb-2">
        <span>Chuyên Mục Báo</span>
        <span>•</span>
        <span><?= $total_articles ?> bài viết</span>
    </div>
    <h1 class="font-headline text-3xl sm:text-5xl font-black text-neutral-900 dark:text-white tracking-tight">
        <?= htmlspecialchars($category['name']) ?>
    </h1>
    <?php if (!empty($category['description'])): ?>
    <p class="font-editorial text-sm sm:text-base text-neutral-600 dark:text-neutral-400 mt-2 max-w-3xl leading-relaxed">
        <?= htmlspecialchars($category['description']) ?>
    </p>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

    <!-- Cột Danh Sách Bài Viết (8 Cột) -->
    <div class="lg:col-span-8">
        <?php if (empty($articles)): ?>
        <div class="p-12 text-center border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#0c0f14]">
            <i class="far fa-newspaper text-4xl text-neutral-300 mb-3 block"></i>
            <h3 class="font-headline text-base font-bold text-neutral-800 dark:text-neutral-200">Chưa có bài viết nào</h3>
            <p class="font-editorial text-xs text-neutral-400 mt-1">Các phóng viên đang cập nhật nội dung cho chuyên mục này.</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
            <?php foreach ($articles as $post): ?>
            <article class="bg-white dark:bg-[#0c0f14] border border-neutral-200 dark:border-neutral-800 p-5 flex flex-col justify-between group">
                <div>
                    <a href="<?= url('article.php?slug=' . urlencode($post['slug'])) ?>" class="block h-48 overflow-hidden bg-neutral-100 dark:bg-neutral-800 mb-3 border border-neutral-200 dark:border-neutral-800">
                        <img src="<?= htmlspecialchars(upload_url($post['thumbnail'])) ?>" 
                             alt="<?= htmlspecialchars($post['title']) ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </a>

                    <div class="flex items-center space-x-2 text-[11px] text-neutral-400 mb-1.5 font-medium">
                        <span><?= date('d/m/Y', strtotime($post['created_at'])) ?></span>
                        <span>•</span>
                        <span><?= number_format($post['views_count']) ?> lượt đọc</span>
                    </div>

                    <h3 class="font-headline text-base sm:text-lg font-bold text-neutral-900 dark:text-white group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors line-clamp-2 leading-snug mb-2">
                        <a href="<?= url('article.php?slug=' . urlencode($post['slug'])) ?>">
                            <?= htmlspecialchars($post['title']) ?>
                        </a>
                    </h3>

                    <p class="font-editorial text-xs sm:text-sm text-neutral-600 dark:text-neutral-400 line-clamp-2 leading-relaxed">
                        <?= htmlspecialchars($post['excerpt']) ?>
                    </p>
                </div>

                <div class="pt-3 mt-4 border-t border-neutral-100 dark:border-neutral-800 flex items-center justify-between text-xs text-neutral-500">
                    <span><?= htmlspecialchars($post['author_name'] ?? 'V-NEWS') ?></span>
                    <a href="<?= url('article.php?slug=' . urlencode($post['slug'])) ?>" class="font-bold text-neutral-900 dark:text-white group-hover:text-red-700 transition-colors">
                        Đọc bài &rarr;
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <!-- Phân trang -->
        <?php if ($total_pages > 1): ?>
        <div class="mt-12 flex justify-center items-center space-x-2 text-xs">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?slug=<?= urlencode($slug) ?>&page=<?= $i ?>" class="w-8 h-8 flex items-center justify-center font-bold <?= $i == $page ? 'bg-neutral-900 text-white dark:bg-white dark:text-neutral-900' : 'border border-neutral-300 dark:border-neutral-700 text-neutral-700 dark:text-neutral-300 hover:bg-neutral-100' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <?php endif; ?>
    </div>

    <!-- Cột Phụ (4 Cột) -->
    <div class="lg:col-span-4 lg:border-l border-neutral-200 dark:border-neutral-800 lg:pl-8 pt-8 lg:pt-0 border-t lg:border-t-0">
        <?php require ROOT_PATH . '/templates/sidebar.php'; ?>
    </div>

</div>

<?php require_once ROOT_PATH . '/templates/footer.php'; ?>
