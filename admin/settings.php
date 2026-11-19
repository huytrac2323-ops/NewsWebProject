<?php
/**
 * V-NEWS CMS - Cài Đặt Hệ Thống & Tên Miền (System & Domain Settings)
 */
$page_title = 'Cài Đặt Hệ Thống';
require_once __DIR__ . '/includes/header.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Token bảo mật không hợp lệ!');
    } else {
        $fields = [
            'site_name', 'site_tagline', 'site_description', 'site_keywords',
            'primary_domain', 'contact_email', 'contact_hotline',
            'facebook_url', 'youtube_url', 'footer_copyright'
        ];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_setting($field, trim($_POST[$field]));
            }
        }

        set_flash('success', 'Đã lưu cài đặt hệ thống thành công!');
        header('Location: ' . url('admin/settings.php'));
        exit;
    }
}
?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between pb-6 mb-8 border-b border-slate-200 dark:border-slate-800">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white">Cài Đặt Hệ Thống & Tên Miền</h1>
            <p class="text-xs text-slate-400 mt-1">Cấu hình thông tin thương hiệu tòa soạn, SEO và tên miền chính thức.</p>
        </div>
    </div>

    <form action="" method="POST" class="space-y-8">
        <?= csrf_field() ?>

        <!-- KHỐI 1: TÊN MIỀN & SEO WEBSITE -->
        <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider pb-3 border-b border-slate-100 dark:border-slate-800 flex items-center">
                <i class="fas fa-globe text-emerald-500 mr-2"></i> Tên Miền Chính & Cấu Hình SEO
            </h3>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">
                    Tên Miền Chính Thức (Primary Domain)
                </label>
                <input type="text" name="primary_domain" value="<?= htmlspecialchars(get_setting('primary_domain', 'https://vnews247.vn')) ?>" 
                       placeholder="https://yourdomain.com" 
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 focus:outline-none focus:border-indigo-600">
                <span class="block text-[11px] text-slate-400 mt-1">
                    Điền tên miền bạn đăng ký (bao gồm https://). Tên miền này được dùng cho Sitemap XML, RSS Feed và thẻ chuẩn SEO Canonical URL.
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Tên Website / Tòa Soạn *</label>
                    <input type="text" name="site_name" value="<?= htmlspecialchars(get_setting('site_name', 'V-NEWS 24/7')) ?>" required 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Khẩu hiệu (Tagline)</label>
                    <input type="text" name="site_tagline" value="<?= htmlspecialchars(get_setting('site_tagline', 'Thời sự - Công nghệ - Kinh doanh')) ?>" 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Mô tả SEO Meta Description</label>
                <textarea name="site_description" rows="3" 
                          class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600"><?= htmlspecialchars(get_setting('site_description')) ?></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Từ khóa SEO (Keywords)</label>
                <input type="text" name="site_keywords" value="<?= htmlspecialchars(get_setting('site_keywords')) ?>" 
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
            </div>
        </div>

        <!-- KHỐI 2: THÔNG TIN LIÊN HỆ & MẠNG XÃ HỘI -->
        <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider pb-3 border-b border-slate-100 dark:border-slate-800 flex items-center">
                <i class="fas fa-address-book text-indigo-500 mr-2"></i> Liên Hệ & Mạng Xã Hội
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Email Tòa Soạn</label>
                    <input type="email" name="contact_email" value="<?= htmlspecialchars(get_setting('contact_email')) ?>" 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Đường Dây Nóng (Hotline)</label>
                    <input type="text" name="contact_hotline" value="<?= htmlspecialchars(get_setting('contact_hotline')) ?>" 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Facebook Page URL</label>
                    <input type="text" name="facebook_url" value="<?= htmlspecialchars(get_setting('facebook_url')) ?>" 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Kênh YouTube URL</label>
                    <input type="text" name="youtube_url" value="<?= htmlspecialchars(get_setting('youtube_url')) ?>" 
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Dòng chữ Bản quyền chân trang (Footer Copyright)</label>
                <input type="text" name="footer_copyright" value="<?= htmlspecialchars(get_setting('footer_copyright')) ?>" 
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs focus:outline-none focus:border-indigo-600">
            </div>
        </div>

        <button type="submit" class="px-8 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-lg shadow-indigo-600/30 transition-all flex items-center space-x-2">
            <i class="fas fa-save"></i>
            <span>Lưu Toàn Bộ Cài Đặt</span>
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
