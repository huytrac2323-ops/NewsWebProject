<?php
/**
 * V-NEWS CMS - Admin Footer Shell
 * Compliant with AntiSlopUI Design Contract (DESIGN_SYSTEM.md)
 */
?>
        </main>

        <!-- ADMIN FOOTER (Clean 1px hairline border, subtle typography) -->
        <footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-6 py-3.5 text-xs text-slate-500 dark:text-slate-400 flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center space-x-2">
                <span class="font-medium text-slate-700 dark:text-slate-300">V-NEWS CMS</span>
                <span class="text-slate-300 dark:text-slate-700">•</span>
                <span>Phiên bản Biên tập 2.0</span>
            </div>
            <div class="flex items-center space-x-3 text-[11px] text-slate-400">
                <span>Hạ tầng PHP 8.x + MySQL</span>
                <span class="text-slate-300 dark:text-slate-700">•</span>
                <span class="tabular-nums">Thời gian hệ thống: <?= date('d/m/Y H:i') ?></span>
            </div>
        </footer>

    </div>

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/60 z-30 hidden md:hidden transition-opacity"></div>

    <!-- Scripts -->
    <script src="<?= asset('js/main.js') ?>"></script>
    <script>
        // Clean Mobile Sidebar Controller
        const mobileToggleBtn = document.getElementById('mobile-toggle-btn');
        const closeSidebarBtn = document.getElementById('close-sidebar-btn');
        const adminSidebar = document.getElementById('admin-sidebar');
        const sidebarBackdrop = document.getElementById('sidebar-backdrop');

        function openSidebar() {
            if (!adminSidebar || !sidebarBackdrop) return;
            adminSidebar.classList.remove('hidden');
            adminSidebar.classList.add('fixed', 'inset-y-0', 'left-0');
            sidebarBackdrop.classList.remove('hidden');
        }

        function closeSidebar() {
            if (!adminSidebar || !sidebarBackdrop) return;
            adminSidebar.classList.add('hidden');
            adminSidebar.classList.remove('fixed', 'inset-y-0', 'left-0');
            sidebarBackdrop.classList.add('hidden');
        }

        if (mobileToggleBtn) mobileToggleBtn.addEventListener('click', openSidebar);
        if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', closeSidebar);
        if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', closeSidebar);
    </script>
</body>
</html>