<?php
/**
 * V-NEWS CMS - Liên Hệ Ban Biên Tập (Editorial Contact)
 */
require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_contact'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash('error', 'Lỗi xác thực CSRF!');
    } else {
        $name = sanitize($_POST['name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $subject = sanitize($_POST['subject'] ?? '');
        $message = sanitize($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            set_flash('error', 'Vui lòng nhập đầy đủ các trường thông tin.');
        } else {
            set_flash('success', 'Thư của bạn đã được chuyển đến Ban biên tập V-NEWS. Xin trân trọng cảm ơn!');
            header('Location: ' . url('contact.php'));
            exit;
        }
    }
}

$page_title = 'Liên Hệ Ban Biên Tập & Tòa Soạn';
require_once ROOT_PATH . '/templates/header.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-10 pb-6 border-b-2 border-neutral-900 dark:border-neutral-200">
        <div class="text-[11px] font-bold uppercase tracking-wider text-red-700 dark:text-red-400 mb-2">
            Đường Dây Nóng & Liên Hệ Tòa Soạn
        </div>
        <h1 class="font-headline text-3xl sm:text-5xl font-black text-neutral-900 dark:text-white tracking-tight">
            Liên Hệ Ban Biên Tập
        </h1>
        <p class="font-editorial text-sm sm:text-base text-neutral-600 dark:text-neutral-400 mt-2 leading-relaxed">
            Tiếp nhận bài vở cộng tác, phản ánh thời sự nóng và liên hệ hợp tác truyền thông.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        <div class="space-y-6 text-xs text-neutral-600 dark:text-neutral-400">
            <div class="p-5 border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#0c0f14]">
                <h4 class="font-headline font-bold text-sm text-neutral-900 dark:text-white mb-2">Trụ sở chính</h4>
                <p class="leading-relaxed">Tầng 12, Tòa nhà Báo chí & Công nghệ, Hà Nội</p>
            </div>
            <div class="p-5 border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#0c0f14]">
                <h4 class="font-headline font-bold text-sm text-neutral-900 dark:text-white mb-2">Đường dây nóng</h4>
                <p class="font-mono text-neutral-900 dark:text-white font-bold text-sm mb-1"><?= htmlspecialchars(get_setting('contact_hotline', '1900 6868')) ?></p>
                <p class="text-[11px] text-neutral-400">Trực 24/7 tiếp nhận nguồn tin độc quyền</p>
            </div>
            <div class="p-5 border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#0c0f14]">
                <h4 class="font-headline font-bold text-sm text-neutral-900 dark:text-white mb-2">Thư điện tử</h4>
                <p class="font-mono text-neutral-900 dark:text-white"><?= htmlspecialchars(get_setting('contact_email', 'toasoan@vnews247.vn')) ?></p>
            </div>
        </div>

        <div class="md:col-span-2 p-6 sm:p-8 border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-[#0c0f14]">
            <h3 class="font-headline text-lg font-bold text-neutral-900 dark:text-white mb-6 uppercase tracking-wider">
                Gửi Thư Trực Tuyến
            </h3>
            <form action="<?= url('contact.php') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-600 dark:text-neutral-400 mb-1">Họ tên *</label>
                        <input type="text" name="name" required class="w-full px-3 py-2 bg-neutral-50 dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-xs focus:outline-none focus:border-neutral-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-600 dark:text-neutral-400 mb-1">Email *</label>
                        <input type="email" name="email" required class="w-full px-3 py-2 bg-neutral-50 dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-xs focus:outline-none focus:border-neutral-900">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-600 dark:text-neutral-400 mb-1">Tiêu đề</label>
                    <input type="text" name="subject" class="w-full px-3 py-2 bg-neutral-50 dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-xs focus:outline-none focus:border-neutral-900">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-600 dark:text-neutral-400 mb-1">Nội dung *</label>
                    <textarea name="message" rows="5" required class="w-full px-3 py-2 bg-neutral-50 dark:bg-neutral-900 border border-neutral-300 dark:border-neutral-700 text-xs focus:outline-none focus:border-neutral-900"></textarea>
                </div>
                <button type="submit" name="send_contact" value="1" class="px-6 py-3 bg-neutral-900 hover:bg-neutral-800 text-white font-bold text-xs uppercase tracking-widest transition-colors">
                    Gửi Thư Đến Tòa Soạn
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/templates/footer.php'; ?>
