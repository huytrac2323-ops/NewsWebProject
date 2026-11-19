<?php
/**
 * V-NEWS CMS - Trang Tĩnh (Static Pages)
 */
require_once __DIR__ . '/config/config.php';

$slug = $_GET['slug'] ?? 'gioi-thieu';

$pages = [
    'gioi-thieu' => [
        'title' => 'Về Chúng Tôi - Tòa Soạn Báo Điện Tử V-NEWS',
        'content' => '
            <p class="lead">V-NEWS là nền tảng báo điện tử và hệ thống quản trị nội dung (CMS) đa phương tiện hàng đầu, chuyên cung cấp thông tin thời sự, công nghệ, kinh tế số và phong cách sống.</p>
            <h3>Tôn chỉ & Mục đích</h3>
            <p>Chúng tôi hoạt động với phương châm: <strong>"Nhanh nhạy - Đa chiều - Khách quan - Sâu sắc"</strong>. Đội ngũ phóng viên và biên tập viên của V-NEWS không ngừng cập nhật dòng chảy tin tức 24/7 để mang đến những góc nhìn giá trị nhất cho độc giả.</p>
            <h3>Nền tảng Công nghệ Hiện đại</h3>
            <p>Hệ thống V-NEWS CMS được xây dựng trên nền tảng PHP hiện đại, tối ưu hóa tốc độ tải trang dưới 1 giây, bảo mật cao và tương thích tuyệt đối với mọi thiết bị di động cũng như các công cụ tìm kiếm chuẩn SEO của Google.</p>
            <p>Trang web hỗ trợ tích hợp tên miền riêng tùy biến và kết nối máy chủ linh hoạt, phục vụ hàng triệu lượt truy cập mỗi tháng.</p>
        '
    ],
    'chinh-sach-bao-mat' => [
        'title' => 'Chính Sách Bảo Mật Thông Tin Độc Giả',
        'content' => '
            <p class="lead">V-NEWS cam kết tôn trọng và bảo vệ tuyệt đối quyền riêng tư cùng thông tin cá nhân của mọi độc giả khi truy cập hệ thống.</p>
            <h3>1. Thu thập thông tin</h3>
            <p>Chúng tôi chỉ thu thập thông tin cá nhân cơ bản (như Tên, Email) khi bạn tự nguyện gửi bình luận, đăng ký nhận bản tin hoặc gửi thư liên hệ đóng góp bài viết.</p>
            <h3>2. Bảo mật dữ liệu</h3>
            <p>Mọi dữ liệu gửi lên hệ thống đều được mã hóa bằng chuẩn SSL (HTTPS) và bảo vệ an toàn trên hệ thống máy chủ cơ sở dữ liệu. Chúng tôi tuyệt đối không bán hoặc chia sẻ thông tin độc giả cho bên thứ ba vì bất kỳ mục đích thương mại nào.</p>
            <h3>3. Cookies</h3>
            <p>Website sử dụng cookies cục bộ để ghi nhớ tùy chọn giao diện (chế độ Dark Mode / Sáng) của bạn nhằm mang lại trải nghiệm đọc tin tối ưu nhất.</p>
        '
    ]
];

$currentPage = $pages[$slug] ?? $pages['gioi-thieu'];
$page_title = $currentPage['title'];

require_once ROOT_PATH . '/templates/header.php';
?>

<div class="max-w-4xl mx-auto bg-white dark:bg-slate-900 rounded-3xl p-8 sm:p-12 border border-slate-200 dark:border-slate-800 shadow-sm">
    <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white mb-8 pb-4 border-b border-slate-100 dark:border-slate-800">
        <?= htmlspecialchars($currentPage['title']) ?>
    </h1>
    <div class="article-prose">
        <?= $currentPage['content'] ?>
    </div>
</div>

<?php require_once ROOT_PATH . '/templates/footer.php'; ?>
