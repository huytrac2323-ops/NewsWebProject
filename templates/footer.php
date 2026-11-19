<?php
/**
 * V-NEWS CMS - Editorial Footer Template
 */
global $pdo;

$footer_categories = [];
try {
    $stmt = $pdo->query("SELECT name, slug FROM categories ORDER BY sort_order ASC LIMIT 6");
    $footer_categories = $stmt->fetchAll();
} catch (Exception $e) {
    $footer_categories = [];
}
?>
        </div> <!-- End max-w-7xl main container -->
    </main>

    <!-- EDITORIAL FOOTER (CHÂN TRANG BÁO IN TRUYỀN THỐNG) -->
    <footer class="bg-white dark:bg-[#0c0f14] border-t-4 border-double border-neutral-900 dark:border-neutral-700 pt-12 pb-10 text-neutral-600 dark:text-neutral-400 text-xs mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Top Footer: Brand Masthead & Subtitle -->
            <div class="flex flex-col sm:flex-row items-baseline justify-between pb-6 mb-8 border-b border-neutral-200 dark:border-neutral-800 gap-4">
                <div>
                    <span class="font-headline text-3xl font-black tracking-tight text-neutral-900 dark:text-white">
                        THE V-NEWS
                    </span>
                    <span class="block text-[11px] font-editorial italic text-neutral-500 mt-0.5">
                        Nhật báo Tin tức, Công nghệ & Đời sống số độc lập.
                    </span>
                </div>
                <div class="text-[11px] font-mono text-neutral-400 break-words">
                    ISSN 2026-8899 • Giấy phép xuất bản số 128/GP-BTTTT
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 pb-10 border-b border-neutral-200 dark:border-neutral-800">
                <!-- Col 1: Categories -->
                <div>
                    <h5 class="font-bold text-[11px] uppercase tracking-wider text-neutral-900 dark:text-white mb-3">
                        Chuyên Mục
                    </h5>
                    <ul class="space-y-2 text-xs">
                        <?php foreach ($footer_categories as $fcat): ?>
                        <li>
                            <a href="<?= url('category.php?slug=' . urlencode($fcat['slug'])) ?>" class="hover:text-red-700 dark:hover:text-red-400 transition-colors">
                                <?= htmlspecialchars($fcat['name']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Col 2: Editorial & Company -->
                <div>
                    <h5 class="font-bold text-[11px] uppercase tracking-wider text-neutral-900 dark:text-white mb-3">
                        Tòa Soạn & Pháp Lý
                    </h5>
                    <ul class="space-y-2 text-xs">
                        <li><a href="<?= url('page.php?slug=gioi-thieu') ?>" class="hover:text-red-700 transition-colors">Về Ban Biên Tập</a></li>
                        <li><a href="<?= url('page.php?slug=chinh-sach-bao-mat') ?>" class="hover:text-red-700 transition-colors">Quy Chuẩn Đạo Đức Báo Chí</a></li>
                        <li><a href="<?= url('contact.php') ?>" class="hover:text-red-700 transition-colors">Liên Hệ Đặt Quảng Cáo</a></li>
                        <li><a href="<?= url('admin/domain_guide.php') ?>" class="text-red-700 dark:text-red-400 font-bold hover:underline">Hướng Dẫn Tên Miền</a></li>
                    </ul>
                </div>

                <!-- Col 3: Syndication & Feeds -->
                <div>
                    <h5 class="font-bold text-[11px] uppercase tracking-wider text-neutral-900 dark:text-white mb-3">
                        Nguồn Dữ Liệu
                    </h5>
                    <ul class="space-y-2 text-xs">
                        <li><a href="<?= url('rss.xml.php') ?>" target="_blank" class="hover:text-red-700 transition-colors flex items-center"><i class="fas fa-rss mr-1.5 text-amber-600"></i> RSS Feed 2.0</a></li>
                        <li><a href="<?= url('sitemap.xml.php') ?>" target="_blank" class="hover:text-red-700 transition-colors flex items-center"><i class="fas fa-sitemap mr-1.5"></i> Google Sitemap XML</a></li>
                        <li><a href="<?= url('admin/index.php') ?>" class="hover:text-red-700 transition-colors">Cổng Quản Trị CMS</a></li>
                    </ul>
                </div>

                <!-- Col 4: Contact Hotline -->
                <div>
                    <h5 class="font-bold text-[11px] uppercase tracking-wider text-neutral-900 dark:text-white mb-3">
                        Đường Dây Nóng
                    </h5>
                    <p class="text-xs text-neutral-600 dark:text-neutral-400 mb-2">
                        Tiếp nhận phản ánh, khiếu nại và cung cấp thông tin thời sự 24/7.
                    </p>
                    <p class="font-mono font-bold text-sm text-neutral-900 dark:text-white">
                        <i class="fas fa-phone-alt text-red-700 mr-1.5"></i> <?= htmlspecialchars(get_setting('contact_hotline', '1900 6868')) ?>
                    </p>
                    <p class="text-[11px] text-neutral-400 mt-1">
                        Email: <?= htmlspecialchars(get_setting('contact_email', 'toasoan@vnews247.vn')) ?>
                    </p>
                </div>
            </div>

            <!-- Bottom: Copyright & Attribution -->
            <div class="pt-6 flex flex-col sm:flex-row justify-between items-center text-[11px] text-neutral-500 gap-2">
                <p><?= htmlspecialchars(get_setting('footer_copyright', '© 2026 THE V-NEWS. Mọi quyền được bảo lưu.')) ?></p>
                <div class="flex items-center space-x-4">
                    <span>Phát triển bằng Modern PHP & Clean Design System</span>
                    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="hover:text-neutral-900 dark:hover:text-white underline font-semibold">
                        Về đầu trang &uarr;
                    </button>
                </div>
            </div>
        </div>
    </footer>

    <!-- Main JavaScript -->
    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
