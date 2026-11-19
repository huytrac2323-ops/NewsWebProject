<?php
/**
 * V-NEWS CMS - Quản Lý Người Dùng & Phân Quyền (Users Management)
 */
$page_title = 'Quản Lý Người Dùng';
require_once __DIR__ . '/includes/header.php';
require_admin(); // Bắt buộc quyền Quản trị tối cao (Admin)

// 1. Thêm người dùng mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token bảo mật không hợp lệ!');
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = in_array($_POST['role'] ?? '', ['admin', 'editor', 'author']) ? $_POST['role'] : 'editor';
        $bio = trim($_POST['bio'] ?? '');

        if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
            set_flash('error', 'Vui lòng điền đầy đủ các thông tin bắt buộc.');
        } else {
            try {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, role, bio) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hash, $full_name, $role, $bio]);
                set_flash('success', 'Đã tạo tài khoản thành viên mới thành công!');
                header('Location: ' . url('admin/users.php'));
                exit;
            } catch (Exception $e) {
                set_flash('error', 'Lỗi tạo tài khoản (Tên đăng nhập hoặc Email có thể đã tồn tại): ' . $e->getMessage());
            }
        }
    }
}

// 2. Xóa người dùng (Không cho xóa chính mình)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $del_id = (int)$_GET['id'];
    if ($del_id === (int)$_SESSION['user_id']) {
        set_flash('error', 'Bạn không thể tự xóa tài khoản đang đăng nhập của chính mình!');
    } elseif (verify_csrf_token($_GET['token'] ?? '')) {
        try {
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$del_id]);
            set_flash('success', 'Đã xóa người dùng thành công!');
        } catch (Exception $e) {
            set_flash('error', 'Lỗi khi xóa người dùng: ' . $e->getMessage());
        }
    }
    header('Location: ' . url('admin/users.php'));
    exit;
}

// 3. Lấy danh sách thành viên
$users = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();
?>

<div class="flex items-center justify-between pb-6 mb-8 border-b border-slate-200 dark:border-slate-800">
    <div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white">Quản Lý Đội Ngũ Tòa Soạn</h1>
        <p class="text-xs text-slate-400 mt-1">Phân quyền tài khoản Quản trị viên (Admin) và Biên tập viên (Editor).</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    
    <!-- Form Thêm Người Dùng (4 Cột) -->
    <div class="lg:col-span-4 bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm self-start">
        <h3 class="text-base font-black text-slate-900 dark:text-white mb-4 pb-2 border-b border-slate-100 dark:border-slate-800">
            Thêm Thành Viên Mới
        </h3>

        <form action="<?= url('admin/users.php') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Họ và Tên *</label>
                <input type="text" name="full_name" required placeholder="Nguyễn Văn B" 
                       class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Tên đăng nhập *</label>
                <input type="text" name="username" required placeholder="editor_minh" 
                       class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Email *</label>
                <input type="email" name="email" required placeholder="minh@vnews.vn" 
                       class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Mật khẩu ban đầu *</label>
                <input type="password" name="password" required placeholder="••••••••" 
                       class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Vai Trò / Phân Quyền</label>
                <select name="role" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold focus:outline-none focus:border-indigo-600">
                    <option value="editor">Biên Tập Viên (Đăng bài, duyệt comment)</option>
                    <option value="admin">Quản Trị Viên Cấp Cao (Toàn quyền hệ thống)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Tiểu sử ngắn</label>
                <textarea name="bio" rows="2" placeholder="Chuyên gia phân tích tài chính..." 
                          class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600"></textarea>
            </div>

            <button type="submit" name="add_user" value="1" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition-colors">
                <i class="fas fa-user-plus mr-1"></i> Tạo Tài Khoản
            </button>
        </form>
    </div>

    <!-- Bảng Danh Sách Người Dùng (8 Cột) -->
    <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Họ và tên</th>
                        <th class="py-3 px-4">Username / Email</th>
                        <th class="py-3 px-4 text-center">Vai trò</th>
                        <th class="py-3 px-4">Ngày tạo</th>
                        <th class="py-3 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-3 px-4 font-bold text-slate-900 dark:text-white flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">
                                <?= mb_substr($u['full_name'], 0, 1) ?>
                            </div>
                            <span><?= htmlspecialchars($u['full_name']) ?></span>
                        </td>
                        <td class="py-3 px-4 font-mono">
                            <span class="text-slate-800 dark:text-slate-200 block font-bold"><?= htmlspecialchars($u['username']) ?></span>
                            <span class="text-[11px] text-slate-400"><?= htmlspecialchars($u['email']) ?></span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider <?= $u['role'] === 'admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300' : 'bg-sky-100 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300' ?>">
                                <?= $u['role'] ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-400">
                            <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                            <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                            <a href="?action=delete&id=<?= $u['id'] ?>&token=<?= csrf_token() ?>" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa thành viên này?')" 
                               class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-colors" title="Xóa tài khoản">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                            <?php else: ?>
                            <span class="text-[10px] text-slate-400 font-bold px-2 py-1 rounded bg-slate-100 dark:bg-slate-800">Đang đăng nhập</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
