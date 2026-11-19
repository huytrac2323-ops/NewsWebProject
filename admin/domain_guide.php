<?php
/**
 * V-NEWS CMS - Hướng Dẫn Đăng Ký & Gắn Tên Miền Riêng (Domain Setup Guide)
 */
$page_title = 'Hướng Dẫn Gắn Tên Miền Riêng';
require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-5xl mx-auto space-y-10">

    <!-- HERO INTRO -->
    <div class="p-8 sm:p-10 rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white relative overflow-hidden shadow-xl border border-slate-800">
        <div class="relative z-10 max-w-3xl">
            <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 mb-4 inline-flex items-center">
                <i class="fas fa-check-circle mr-1.5"></i> Chuẩn Bị Triển Khai Toàn Cầu
            </span>
            <h1 class="text-3xl sm:text-4xl font-black mb-3">
                Hướng Dẫn Đăng Ký & Trỏ Tên Miền Riêng
            </h1>
            <p class="text-slate-300 text-sm leading-relaxed">
                Mã nguồn <strong>V-NEWS CMS</strong> được lập trình với cơ chế tự động nhận diện URL máy chủ (Dynamic Base URL), tích hợp bảo mật PDO chống SQL Injection và tương thích 100% với mọi nhà cung cấp Hosting cPanel, DirectAdmin hoặc Cloud VPS khi gắn tên miền riêng.
            </p>
        </div>
    </div>

    <!-- TỔNG QUAN 5 BƯỚC -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-xs">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div>
                <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 font-black flex items-center justify-center text-sm mb-3">1</span>
                <h4 class="font-bold text-slate-900 dark:text-white text-sm">Mua Tên Miền</h4>
                <p class="text-slate-400 mt-1">Chọn tên miền đẹp (.vn, .com, .net) tại nhà đăng ký.</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div>
                <span class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-slate-800 text-purple-600 dark:text-purple-400 font-black flex items-center justify-center text-sm mb-3">2</span>
                <h4 class="font-bold text-slate-900 dark:text-white text-sm">Mua Hosting / VPS</h4>
                <p class="text-slate-400 mt-1">Hosting Linux hỗ trợ PHP 8.1+ & MySQL.</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div>
                <span class="w-8 h-8 rounded-xl bg-sky-50 dark:bg-slate-800 text-sky-600 dark:text-sky-400 font-black flex items-center justify-center text-sm mb-3">3</span>
                <h4 class="font-bold text-slate-900 dark:text-white text-sm">Trỏ DNS Bản Ghi</h4>
                <p class="text-slate-400 mt-1">Tạo bản ghi A và CNAME trỏ về IP Hosting.</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 font-black flex items-center justify-center text-sm mb-3">4</span>
                <h4 class="font-bold text-slate-900 dark:text-white text-sm">Upload Mã Nguồn</h4>
                <p class="text-slate-400 mt-1">Tải mã nguồn lên public_html & import MySQL.</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div>
                <span class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-slate-800 text-amber-600 dark:text-amber-400 font-black flex items-center justify-center text-sm mb-3">5</span>
                <h4 class="font-bold text-slate-900 dark:text-white text-sm">Bật SSL Miễn Phí</h4>
                <p class="text-slate-400 mt-1">Kích hoạt HTTPS Let's Encrypt bảo mật xanh.</p>
            </div>
        </div>
    </div>

    <!-- CHI TIẾT TỪNG BƯỚC -->
    <div class="space-y-8">

        <!-- BƯỚC 1 -->
        <section class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">1</span>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Bước 1: Chọn & Đăng Ký Tên Miền (Domain Name)</h3>
            </div>

            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                Tên miền là địa chỉ định danh trang báo điện tử của bạn (Ví dụ: <code class="text-indigo-600 dark:text-indigo-400 font-bold">tintuc24h.vn</code> hoặc <code class="text-indigo-600 dark:text-indigo-400 font-bold">vnews-media.com</code>).
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs">
                    <h4 class="font-bold text-slate-900 dark:text-white mb-2 flex items-center">
                        <i class="fas fa-flag text-rose-500 mr-2"></i> Tên Miền Việt Nam (.vn, .com.vn)
                    </h4>
                    <p class="text-slate-500 dark:text-slate-400 mb-3">Tốt nhất cho đối tượng độc giả tại Việt Nam, tăng độ tin cậy và tối ưu SEO địa phương của Google.</p>
                    <span class="font-semibold text-slate-700 dark:text-slate-300">Nhà đăng ký uy tín:</span>
                    <ul class="list-disc pl-5 mt-1 space-y-1 text-slate-500 dark:text-slate-400">
                        <li><strong>iNET</strong> (inet.vn)</li>
                        <li><strong>Mắt Bão</strong> (matbao.net)</li>
                        <li><strong>PA Việt Nam</strong> (pavietnam.vn)</li>
                        <li><strong>Nhân Hòa</strong> (nhanhoa.com)</li>
                    </ul>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs">
                    <h4 class="font-bold text-slate-900 dark:text-white mb-2 flex items-center">
                        <i class="fas fa-globe text-sky-500 mr-2"></i> Tên Miền Quốc Tế (.com, .net, .news)
                    </h4>
                    <p class="text-slate-500 dark:text-slate-400 mb-3">Phổ biến toàn cầu, thủ tục đăng ký trực tuyến kích hoạt tức thì trong 1 phút.</p>
                    <span class="font-semibold text-slate-700 dark:text-slate-300">Nhà đăng ký uy tín:</span>
                    <ul class="list-disc pl-5 mt-1 space-y-1 text-slate-500 dark:text-slate-400">
                        <li><strong>Namecheap</strong> (namecheap.com)</li>
                        <li><strong>Cloudflare Registrar</strong> (cloudflare.com) - Giá gốc không chênh lệch</li>
                        <li><strong>GoDaddy</strong> (godaddy.com)</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- BƯỚC 2 -->
        <section class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">2</span>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Bước 2: Mua Hosting Hoặc Cloud VPS Hỗ Trợ PHP</h3>
            </div>

            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                Sau khi mua gói Hosting (ví dụ cPanel Hosting tại Vietnix, AZDIGI, Hostinger, Inet), nhà cung cấp sẽ gửi cho bạn một email chứa thông tin:
            </p>

            <div class="p-4 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800 font-mono text-xs text-slate-800 dark:text-slate-200 space-y-1">
                <p>• Địa chỉ IP máy chủ (Server IP): <strong>103.20.xxx.xxx</strong> (Ghi nhớ IP này để trỏ DNS)</p>
                <p>• Link đăng nhập cPanel: <strong>https://cpanel.domain-hosting.com:2083</strong></p>
                <p>• Tên đăng nhập và Mật khẩu cPanel</p>
            </div>
        </section>

        <!-- BƯỚC 3 -->
        <section class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">3</span>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Bước 3: Trỏ Bản Ghi DNS Của Tên Miền Về Hosting</h3>
            </div>

            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                Đăng nhập vào trang quản trị tên miền của bạn (ví dụ iNET hoặc Cloudflare), vào mục <strong>Quản lý DNS (DNS Management)</strong> và tạo chính xác 2 bản ghi sau:
            </p>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-mono">
                    <thead class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        <tr>
                            <th class="p-3">Loại bản ghi (Type)</th>
                            <th class="p-3">Tên máy chủ (Host / Name)</th>
                            <th class="p-3">Giá trị trỏ tới (Value / IP Address)</th>
                            <th class="p-3">TTL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-slate-600 dark:text-slate-300">
                        <tr>
                            <td class="p-3 font-bold text-indigo-600 dark:text-indigo-400">A</td>
                            <td class="p-3 font-bold">@</td>
                            <td class="p-3">IP Hosting (ví dụ: 103.20.123.45)</td>
                            <td class="p-3">Tự động / 300s</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-indigo-600 dark:text-indigo-400">CNAME</td>
                            <td class="p-3 font-bold">www</td>
                            <td class="p-3">tenmiencuaban.com</td>
                            <td class="p-3">Tự động / 300s</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="text-[11px] text-slate-400 italic">
                * Mẹo: Bạn có thể kiểm tra xem tên miền đã trỏ về IP mới chưa tại website miễn phí: <a href="https://www.dnschecker.org" target="_blank" class="text-indigo-500 underline font-bold">dnschecker.org</a>.
            </p>
        </section>

        <!-- BƯỚC 4 -->
        <section class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">4</span>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Bước 4: Tải Mã Nguồn Lên Hosting & Tạo CSDL MySQL</h3>
            </div>

            <ol class="list-decimal pl-5 space-y-3 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                <li>
                    <strong>Nén toàn bộ thư mục dự án</strong> thành file <code>website.zip</code>.
                </li>
                <li>
                    Đăng nhập vào <strong>cPanel</strong> -> Tìm mục <strong>File Manager (Quản lý tệp)</strong> -> Vào thư mục <strong>public_html</strong> -> Bấm <strong>Upload</strong> và tải file zip lên -> Chuột phải chọn <strong>Extract (Giải nén)</strong>.
                </li>
                <li>
                    Tạo cơ sở dữ liệu: Trong cPanel, vào mục <strong>MySQL Databases</strong>:
                    <ul class="list-disc pl-5 mt-1 space-y-0.5 text-xs text-slate-500 dark:text-slate-400">
                        <li>Tạo mới 1 Database (Ví dụ: <code>u123_vnews</code>).</li>
                        <li>Tạo mới 1 User và mật khẩu an toàn.</li>
                        <li>Gán User vào Database và tích chọn <strong>ALL PRIVILEGES</strong>.</li>
                    </ul>
                </li>
                <li>
                    Import bảng dữ liệu: Mở <strong>phpMyAdmin</strong> từ cPanel -> Chọn Database vừa tạo -> Bấm tab <strong>Import</strong> -> Chọn file <code class="text-indigo-600 font-bold">database/schema.sql</code> có sẵn trong mã nguồn và bấm <strong>Go / Thực hiện</strong>.
                </li>
                <li>
                    Cấu hình kết nối trong file <code class="text-indigo-600 font-bold">config/database.php</code>:
                    <div class="p-4 rounded-xl bg-slate-950 text-slate-200 font-mono text-xs my-2">
<pre>// Đổi sang chế độ MySQL cho Hosting
define('DB_TYPE', 'mysql');

define('DB_HOST', 'localhost');
define('DB_NAME', 'u123_vnews');      // Tên Database vừa tạo
define('DB_USER', 'u123_vnews_user'); // Tên User Database
define('DB_PASS', 'MatKhauCuaBan123@'); // Mật khẩu Database</pre>
                    </div>
                </li>
            </ol>
        </section>

        <!-- BƯỚC 5 -->
        <section class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <div class="flex items-center space-x-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <span class="w-8 h-8 rounded-xl bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">5</span>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Bước 5: Kích Hoạt Chứng Chỉ Bảo Mật SSL (HTTPS) & Hoàn Tất</h3>
            </div>

            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                Trên cPanel, tìm mục <strong>SSL/TLS Status</strong> hoặc <strong>Let's Encrypt SSL</strong> -> Tích chọn tên miền của bạn và bấm <strong>Run AutoSSL</strong>. Sau 2-3 phút, biểu tượng ổ khóa xanh (HTTPS an toàn) sẽ được bật tự động miễn phí trọn đời!
            </p>

            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300 space-y-2">
                <p class="font-bold flex items-center">
                    <i class="fas fa-check-double mr-2"></i> Chúc mừng! Trang web của bạn đã sẵn sàng chạy với tên miền thật!
                </p>
                <p>
                    Bước cuối cùng: Đăng nhập vào trang quản trị CMS tại <code class="font-bold">https://tenmiencuaban.com/admin</code> -> Vào mục <strong>Cài Đặt Hệ Thống</strong> -> Cập nhật mục <strong>Tên Miền Chính Thức</strong> thành tên miền của bạn.
                </p>
            </div>
        </section>

    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
