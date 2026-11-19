<?php
/**
 * V-NEWS CMS - Quản Lý Chuyên Mục (Categories Management)
 */
$page_title = 'Quản Lý Chuyên Mục';
require_once __DIR__ . '/includes/header.php';

// 1. Xử lý Thêm / Sửa chuyên mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token bảo mật không hợp lệ!');
    } else {
        $cat_id = (int)($_POST['category_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        if (empty($slug)) $slug = slugify($name);
        $description = trim($_POST['description'] ?? '');
        $color = trim($_POST['color'] ?? 'indigo');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if (empty($name)) {
            set_flash('error', 'Tên chuyên mục không được để trống!');
        } else {
            try {
                if ($cat_id > 0) {
                    $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, color = ?, sort_order = ? WHERE id = ?");
                    $stmt->execute([$name, $slug, $description, $color, $sort_order, $cat_id]);
                    set_flash('success', 'Đã cập nhật chuyên mục thành công!');
                } else {
                    $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, color, sort_order) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $slug, $description, $color, $sort_order]);
                    set_flash('success', 'Đã thêm chuyên mục mới thành công!');
                }
                header('Location: ' . url('admin/categories.php'));
                exit;
            } catch (Exception $e) {
                set_flash('error', 'Lỗi lưu chuyên mục (Slug có thể đã bị trùng): ' . $e->getMessage());
            }
        }
    }
}

// 2. Xử lý Xóa chuyên mục
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $del_id = (int)$_GET['id'];
    if (verify_csrf_token($_GET['token'] ?? '')) {
        try {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$del_id]);
            set_flash('success', 'Đã xóa chuyên mục thành công!');
        } catch (Exception $e) {
            set_flash('error', 'Lỗi khi xóa: ' . $e->getMessage());
        }
    }
    header('Location: ' . url('admin/categories.php'));
    exit;
}

// 3. Lấy thông tin chỉnh sửa nếu có `?edit=id`
$edit_cat = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ? LIMIT 1");
    $stmt->execute([(int)$_GET['edit']]);
    $edit_cat = $stmt->fetch();
}

// 4. Lấy danh sách tất cả chuyên mục kèm số lượng bài
$categories = [];
try {
    $stmt = $pdo->query("
        SELECT c.*, COUNT(a.id) as articles_count 
        FROM categories c
        LEFT JOIN articles a ON c.id = a.category_id
        GROUP BY c.id
        ORDER BY c.sort_order ASC, c.id ASC
    ");
    $categories = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<div class="flex items-center justify-between pb-6 mb-8 border-b border-slate-200 dark:border-slate-800">
    <div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white">Quản Lý Chuyên Mục</h1>
        <p class="text-xs text-slate-400 mt-1">Phân loại tin tức và bài viết theo từng chủ đề nội dung.</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Cột trái: Form Thêm / Sửa Chuyên Mục (4 Cột) -->
    <div class="lg:col-span-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm self-start">
        <h3 class="text-base font-black text-slate-900 dark:text-white mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
            <?= $edit_cat ? 'Chỉnh Sửa Chuyên Mục' : 'Thêm Chuyên Mục Mới' ?>
        </h3>

        <form action="<?= url('admin/categories.php') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="category_id" value="<?= $edit_cat['id'] ?? 0 ?>">

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Tên Chuyên Mục *</label>
                <input type="text" id="title-input" name="name" value="<?= htmlspecialchars($edit_cat['name'] ?? '') ?>" required 
                       placeholder="Ví dụ: Công nghệ số" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Slug (Đường dẫn SEO)</label>
                <input type="text" id="slug-input" name="slug" value="<?= htmlspecialchars($edit_cat['slug'] ?? '') ?>" 
                       placeholder="tu-dong-sinh-slug" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Màu Sắc Huy Hiệu</label>
                <select name="color" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
                    <?php 
                    $colors = ['indigo' => 'Chàm (Indigo)', 'purple' => 'Tím (Purple)', 'emerald' => 'Xanh lục (Emerald)', 'amber' => 'Hổ phách (Amber)', 'rose' => 'Đỏ hồng (Rose)'];
                    foreach ($colors as $k => $v): 
                    ?>
                    <option value="<?= $k ?>" <?= ($edit_cat['color'] ?? 'indigo') === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Mô Tả Ngắn</label>
                <textarea name="description" rows="3" placeholder="Mô tả tóm tắt chủ đề của chuyên mục..." 
                          class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600"><?= htmlspecialchars($edit_cat['description'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Thứ Tự Sắp Xếp</label>
                <input type="number" name="sort_order" value="<?= $edit_cat['sort_order'] ?? 0 ?>" 
                       class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
            </div>

            <div class="pt-2 flex items-center space-x-2">
                <button type="submit" name="save_category" value="1" class="flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition-colors">
                    <?= $edit_cat ? 'Lưu Thay Đổi' : 'Thêm Chuyên Mục' ?>
                </button>
                <?php if ($edit_cat): ?>
                <a href="<?= url('admin/categories.php') ?>" class="px-3 py-2.5 rounded-xl bg-slate-200 dark:bg-slate-700 text-xs font-bold hover:bg-slate-300">
                    Hủy
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Cột phải: Danh Sách Chuyên Mục (8 Cột) -->
    <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Tên chuyên mục</th>
                        <th class="py-3 px-4">Slug</th>
                        <th class="py-3 px-4">Huy hiệu</th>
                        <th class="py-3 px-4 text-center">Số bài</th>
                        <th class="py-3 px-4 text-center">Thứ tự</th>
                        <th class="py-3 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($categories as $cat): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">
                            <?= htmlspecialchars($cat['name']) ?>
                        </td>
                        <td class="py-3 px-4 font-mono text-[11px] text-slate-400">
                            <?= htmlspecialchars($cat['slug']) ?>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider badge-<?= htmlspecialchars($cat['color']) ?>">
                                <?= htmlspecialchars($cat['color']) ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center font-bold">
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                <?= $cat['articles_count'] ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center font-mono text-slate-400">
                            <?= $cat['sort_order'] ?>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                            <a href="?edit=<?= $cat['id'] ?>" class="p-2 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?action=delete&id=<?= $cat['id'] ?>&token=<?= csrf_token() ?>" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa chuyên mục này không? Các bài viết liên quan có thể bị ảnh hưởng!')" 
                               class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-colors" title="Xóa">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
