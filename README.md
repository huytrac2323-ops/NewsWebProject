# 📰 V-NEWS CMS - Hệ Thống Báo Điện Tử, Blog & Quản Trị Nội Dung Đa Phương Tiện (PHP & MySQL/SQLite)

Một giải pháp website tin tức, blog cá nhân & báo điện tử hiện đại, tốc độ cao được xây dựng hoàn toàn bằng **PHP thuần hiện đại (Modern PHP)**, bảo mật chuẩn OWASP và sẵn sàng 100% để **gắn Tên Miền riêng (.vn, .com, .net, .news)** lên bất kỳ Hosting (cPanel/DirectAdmin) hoặc Cloud VPS nào.

---

## 🌟 1. Tính Năng Nổi Bật

### 🌐 Giao Diện Độc Giả (Frontend)
- **Thiết kế chuẩn Báo chí Hiện đại:** Bố cục tin nóng (Breaking News), Hero Featured 1 chính + 3 phụ, Tin theo chuyên mục, Dòng thời sự mới nhất.
- **Tùy biến Giao diện Sáng / Tối (Dark / Light Mode):** Tự động thích ứng sở thích người dùng và lưu trạng thái vào LocalStorage.
- **Đọc bài viết chuẩn Typography:** Hỗ trợ thanh tiến trình đọc bài (Reading Progress Bar), ước tính thời gian đọc, lượt xem thực tế (View Counter), tác giả, ngày đăng tiếng Việt.
- **Tương tác Độc giả:** Hệ thống gửi bình luận có kiểm duyệt, nút chia sẻ nhanh lên Facebook, Twitter/X, sao chép liên kết (Copy Link) có Toast thông báo mượt mà.
- **Tìm kiếm & Phân trang:** Bộ máy tìm kiếm toàn văn nhanh chóng, lọc bài viết theo chuyên mục và phân trang tiện lợi.
- **Đăng ký nhận bản tin (Newsletter Subscription):** Form đăng ký email tích hợp ngay chân trang.
- **Tối ưu SEO Toàn Diện:**
  - Tự động sinh `sitemap.xml.php` cho Google Search Console.
  - Cung cấp nguồn cấp tin `rss.xml.php` chuẩn RSS 2.0.
  - Thẻ OpenGraph, Twitter Card, Canonical URL động theo tên miền.

### 🛡️ Hệ Thống Quản Trị Nội Dung (Backend CMS Panel)
- **Bảng Điều Khiển Trực Quan (Dashboard):** Thống kê số lượng bài viết, chuyên mục, bình luận, tổng lượt xem và danh sách hoạt động gần đây.
- **Quản lý Bài viết (CRUD):**
  - Soạn bài viết mới và chỉnh sửa trực quan với thanh công cụ định dạng HTML nhanh (In đậm, In nghiêng, Tiêu đề H3, Trích dẫn, Chèn ảnh, Danh sách).
  - Tự động tạo URL SEO (Slug) theo thời gian thực từ Tiêu đề tiếng Việt.
  - Tải ảnh đại diện trực tiếp từ máy tính lên thư mục `storage/uploads/` hoặc dán link ảnh từ Unsplash/CDN.
  - Gắn nhãn **Tin Nổi Bật (Featured)** hoặc **Tin Nóng (Breaking News)**.
- **Quản lý Chuyên mục (Categories):** Tùy chỉnh tên, màu sắc huy hiệu, mô tả và thứ tự sắp xếp.
- **Kiểm duyệt Bình luận (Comments Moderation):** Duyệt đăng, đánh dấu Spam hoặc xóa bình luận không phù hợp.
- **Quản lý Thành viên & Phân quyền:** Tạo tài khoản Biên tập viên (Editor) hoặc Quản trị viên cấp cao (Admin).
- **Cài đặt Hệ thống (Settings):** Cấu hình tên website, slogan, tên miền chính thức, logo, email tòa soạn, số hotline, bản quyền.
- **Trang Hướng Dẫn Tên Miền Độc Lập:** Tích hợp sẵn tài liệu hướng dẫn kỹ thuật ngay trong menu Admin.

---

## 🚀 2. Hướng Dẫn Chạy Thử Cục Bộ (Localhost)

Website hỗ trợ cơ chế **Zero-Config với SQLite**: Bạn không cần cài đặt hay tạo cơ sở dữ liệu MySQL phức tạp để chạy thử, hệ thống sẽ tự động khởi tạo CSDL mẫu với đầy đủ bài viết chất lượng cao ngay lần đầu khởi động!

### Bước 1: Mở Terminal / PowerShell tại thư mục dự án
```powershell
cd c:\Users\acer\Downloads\NewsWebProject
```

### Bước 2: Khởi động máy chủ PHP tích hợp
```powershell
php -S localhost:8000
```

### Bước 3: Truy cập vào trình duyệt
- **Trang chủ Website:** [http://localhost:8000](http://localhost:8000)
- **Trang Quản Trị CMS:** [http://localhost:8000/admin](http://localhost:8000/admin)
  - **Tên đăng nhập:** `admin`
  - **Mật khẩu:** `admin123`

---

## 🌐 3. Hướng Dẫn Đăng Ký & Gắn Tên Miền Riêng (Deploy Lên Hosting)

Để website hoạt động chính thức trên Internet với tên miền của bạn (ví dụ: `https://tintuc24h.com` hoặc `https://myblog.vn`), bạn thực hiện theo các bước sau:

### Bước 1: Mua Tên Miền (Domain Name)
- **Tên miền Việt Nam (.vn, .com.vn):** Đăng ký tại [iNET.vn](https://inet.vn), [Mắt Bão](https://matbao.net), [PA Việt Nam](https://pavietnam.vn).
- **Tên miền Quốc tế (.com, .net, .news):** Đăng ký tại [Namecheap](https://namecheap.com) hoặc [Cloudflare Registrar](https://cloudflare.com).

### Bước 2: Mua Hosting cPanel / VPS
- Chọn bất kỳ gói Hosting Linux nào hỗ trợ PHP 8.1 trở lên và MySQL (như Vietnix, AZDIGI, Hostinger, Inet,...).
- Sau khi mua, bạn sẽ nhận được **Địa chỉ IP của Hosting** (Ví dụ: `103.20.123.45`).

### Bước 3: Trỏ Bản Ghi DNS Của Tên Miền
Đăng nhập vào trang quản lý tên miền của bạn, tạo 2 bản ghi sau:
| Loại bản ghi (Type) | Tên máy chủ (Host) | Giá trị trỏ đến (Value) |
|---|---|---|
| **A** | `@` | IP Hosting của bạn (Ví dụ: `103.20.123.45`) |
| **CNAME** | `www` | Tên miền của bạn (Ví dụ: `tintuc24h.com`) |

*(Kiểm tra kết quả trỏ DNS sau 5-15 phút tại [dnschecker.org](https://www.dnschecker.org))*

### Bước 4: Tải Mã Nguồn Lên Hosting & Cấu Hình MySQL
1. Nén toàn bộ thư mục `NewsWebProject` thành file `.zip`.
2. Đăng nhập vào **cPanel** -> Mở **File Manager** -> Vào thư mục `public_html` -> Tải file `.zip` lên và Giải nén (Extract).
3. Vào cPanel -> **MySQL Databases**:
   - Tạo 1 Database mới (ví dụ: `u123_vnews`).
   - Tạo 1 User mới và gán toàn quyền (**ALL PRIVILEGES**) vào Database vừa tạo.
4. Mở **phpMyAdmin** từ cPanel -> Chọn Database vừa tạo -> Chọn tab **Import** -> Chọn file `database/schema.sql` có sẵn trong mã nguồn -> Bấm **Go / Thực hiện**.
5. Mở file `config/database.php` trên File Manager và đổi cấu hình sang MySQL:
   ```php
   define('DB_TYPE', 'mysql');
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'u123_vnews');      // Tên Database vừa tạo
   define('DB_USER', 'u123_vnews_user'); // Tên User Database
   define('DB_PASS', 'MatKhauCuaBan@');  // Mật khẩu Database
   ```

### Bước 5: Bật Chứng Chỉ Bảo Mật SSL Miễn Phí (HTTPS)
Trong giao diện cPanel, tìm mục **SSL/TLS Status** hoặc **Let's Encrypt SSL** -> Tích chọn tên miền của bạn và nhấn **Run AutoSSL**. Website của bạn sẽ có biểu tượng ổ khóa xanh an toàn vĩnh viễn!

### Bước 6: Cập nhật Tên Miền Trong CMS
Đăng nhập vào CMS theo tên miền mới: `https://tenmiencuaban.com/admin` -> Vào **Cài Đặt Hệ Thống** -> Cập nhật trường **Tên Miền Chính Thức** thành `https://tenmiencuaban.com`.

---

## 📁 4. Cấu Trúc Thư Mục Dự Án

```text
NewsWebProject/
├── config/
│   ├── config.php          # Cấu hình hệ thống, Base URL động, hằng số
│   ├── database.php        # Bộ kết nối PDO (Hỗ trợ cả SQLite & MySQL)
│   ├── functions.php       # Các hàm tiện ích (Slug SEO tiếng Việt, format ngày, CSRF, Toast)
│   └── auth.php            # Quản lý phân quyền, session đăng nhập
├── database/
│   ├── schema.sql          # Bản vẽ CSDL MySQL chuẩn phpMyAdmin
│   └── sqlite_init.php     # Tự động nạp CSDL & bài viết mẫu khi chạy cục bộ
├── storage/
│   ├── database.sqlite     # CSDL SQLite cục bộ (Tự động tạo)
│   └── uploads/            # Thư mục lưu ảnh bài viết tải lên
├── templates/
│   ├── header.php          # Thanh điều hướng, Breaking News, Dark mode, Menu di động
│   ├── footer.php          # Chân trang, form đăng ký nhận tin, liên hệ
│   └── sidebar.php         # Tin đọc nhiều, danh mục bài viết, banner tên miền
├── assets/
│   ├── css/style.css       # Định dạng typography bài viết, hiệu ứng Dark mode
│   └── js/main.js          # Dark mode switcher, menu drawer, tạo slug tự động
├── admin/
│   ├── includes/           # Header & Footer giao diện quản trị
│   ├── index.php           # Dashboard thống kê KPI, bài viết & bình luận mới
│   ├── login.php           # Đăng nhập quản trị bảo mật CSRF
│   ├── logout.php          # Đăng xuất an toàn
│   ├── articles.php        # Danh sách, lọc, xóa bài viết
│   ├── article_edit.php    # Soạn thảo bài viết mới & chỉnh sửa (WYSIWYG tools)
│   ├── categories.php      # Quản lý chuyên mục bài viết
│   ├── comments.php        # Kiểm duyệt bình luận bạn đọc
│   ├── users.php           # Quản lý biên tập viên & quản trị viên
│   ├── settings.php        # Cài đặt website, SEO meta & tên miền chính
│   └── domain_guide.php    # Hướng dẫn chi tiết đăng ký & trỏ tên miền
├── index.php               # Trang chủ báo điện tử
├── article.php             # Trang chi tiết bài viết, bình luận & bài liên quan
├── category.php            # Trang lưu trữ chuyên mục kèm phân trang
├── search.php              # Trang tìm kiếm bài viết theo từ khóa
├── contact.php             # Trang liên hệ tòa soạn & đóng góp bài viết
├── page.php                # Trang tĩnh (Giới thiệu, Chính sách bảo mật)
├── sitemap.xml.php         # Sơ đồ trang web chuẩn SEO Google
├── rss.xml.php             # Kênh tin tức RSS 2.0 Feed
├── .htaccess               # Cấu hình Apache Rewrite & bảo mật thư mục
├── nginx.conf.example      # Cấu hình máy chủ Nginx mẫu cho VPS
└── README.md               # Tài liệu hướng dẫn sử dụng và triển khai
```

---
© 2026 V-NEWS CMS. Phát triển cho mục đích xây dựng Báo Điện Tử, Blog cá nhân & Hệ thống Quản trị Nội dung chuyên nghiệp.
