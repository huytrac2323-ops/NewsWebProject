<?php
/**
 * V-NEWS CMS - Quản Lý Bình Luận (Comments Moderation)
 */
$page_title = 'Quản Lý Bình Luận';
require_once __DIR__ . '/includes/header.php';

// 1. Xử lý thao tác trạng thái (Approve, Spam, Delete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $com_id = (int)$_GET['id'];

    if (verify_csrf_token($_GET['token'] ?? '')) {
        try {
            if ($action === 'approve') {
                $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id = ?")->execute([$com_id]);
                set_flash('success', 'Đã duyệt bình luận hiển thị công khai!');
            } elseif ($action === 'spam') {
                $pdo->prepare("UPDATE comments SET status = 'spam' WHERE id = ?")->execute([$com_id]);
                set_flash('warning', 'Đã đánh dấu bình luận là Spam!');
            } elseif ($action === 'delete') {
                $pdo->prepare("DELETE FROM comments WHERE id = ?")->execute([$com_id]);
                set_flash('success', 'Đã xóa bình luận thành công!');
            }
        } catch (Exception $e) {
            set_flash('error', 'Lỗi thực thi: ' . $e->getMessage());
        }
    }
    header('Location: ' . url('admin/comments.php'));
    exit;
}

// 2. Lấy danh sách bình luận
$comments = [];
try {
    $stmt = $pdo->query("
        SELECT c.*, a.title as article_title, a.slug as article_slug 
        FROM comments c
        LEFT JOIN articles a ON c.article_id = a.id
        ORDER BY c.id DESC
    ");
    $comments = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<div class="flex items-center justify-between pb-6 mb-8 border-b border-slate-200 dark:border-slate-800">
    <div>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white">Kiểm Duyệt Bình Luận</h1>
        <p class="text-xs text-slate-400 mt-1">Quản lý phản hồi và ý kiến độc giả trên toàn hệ thống.</p>
    </div>
</div>

<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 uppercase tracking-wider font-bold border-b border-slate-200 dark:border-slate-800">
                <tr>
                    <th class="py-3 px-4">Độc giả</th>
                    <th class="py-3 px-4">Nội dung bình luận</th>
                    <th class="py-3 px-4">Bài viết liên quan</th>
                    <th class="py-3 px-4 text-center">Trạng thái</th>
                    <th class="py-3 px-4">Thời gian</th>
                    <th class="py-3 px-4 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php if (empty($comments)): ?>
                <tr>
                    <td colspan="6" class="py-12 text-center text-slate-400 italic">
                        Chưa có bình luận nào trên hệ thống.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($comments as $c): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="font-bold text-slate-900 dark:text-white block"><?= htmlspecialchars($c['author_name']) ?></span>
                            <span class="text-[11px] text-slate-400 font-mono"><?= htmlspecialchars($c['author_email']) ?></span>
                        </td>
                        <td class="py-3 px-4 max-w-sm">
                            <p class="text-slate-700 dark:text-slate-300 line-clamp-2 leading-relaxed">
                                <?= htmlspecialchars($c['content']) ?>
                            </p>
                        </td>
                        <td class="py-3 px-4 max-w-xs">
                            <a href="<?= url('article.php?slug=' . urlencode($c['article_slug'] ?? '')) ?>" target="_blank" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline line-clamp-1">
                                <?= htmlspecialchars($c['article_title'] ?? 'Bài viết không xác định') ?>
                            </a>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <?php if ($c['status'] === 'approved'): ?>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                                Đã Duyệt
                            </span>
                            <?php elseif ($c['status'] === 'spam'): ?>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                                Spam
                            </span>
                            <?php else: ?>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300">
                                Chờ Duyệt
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-slate-400 whitespace-nowrap">
                            <?= time_ago($c['created_at']) ?>
                        </td>
                        <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
                            <?php if ($c['status'] !== 'approved'): ?>
                            <a href="?action=approve&id=<?= $c['id'] ?>&token=<?= csrf_token() ?>" class="p-2 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-colors" title="Duyệt đăng">
                                <i class="fas fa-check"></i>
                            </a>
                            <?php endif; ?>

                            <?php if ($c['status'] !== 'spam'): ?>
                            <a href="?action=spam&id=<?= $c['id'] ?>&token=<?= csrf_token() ?>" class="p-2 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white transition-colors" title="Đánh dấu Spam">
                                <i class="fas fa-ban"></i>
                            </a>
                            <?php endif; ?>

                            <a href="?action=delete&id=<?= $c['id'] ?>&token=<?= csrf_token() ?>" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận này?')" 
                               class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-colors" title="Xóa">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
