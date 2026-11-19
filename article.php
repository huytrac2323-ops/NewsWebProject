<?php
/**
 * V-NEWS CMS - Chi Tiết Bài Báo (Editorial Article View)
 */
require_once __DIR__ . '/config/config.php';

$slug = $_GET['slug'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (empty($slug) && $id <= 0) {
    header('Location: ' . url());
    exit;
}

// 1. Tìm bài viết theo slug hoặc id
$article = null;
try {
    if (!empty($slug)) {
        $stmt = $pdo->prepare("
            SELECT a.*, c.name as category_name, c.slug as category_slug, 
                   u.full_name as author_name, u.bio as author_bio, u.avatar as author_avatar
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN users u ON a.author_id = u.id
            WHERE a.slug = ? AND a.status = 'published'
            LIMIT 1
        ");
        $stmt->execute([$slug]);
    } else {
        $stmt = $pdo->prepare("
            SELECT a.*, c.name as category_name, c.slug as category_slug, 
                   u.full_name as author_name, u.bio as author_bio, u.avatar as author_avatar
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN users u ON a.author_id = u.id
            WHERE a.id = ? AND a.status = 'published'
            LIMIT 1
        ");
        $stmt->execute([$id]);
    }
    $article = $stmt->fetch();
} catch (Exception $e) {
    $article = null;
}

if (!$article) {
    set_flash('error', 'Bài viết không tồn tại hoặc đã bị gỡ bỏ.');
    header('Location: ' . url());
    exit;
}

// 2. Tăng số lượt xem
try {
    $pdo->prepare("UPDATE articles SET views_count = views_count + 1 WHERE id = ?")->execute([$article['id']]);
    $article['views_count']++;
} catch (Exception $e) {}

// 3. Xử lý gửi bình luận
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Lỗi xác thực bảo mật CSRF!');
    } else {
        $author_name = sanitize($_POST['author_name'] ?? '');
        $author_email = sanitize($_POST['author_email'] ?? '');
        $content = sanitize($_POST['content'] ?? '');

        if (empty($author_name) || empty($author_email) || empty($content)) {
            set_flash('error', 'Vui lòng điền đầy đủ các thông tin.');
        } else {
            try {
                $stmtComment = $pdo->prepare("
                    INSERT INTO comments (article_id, author_name, author_email, content, status, created_at)
                    VALUES (?, ?, ?, ?, 'approved', CURRENT_TIMESTAMP)
                ");
                $stmtComment->execute([$article['id'], $author_name, $author_email, $content]);
                set_flash('success', 'Bình luận của bạn đã được xuất bản.');
                header('Location: ' . url('article.php?slug=' . urlencode($article['slug']) . '#comments'));
                exit;
            } catch (Exception $e) {
                set_flash('error', 'Lỗi lưu bình luận: ' . $e->getMessage());
            }
        }
    }
}

// 4. Lấy bình luận
$comments = [];
try {
    $stmtC = $pdo->prepare("SELECT * FROM comments WHERE article_id = ? AND status = 'approved' ORDER BY id DESC");
    $stmtC->execute([$article['id']]);
    $comments = $stmtC->fetchAll();
} catch (Exception $e) {}

// 5. Bài cùng chuyên mục
$related_posts = [];
try {
    $stmtR = $pdo->prepare("SELECT id, title, slug, thumbnail, created_at FROM articles WHERE category_id = ? AND id != ? AND status = 'published' ORDER BY id DESC LIMIT 3");
    $stmtR->execute([$article['category_id'], $article['id']]);
    $related_posts = $stmtR->fetchAll();
} catch (Exception $e) {}

$page_title = $article['title'];
$page_desc = $article['excerpt'];
$page_thumb = upload_url($article['thumbnail']);
$canonical_url = url('article.php?slug=' . urlencode($article['slug']));

require_once ROOT_PATH . '/templates/header.php';
?>

<!-- Progress Bar -->
<div class="fixed top-0 left-0 h-1 bg-red-700 z-50 transition-all duration-150" id="reading-progress" style="width: 0%;"></div>

<!-- Breadcrumb -->
<div class="flex items-center space-x-2 text-[11px] font-bold uppercase tracking-wider text-neutral-400 mb-6">
    <a href="<?= url() ?>" class="hover:text-red-700">Trang chủ</a>
    <span>/</span>
    <a href="<?= url('category.php?slug=' . urlencode($article['category_slug'])) ?>" class="hover:text-red-700">
        <?= htmlspecialchars($article['category_name']) ?>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

    <!-- CỘT BÀI BÁO (8 CỘT) -->
    <article class="lg:col-span-8 bg-white dark:bg-[#0c0f14] border border-neutral-200 dark:border-neutral-800 p-4 sm:p-10 lg:p-12">
        
        <!-- Category & Meta Bar -->
        <div class="flex flex-wrap items-center justify-between gap-2 pb-3 mb-4 border-b border-neutral-200 dark:border-neutral-800 text-xs text-neutral-500">
            <span class="font-black uppercase tracking-widest text-red-700 dark:text-red-400 text-xs">
                <?= htmlspecialchars($article['category_name']) ?>
            </span>
            <div class="flex flex-wrap items-center gap-2 text-[11px]">
                <span class="tabular-nums"><?= format_date_vn($article['created_at']) ?></span>
                <span>•</span>
                <span><?= reading_time($article['content']) ?></span>
                <span>•</span>
                <span class="tabular-nums"><?= number_format($article['views_count']) ?> lượt đọc</span>
            </div>
        </div>

        <!-- Headline (H1) -->
        <h1 class="font-headline text-2xl sm:text-4xl lg:text-5xl font-black text-neutral-900 dark:text-white leading-[1.2] mb-6 tracking-tight break-words">
            <?= htmlspecialchars($article['title']) ?>
        </h1>

        <!-- Author Byline & Social Share -->
        <div class="flex flex-wrap items-center justify-between py-3 mb-8 border-y-2 border-neutral-900 dark:border-neutral-700 gap-4 text-xs">
            <div>
                <span class="font-bold text-neutral-900 dark:text-white text-sm">Bởi <?= htmlspecialchars($article['author_name'] ?? 'Tòa soạn V-NEWS') ?></span>
                <span class="text-neutral-500 block text-[11px]">Phóng viên chuyên đề • Tòa soạn V-NEWS</span>
            </div>

            <!-- Share Buttons (Monochrome Editorial Style) -->
            <div class="flex items-center space-x-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-neutral-400 mr-1">Chia sẻ:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($canonical_url) ?>" target="_blank" 
                   class="px-2.5 py-1 border border-neutral-300 dark:border-neutral-700 hover:border-neutral-900 dark:hover:border-white text-neutral-700 dark:text-neutral-300 transition-colors">
                    <i class="fab fa-facebook-f mr-1"></i> Facebook
                </a>
                <button onclick="copyCurrentUrl()" 
                        class="px-2.5 py-1 border border-neutral-300 dark:border-neutral-700 hover:border-neutral-900 dark:hover:border-white text-neutral-700 dark:text-neutral-300 transition-colors">
                    <i class="fas fa-link mr-1"></i> Sao chép link
                </button>
            </div>
        </div>

        <!-- Ảnh Bìa Phóng Sự -->
        <?php if (!empty($article['thumbnail'])): ?>
        <figure class="mb-8 border border-neutral-200 dark:border-neutral-800">
            <img src="<?= htmlspecialchars(upload_url($article['thumbnail'])) ?>" 
                 alt="<?= htmlspecialchars($article['title']) ?>" 
                 class="w-full max-h-[520px] object-cover">
        </figure>
        <?php endif; ?>

        <!-- Lead Excerpt (Tóm tắt mở đầu) -->
        <?php if (!empty($article['excerpt'])): ?>
        <p class="font-editorial text-lg sm:text-xl font-normal italic text-neutral-800 dark:text-neutral-200 leading-relaxed pb-6 mb-8 border-b border-neutral-200 dark:border-neutral-800">
            <?= htmlspecialchars($article['excerpt']) ?>
        </p>
        <?php endif; ?>

        <!-- Nội dung chính -->
        <div class="article-prose pb-8 border-b-2 border-neutral-900 dark:border-neutral-700">
            <?= $article['content'] ?>
        </div>

        <!-- Tags -->
        <?php if (!empty($article['tags'])): ?>
        <div class="mt-8 flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold uppercase tracking-wider text-neutral-400 mr-2">Chủ đề:</span>
            <?php 
            $tags = explode(',', $article['tags']);
            foreach ($tags as $tag): 
                $tag = trim($tag);
                if (empty($tag)) continue;
            ?>
            <a href="<?= url('search.php?q=' . urlencode($tag)) ?>" class="text-xs px-2.5 py-1 border border-neutral-200 dark:border-neutral-800 hover:bg-neutral-900 hover:text-white dark:hover:bg-white dark:hover:text-neutral-900 transition-colors">
                #<?= htmlspecialchars($tag) ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Author Bio Box -->
        <div class="mt-10 p-6 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 flex items-start space-x-4">
            <div class="w-12 h-12 rounded-none bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 font-headline font-black text-xl flex items-center justify-center flex-shrink-0">
                <?= mb_substr($article['author_name'] ?? 'V', 0, 1) ?>
            </div>
            <div>
                <h4 class="font-headline text-base font-bold text-neutral-900 dark:text-white"><?= htmlspecialchars($article['author_name'] ?? 'Tác giả') ?></h4>
                <p class="font-editorial text-xs text-neutral-600 dark:text-neutral-400 mt-1 leading-relaxed">
                    <?= htmlspecialchars($article['author_bio'] ?? 'Biên tập viên V-NEWS. Phụ trách chuyên mục phân tích công nghệ, kinh tế số và thời sự quốc tế.') ?>
                </p>
            </div>
        </div>

        <!-- BÀI CÙNG CHUYÊN MỤC -->
        <?php if (!empty($related_posts)): ?>
        <div class="mt-12 pt-8 border-t border-neutral-200 dark:border-neutral-800">
            <h3 class="font-headline text-lg font-black uppercase tracking-wider text-neutral-900 dark:text-white mb-6">
                Cùng Chuyên Mục
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <?php foreach ($related_posts as $rel): ?>
                <a href="<?= url('article.php?slug=' . urlencode($rel['slug'])) ?>" class="group block">
                    <div class="h-32 overflow-hidden bg-neutral-100 dark:bg-neutral-800 mb-2 border border-neutral-200 dark:border-neutral-800">
                        <img src="<?= htmlspecialchars(upload_url($rel['thumbnail'])) ?>" 
                             alt="<?= htmlspecialchars($rel['title']) ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <h4 class="font-headline text-xs font-bold text-neutral-900 dark:text-white group-hover:text-red-700 transition-colors line-clamp-2 leading-snug">
                        <?= htmlspecialchars($rel['title']) ?>
                    </h4>
                    <span class="text-[11px] text-neutral-400 mt-1 block"><?= date('d/m/Y', strtotime($rel['created_at'])) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- BÌNH LUẬN Ý KIẾN BẠN ĐỌC -->
        <section id="comments" class="mt-14 pt-10 border-t-2 border-neutral-900 dark:border-neutral-700">
            <h3 class="font-headline text-xl font-black uppercase tracking-wider text-neutral-900 dark:text-white mb-6">
                Ý Kiến Bạn Đọc (<?= count($comments) ?>)
            </h3>

            <!-- Form gửi bình luận -->
            <form action="<?= url('article.php?slug=' . urlencode($article['slug'])) ?>" method="POST" class="p-6 bg-neutral-50 dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 mb-10">
                <?= csrf_field() ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-600 dark:text-neutral-400 mb-1">Họ tên *</label>
                        <input type="text" name="author_name" required placeholder="Nguyễn Văn A" 
                               class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 text-xs focus:outline-none focus:border-neutral-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-600 dark:text-neutral-400 mb-1">Email *</label>
                        <input type="email" name="author_email" required placeholder="email@example.com" 
                               class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 text-xs focus:outline-none focus:border-neutral-900">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-600 dark:text-neutral-400 mb-1">Nội dung ý kiến *</label>
                    <textarea name="content" rows="3" required placeholder="Gửi quan điểm của bạn..." 
                              class="w-full px-3 py-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-700 text-xs focus:outline-none focus:border-neutral-900"></textarea>
                </div>
                <button type="submit" name="submit_comment" value="1" 
                        class="px-5 py-2.5 text-xs font-black uppercase tracking-widest bg-neutral-900 hover:bg-neutral-800 text-white transition-colors">
                    Gửi Bình Luận
                </button>
            </form>

            <!-- Danh sách ý kiến -->
            <div class="space-y-4">
                <?php if (empty($comments)): ?>
                <p class="font-editorial text-sm text-neutral-400 italic">Chưa có bình luận nào cho bài viết này.</p>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                    <div class="p-4 border-b border-neutral-200 dark:border-neutral-800 pb-4">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="font-headline font-bold text-sm text-neutral-900 dark:text-white"><?= htmlspecialchars($comment['author_name']) ?></span>
                            <span class="text-[11px] text-neutral-400"><?= time_ago($comment['created_at']) ?></span>
                        </div>
                        <p class="font-editorial text-xs text-neutral-700 dark:text-neutral-300 leading-relaxed">
                            <?= nl2br(htmlspecialchars($comment['content'])) ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

    </article>

    <!-- SIDEBAR (4 CỘT) -->
    <div class="lg:col-span-4 pt-8 lg:pt-0 border-t lg:border-t-0 border-neutral-200 dark:border-neutral-800">
        <?php require ROOT_PATH . '/templates/sidebar.php'; ?>
    </div>

</div>

<?php require_once ROOT_PATH . '/templates/footer.php'; ?>
