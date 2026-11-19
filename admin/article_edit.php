<?php
/**
 * V-NEWS CMS - Trình Soạn Thảo & Chỉnh Sửa Bài Viết (Article Editor)
 */
$page_title = 'Biên Tập Bài Viết';
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$article = [
    'id' => 0,
    'title' => '',
    'slug' => '',
    'category_id' => 1,
    'excerpt' => '',
    'content' => '',
    'thumbnail' => '',
    'status' => 'published',
    'is_featured' => 0,
    'is_breaking' => 0,
    'tags' => ''
];

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if ($found) {
        $article = $found;
    } else {
        set_flash('error', 'Bài viết không tồn tại!');
        header('Location: ' . url('admin/articles.php'));
        exit;
    }
}

// Xử lý lưu Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token bảo mật CSRF không hợp lệ!');
    } else {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        if (empty($slug)) {
            $slug = slugify($title);
        } else {
            $slug = slugify($slug);
        }

        $category_id = (int)($_POST['category_id'] ?? 1);
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $status = in_array($_POST['status'] ?? '', ['published', 'draft']) ? $_POST['status'] : 'published';
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        $is_breaking = isset($_POST['is_breaking']) ? 1 : 0;
        $tags = trim($_POST['tags'] ?? '');
        $thumbnail = trim($_POST['thumbnail_url'] ?? $article['thumbnail']);

        // Xử lý tải ảnh lên (File Upload)
        if (!empty($_FILES['thumbnail_file']['name'])) {
            $file = $_FILES['thumbnail_file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($ext, $allowed) && $file['error'] === UPLOAD_ERR_OK) {
                $new_filename = 'img_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $target = UPLOAD_PATH . '/' . $new_filename;
                if (move_uploaded_file($file['tmp_name'], $target)) {
                    $thumbnail = $new_filename;
                }
            }
        }

        if (empty($title) || empty($content)) {
            set_flash('error', 'Vui lòng điền tiêu đề và nội dung bài viết!');
        } else {
            try {
                if ($id > 0) {
                    // Cập nhật bài viết hiện tại
                    $stmtUpdate = $pdo->prepare("
                        UPDATE articles 
                        SET title = ?, slug = ?, category_id = ?, excerpt = ?, content = ?, 
                            thumbnail = ?, status = ?, is_featured = ?, is_breaking = ?, tags = ?, updated_at = CURRENT_TIMESTAMP
                        WHERE id = ?
                    ");
                    $stmtUpdate->execute([$title, $slug, $category_id, $excerpt, $content, $thumbnail, $status, $is_featured, $is_breaking, $tags, $id]);
                    set_flash('success', 'Đã cập nhật bài viết thành công!');
                } else {
                    // Tạo bài viết mới
                    $author_id = $_SESSION['user_id'] ?? 1;
                    $stmtInsert = $pdo->prepare("
                        INSERT INTO articles (title, slug, category_id, author_id, excerpt, content, thumbnail, status, is_featured, is_breaking, tags, views_count, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, CURRENT_TIMESTAMP)
                    ");
                    $stmtInsert->execute([$title, $slug, $category_id, $author_id, $excerpt, $content, $thumbnail, $status, $is_featured, $is_breaking, $tags]);
                    $id = $pdo->lastInsertId();
                    set_flash('success', 'Đã tạo bài viết mới thành công!');
                }
                header('Location: ' . url('admin/articles.php'));
                exit;
            } catch (Exception $e) {
                set_flash('error', 'Lỗi lưu bài viết: ' . $e->getMessage());
            }
        }
    }
}

// Lấy danh sách chuyên mục
$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC")->fetchAll();
?>

<div class="max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between pb-6 mb-8 border-b border-slate-200 dark:border-slate-800">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">
                <?= $id > 0 ? 'Chỉnh Sửa Bài Viết #' . $id : 'Soạn Bài Viết Mới' ?>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Hệ thống tối ưu cấu trúc SEO và thẻ tương thích mạng xã hội.</p>
        </div>
        <a href="<?= url('admin/articles.php') ?>" class="px-4 py-2 rounded-xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-300 transition-colors">
            <i class="fas fa-arrow-left mr-1"></i> Danh Sách Bài Viết
        </a>
    </div>

    <!-- Main Editor Form -->
    <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Cột trái: Tiêu đề, Slug, Tóm tắt, Nội dung (8 Cột) -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Tiêu đề -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                            Tiêu Đề Bài Viết *
                        </label>
                        <input type="text" id="title-input" name="title" value="<?= htmlspecialchars($article['title']) ?>" required 
                               placeholder="Nhập tiêu đề tin tức hoặc bài viết cuốn hút..." 
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-base font-bold text-slate-900 dark:text-white focus:outline-none focus:border-indigo-600">
                    </div>

                    <!-- SEO Slug URL -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1 flex items-center">
                            <i class="fas fa-link mr-1 text-slate-400"></i> Đường dẫn SEO (Slug URL)
                        </label>
                        <div class="flex items-center text-xs font-mono text-slate-400 bg-slate-100 dark:bg-slate-800/80 rounded-xl px-3 border border-slate-200 dark:border-slate-700">
                            <span class="text-slate-400 select-none"><?= url('article.php?slug=') ?></span>
                            <input type="text" id="slug-input" name="slug" value="<?= htmlspecialchars($article['slug']) ?>" 
                                   placeholder="tu-dong-tao-slug" 
                                   class="flex-1 py-2 bg-transparent text-indigo-600 dark:text-indigo-400 font-bold focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Tóm tắt ngắn (Excerpt) -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wider">
                        Đoạn Mở Đầu / Tóm Tắt (Lead Excerpt)
                    </label>
                    <textarea name="excerpt" rows="3" placeholder="Đoạn trích tóm tắt bài viết xuất hiện trên trang chủ và mạng xã hội..." 
                              class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-600"><?= htmlspecialchars($article['excerpt']) ?></textarea>
                </div>

                <!-- Trình soạn thảo Nội Dung Bài Viết -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            Nội Dung Chi Tiết (Rich HTML Content) *
                        </label>
                    </div>

                    <!-- Toolbar Hỗ trợ Soạn Thảo Nhanh -->
                    <div class="flex flex-wrap items-center gap-1.5 p-2 bg-slate-100 dark:bg-slate-800 rounded-xl mb-3 border border-slate-200 dark:border-slate-700 text-xs">
                        <button type="button" onclick="insertTag('<b>', '</b>')" class="px-2.5 py-1 rounded bg-white dark:bg-slate-700 shadow-sm font-bold hover:bg-indigo-50">B</button>
                        <button type="button" onclick="insertTag('<i>', '</i>')" class="px-2.5 py-1 rounded bg-white dark:bg-slate-700 shadow-sm italic hover:bg-indigo-50">I</button>
                        <button type="button" onclick="insertTag('<h3>', '</h3>')" class="px-2.5 py-1 rounded bg-white dark:bg-slate-700 shadow-sm font-bold hover:bg-indigo-50">H3</button>
                        <button type="button" onclick="insertTag('<blockquote>', '</blockquote>')" class="px-2.5 py-1 rounded bg-white dark:bg-slate-700 shadow-sm hover:bg-indigo-50"><i class="fas fa-quote-left"></i> Trích dẫn</button>
                        <button type="button" onclick="insertTag('<ul>\n  <li>', '</li>\n</ul>')" class="px-2.5 py-1 rounded bg-white dark:bg-slate-700 shadow-sm hover:bg-indigo-50"><i class="fas fa-list-ul"></i> Danh sách</button>
                        <button type="button" onclick="insertTag('<p>', '</p>')" class="px-2.5 py-1 rounded bg-white dark:bg-slate-700 shadow-sm hover:bg-indigo-50">&lt;p&gt;</button>
                        <button type="button" onclick="insertTag('<img src=\'https://...\' alt=\'Mô tả ảnh\'>', '')" class="px-2.5 py-1 rounded bg-white dark:bg-slate-700 shadow-sm hover:bg-indigo-50"><i class="fas fa-image"></i> Chèn ảnh</button>
                        <button type="button" onclick="insertTag('<a href=\'https://...\'>', '</a>')" class="px-2.5 py-1 rounded bg-white dark:bg-slate-700 shadow-sm hover:bg-indigo-50"><i class="fas fa-link"></i> Link</button>
                    </div>

                    <textarea id="article-content" name="content" rows="16" required 
                              placeholder="Nhập nội dung bài viết đầy đủ với định dạng HTML chuẩn..." 
                              class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-mono text-slate-800 dark:text-slate-200 focus:outline-none focus:border-indigo-600"><?= htmlspecialchars($article['content']) ?></textarea>
                </div>

            </div>

            <!-- Cột phải: Xuất bản, Chuyên mục, Ảnh thumbnail, Thẻ tag (4 Cột) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Hộp Xuất Bản (Publish Box) -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider pb-3 border-b border-slate-100 dark:border-slate-800">
                        Cấu Hình Xuất Bản
                    </h3>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Trạng thái</label>
                        <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold focus:outline-none focus:border-indigo-600">
                            <option value="published" <?= $article['status'] === 'published' ? 'selected' : '' ?>>Đã xuất bản (Công khai)</option>
                            <option value="draft" <?= $article['status'] === 'draft' ? 'selected' : '' ?>>Lưu bản nháp (Ẩn)</option>
                        </select>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                        <label class="flex items-center space-x-2.5 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" <?= $article['is_featured'] ? 'checked' : '' ?> class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="font-bold text-slate-800 dark:text-slate-200">Đặt làm Tin Nổi Bật (Featured)</span>
                        </label>
                        <label class="flex items-center space-x-2.5 cursor-pointer">
                            <input type="checkbox" name="is_breaking" value="1" <?= $article['is_breaking'] ? 'checked' : '' ?> class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500">
                            <span class="font-bold text-rose-600 dark:text-rose-400">Đặt làm Tin Nóng (Breaking News)</span>
                        </label>
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition-colors flex items-center justify-center space-x-2">
                            <i class="fas fa-save"></i>
                            <span><?= $id > 0 ? 'Cập Nhật Bài Viết' : 'Xuất Bản Bài Viết' ?></span>
                        </button>
                    </div>
                </div>

                <!-- Chuyên Mục (Category) -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Chuyên Mục *
                    </label>
                    <select name="category_id" required class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold focus:outline-none focus:border-indigo-600">
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $article['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Ảnh Thumbnail Đại Diện -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        Ảnh Bìa Đại Diện
                    </label>

                    <?php if (!empty($article['thumbnail'])): ?>
                    <div class="h-32 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                        <img src="<?= htmlspecialchars(upload_url($article['thumbnail'])) ?>" alt="" class="w-full h-full object-cover">
                    </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">Cách 1: Tải ảnh từ máy tính</label>
                        <input type="file" name="thumbnail_file" accept="image/*" 
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">Cách 2: Hoặc dán URL ảnh trực tiếp</label>
                        <input type="text" name="thumbnail_url" value="<?= htmlspecialchars($article['thumbnail']) ?>" placeholder="https://images.unsplash.com/..." 
                               class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
                    </div>
                </div>

                <!-- Thẻ Từ Khóa (Tags) -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Thẻ Từ Khóa (Tags)
                    </label>
                    <input type="text" name="tags" value="<?= htmlspecialchars($article['tags']) ?>" placeholder="AI, Công nghệ, Khởi nghiệp..." 
                           class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
                    <span class="block text-[10px] text-slate-400 mt-1.5">Phân cách các từ khóa bằng dấu phẩy (,).</span>
                </div>

            </div>

        </div>
    </form>
</div>

<script>
function insertTag(openTag, closeTag) {
    const textarea = document.getElementById('article-content');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selected = textarea.value.substring(start, end);
    const replacement = openTag + selected + closeTag;
    textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
    textarea.focus();
    textarea.setSelectionRange(start + openTag.length, start + openTag.length + selected.length);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
