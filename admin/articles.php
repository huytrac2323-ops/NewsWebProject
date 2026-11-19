<?php
/**
 * V-NEWS CMS - Danh Sách & Quản Lý Bài Viết (Articles Management)
 */
$page_title = 'Quản Lý Bài Viết';
require_once __DIR__ . '/includes/header.php';

// 1. Xử lý Xóa bài viết
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $del_id = (int)$_GET['id'];
    if (verify_csrf_token($_GET['token'] ?? '')) {
        try {
            $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
            $stmt->execute([$del_id]);
            set_flash('success', 'Đã xóa bài viết thành công!');
        } catch (Exception $e) {
            set_flash('error', 'Lỗi khi xóa bài viết: ' . $e->getMessage());
        }
    } else {
        set_flash('error', 'Token bảo mật không hợp lệ!');
    }
    header('Location: ' . url('admin/articles.php'));
    exit;
}

// 2. Xử lý Đổi trạng thái Featured / Breaking nhanh
if (isset($_GET['action']) && $_GET['action'] === 'toggle_featured' && isset($_GET['id'])) {
    $toggle_id = (int)$_GET['id'];
    if (verify_csrf_token($_GET['token'] ?? '')) {
        try {
            $pdo->prepare("UPDATE articles SET is_featured = CASE WHEN is_featured = 1 THEN 0 ELSE 1 END WHERE id = ?")->execute([$toggle_id]);
            set_flash('success', 'Đã cập nhật trạng thái nổi bật!');
        } catch (Exception $e) {}
    }
    header('Location: ' . url('admin/articles.php'));
    exit;
}

// 3. Bộ lọc & Phân trang
$search = trim($_GET['search'] ?? '');
$cat_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$status_filter = trim($_GET['status'] ?? '');

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

// Xây dựng điều kiện truy vấn
$where = ["1=1"];
$params = [];

if (!empty($search)) {
    $where[] = "(a.title LIKE ? OR a.tags LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($cat_filter > 0) {
    $where[] = "a.category_id = ?";
    $params[] = $cat_filter;
}

if (!empty($status_filter)) {
    $where[] = "a.status = ?";
    $params[] = $status_filter;
}

$where_clause = implode(" AND ", $where);

// Đếm tổng số bài viết
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM articles a WHERE $where_clause");
$stmtCount->execute($params);
$total_rows = (int)$stmtCount->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// Lấy danh sách bài viết
$sql = "
    SELECT a.*, c.name as category_name, c.color as category_color, u.full_name as author_name 
    FROM articles a
    LEFT JOIN categories c ON a.category_id = c.id
    LEFT JOIN users u ON a.author_id = u.id
    WHERE $where_clause
    ORDER BY a.id DESC 
    LIMIT $limit OFFSET $offset
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$articles = $stmt->fetchAll();

// Lấy danh mục để lọc
$all_categories = $pdo->query("SELECT id, name FROM categories ORDER BY sort_order ASC")->fetchAll();
?>

<!-- Header Actions -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-200 dark:border-slate-800 gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white">Quản Lý Bài Viết</h1>
        <p class="text-xs text-slate-400 mt-1">Tổng cộng có <strong><?= $total_rows ?></strong> bài viết trong hệ thống</p>
    </div>
    <a href="<?= url('admin/article_edit.php') ?>" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition-colors inline-flex items-center self-start sm:self-auto">
        <i class="fas fa-plus mr-2"></i> Viết Bài Mới
    </a>
</div>

<!-- Form Tìm kiếm & Lọc -->
<form action="<?= url('admin/articles.php') ?>" method="GET" class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm mb-6 flex flex-wrap gap-4 items-center">
    <div class="flex-1 min-w-[200px]">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm tiêu đề hoặc thẻ tag..." 
               class="w-full px-3.5 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
    </div>

    <div class="w-48">
        <select name="category" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
            <option value="0">Tất cả chuyên mục</option>
            <?php foreach ($all_categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $cat_filter == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="w-40">
        <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
            <option value="">Tất cả trạng thái</option>
            <option value="published" <?= $status_filter === 'published' ? 'selected' : '' ?>>Đã xuất bản</option>
            <option value="draft" <?= $status_filter === 'draft' ? 'selected' : '' ?>>Bản nháp</option>
        </select>
    </div>

    <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 dark:bg-slate-700 text-white font-bold text-xs hover:bg-slate-800 transition-colors">
        <i class="fas fa-filter mr-1"></i> Lọc
    </button>
    
    <?php if (!empty($search) || $cat_filter > 0 || !empty($status_filter)): ?>
    <a href="<?= url('admin/articles.php') ?>" class="text-xs text-rose-500 font-bold hover:underline">Xóa bộ lọc</a>
    <?php endif; ?>
</form>

<!-- Bảng danh sách bài viết -->
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-200 dark:border-slate-800">
                <tr>
                    <th class="py-3 px-4">Ảnh</th>
                    <th class="py-3 px-4">Tiêu đề bài viết</th>
                    <th class="py-3 px-4">Chuyên mục</th>
                    <th class="py-3 px-4 text-center">Nổi bật</th>
                    <th class="py-3 px-4 text-center">Lượt xem</th>
                    <th class="py-3 px-4 text-center">Trạng thái</th>
                    <th class="py-3 px-4">Ngày đăng</th>
                    <th class="py-3 px-4 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php if (empty($articles)): ?>
                <tr>
                    <td colspan="8" class="py-12 text-center text-slate-400 italic">
                        Không tìm thấy bài viết nào phù hợp.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($articles as $art): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-3 px-4">
                            <div class="w-14 h-10 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 flex-shrink-0">
                                <img src="<?= htmlspecialchars(upload_url($art['thumbnail'])) ?>" alt="" class="w-full h-full object-cover">
                            </div>
                        </td>
                        <td class="py-3 px-4 max-w-sm">
                            <a href="<?= url('article.php?slug=' . urlencode($art['slug'])) ?>" target="_blank" class="font-bold text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 line-clamp-1">
                                <?= htmlspecialchars($art['title']) ?>
                            </a>
                            <span class="text-[11px] text-slate-400 block mt-0.5">Tác giả: <?= htmlspecialchars($art['author_name'] ?? 'V-NEWS') ?></span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider badge-<?= htmlspecialchars($art['category_color'] ?? 'indigo') ?>">
                                <?= htmlspecialchars($art['category_name']) ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="?action=toggle_featured&id=<?= $art['id'] ?>&token=<?= csrf_token() ?>" 
                               class="text-sm <?= $art['is_featured'] ? 'text-amber-500 hover:text-amber-600' : 'text-slate-300 dark:text-slate-600 hover:text-amber-400' ?>" title="Bật/Tắt nổi bật">
                                <i class="fas fa-star"></i>
                            </a>
                        </td>
                        <td class="py-3 px-4 text-center font-mono font-semibold text-slate-600 dark:text-slate-300">
                            <?= number_format($art['views_count']) ?>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $art['status'] === 'published' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-slate-200 text-slate-700' ?>">
                                <?= $art['status'] === 'published' ? 'Đã đăng' : 'Bản nháp' ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                            <?= date('d/m/Y', strtotime($art['created_at'])) ?>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
                            <a href="<?= url('admin/article_edit.php?id=' . $art['id']) ?>" class="p-2 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= url('article.php?slug=' . urlencode($art['slug'])) ?>" target="_blank" class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 transition-colors" title="Xem bài">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="?action=delete&id=<?= $art['id'] ?>&token=<?= csrf_token() ?>" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa bài viết này không? Hành động này không thể hoàn tác!')" 
                               class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-colors" title="Xóa bài viết">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <?php if ($total_pages > 1): ?>
    <div class="p-4 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center text-xs">
        <span class="text-slate-400">Trang <?= $page ?> / <?= $total_pages ?></span>
        <div class="flex space-x-1">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= $cat_filter ?>&status=<?= urlencode($status_filter) ?>" 
               class="w-8 h-8 rounded-lg flex items-center justify-center font-bold <?= $i == $page ? 'bg-indigo-600 text-white' : 'border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
