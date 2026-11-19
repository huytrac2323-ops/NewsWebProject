-- ========================================================
-- NẠP DỮ LIỆU BÀI VIẾT & CHUYÊN MỤC MẪU CHO V-NEWS
-- ========================================================

SET NAMES utf8mb4;

-- 1. Thêm Chuyên mục
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `color`, `sort_order`) VALUES
(1, 'Công nghệ', 'cong-nghe', 'Cập nhật xu hướng công nghệ, phần cứng, phần mềm và tiện ích', 'indigo', 1),
(2, 'Trí tuệ nhân tạo', 'tri-tue-nhan-tao', 'Đột phá AI, Machine Learning, Robot và tương lai số', 'purple', 2),
(3, 'Kinh doanh', 'kinh-doanh', 'Kinh tế thị trường, tài chính số, khởi nghiệp và đầu tư', 'emerald', 3),
(4, 'Đời sống số', 'doi-song-so', 'Phong cách sống hiện đại, trải nghiệm du lịch và văn hóa', 'amber', 4),
(5, 'Bảo mật & Web', 'bao-mat-web', 'Bảo mật mạng, an ninh dữ liệu, SEO và hạ tầng tên miền', 'rose', 5)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- 2. Thêm Bài viết mẫu
INSERT INTO `articles` (`id`, `title`, `slug`, `excerpt`, `content`, `thumbnail`, `category_id`, `author_id`, `status`, `is_featured`, `is_breaking`, `views_count`, `tags`, `created_at`) VALUES
(1, 'Kỷ nguyên AI Agent 2026: Trợ lý thông minh thay đổi cách con người làm việc như thế nào?', 
'ky-nguyen-ai-agent-2026-tro-ly-thong-minh-thay-doi-cong-viec', 
'Sự trỗi dậy của các mô hình đa tác nhân (Multi-Agent Systems) đang định hình lại toàn bộ quy trình vận hành của các tập đoàn công nghệ toàn cầu trong năm 2026.', 
'<p class=\"lead\">Từ việc tự động hóa viết mã nguồn, phân tích dữ liệu thị trường cho đến điều phối chuỗi cung ứng, các tác nhân AI (AI Agents) thế hệ mới đang vượt xa giới hạn của chatbot truyền thống.</p><h3>1. Bước nhảy vọt từ đối thoại sang hành động thực tế</h3><p>Nếu như những năm 2023 - 2024 thế giới chứng kiến sự bùng nổ của các mô hình ngôn ngữ lớn (LLMs) hỗ trợ hỏi đáp, thì bước sang năm 2026, trọng tâm đã dịch chuyển hoàn toàn sang <strong>Agentic AI</strong>. Đây là các hệ thống có khả năng tự lập kế hoạch nhiều bước (Reasoning & Planning), tự gọi công cụ (Tool calling) và phối hợp với các agent khác để hoàn thành mục tiêu phức tạp.</p><blockquote>\"AI Agent không chỉ đưa ra câu trả lời; chúng thực thi hành động, kiểm thử kết quả và tự sửa sai nếu phát sinh lỗi.\" - Chuyên gia công nghệ nhận định.</blockquote><h3>2. Lợi ích vượt trội đối với doanh nghiệp</h3><p>Các khảo sát mới nhất cho thấy các doanh nghiệp áp dụng giải pháp AI tự động hóa đã tăng 45% năng suất lao động trong các tác vụ văn phòng và kiểm thử phần mềm.</p><ul><li><strong>Giảm 70% thời gian xử lý sự cố kỹ thuật:</strong> AI có thể đọc log hệ thống và đề xuất bản vá tự động.</li><li><strong>Cá nhân hóa dịch vụ khách hàng 24/7:</strong> Tương tác bằng ngôn ngữ tự nhiên theo ngữ cảnh sâu.</li><li><strong>Tối ưu hóa chi phí vận hành:</strong> Giúp các nhóm nhỏ đạt hiệu suất tương đương các phòng ban lớn.</li></ul>', 
'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200&auto=format&fit=crop&q=80', 
2, 1, 'published', 1, 1, 1542, 'AI, Trí tuệ nhân tạo, Agentic AI, Xu hướng 2026', NOW()),

(2, 'Hướng dẫn đăng ký Tên miền & Thiết lập Hosting PHP từ A đến Z cho người mới bắt đầu', 
'huong-dan-dang-ky-ten-mien-va-thiet-lap-hosting-php', 
'Toàn bộ quy trình chọn mua tên miền .com, .vn, trỏ bản ghi DNS (A record, CNAME) và cấu hình mã nguồn PHP lên cPanel/VPS để website hoạt động toàn cầu.', 
'<p class=\"lead\">Sở hữu một website với tên miền riêng (Custom Domain) là bước khởi đầu quan trọng nhất để xây dựng thương hiệu cá nhân hoặc báo điện tử chuyên nghiệp.</p><h3>Bước 1: Chọn và đăng ký tên miền (Domain Name)</h3><p>Tên miền chính là địa chỉ định danh trang web của bạn trên Internet (ví dụ: <code>mywebsite.com</code> hoặc <code>tintuc24h.vn</code>).</p><h3>Bước 2: Trỏ bản ghi DNS của tên miền về máy chủ</h3><p>Đăng nhập vào trang quản lý tên miền của bạn, truy cập mục DNS Management và tạo bản ghi A trỏ về IP của Hosting.</p>', 
'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=1200&auto=format&fit=crop&q=80', 
5, 1, 'published', 1, 0, 980, 'Tên miền, Domain, Hosting, PHP, DNS, cPanel', NOW()),

(3, 'Thị trường Thương mại Điện tử và Thanh toán số Đông Nam Á tăng tốc mạnh mẽ', 
'thi-truong-thuong-mai-dien-tu-thanh-toan-so-dong-nam-a', 
'Báo cáo nền kinh tế số chỉ ra Việt Nam và Indonesia tiếp tục là hai thị trường có tốc độ tăng trưởng GMV thương mại điện tử nhanh nhất khu vực.', 
'<p class=\"lead\">Xu hướng mua sắm kết hợp giải trí (Shoppertainment) cùng sự phổ cập của mã thanh toán VietQR và ví điện tử đang biến khu vực thành trung tâm phát triển thương mại số năng động bậc nhất châu Á.</p><h3>Đòn bẩy từ hạ tầng thanh toán không tiền mặt</h3><p>Tỷ lệ thanh toán không dùng tiền mặt tại Việt Nam đã tăng hơn 60% chỉ trong hai năm qua. Các tiểu thương và chuỗi bán lẻ đều đã tích hợp thanh toán mã QR tức thì, giúp quy trình giao dịch diễn ra chỉ trong vài giây.</p>', 
'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=1200&auto=format&fit=crop&q=80', 
3, 1, 'published', 1, 0, 745, 'Kinh doanh, Thương mại điện tử, FinTech, Kinh tế số', NOW()),

(4, 'Top các xu hướng thiết kế Website Báo chí và Trải nghiệm Người dùng (UX/UI) 2026', 
'top-xu-huong-thiet-ke-website-bao-chi-va-ux-ui-2026', 
'Giao diện tối giản, tốc độ tải trang dưới 1 giây, typography chuẩn tạp chí cùng chế độ Dark Mode thích ứng tự động là những chuẩn mực mới.', 
'<p class=\"lead\">Độc giả hiện đại không chỉ tìm kiếm tin tức chính xác mà còn yêu cầu một trải nghiệm đọc thông suốt, không bị làm phiền bởi quảng cáo rác.</p><h3>Những yếu tố then chốt tạo nên một trang báo điện tử đỉnh cao:</h3><ul><li><strong>Typography tương phản cao:</strong> Giúp người đọc không bị mỏi mắt.</li><li><strong>Mobile-first tuyệt đối:</strong> Hơn 85% lưu lượng đọc tin tức đến từ điện thoại thông minh.</li></ul>', 
'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=1200&auto=format&fit=crop&q=80', 
1, 1, 'published', 0, 0, 610, 'UI/UX, Thiết kế Web, Báo điện tử, Typography', NOW()),

(5, 'Sống tối giản kỹ thuật số (Digital Minimalism): Bí quyết tái tạo năng lượng cho người làm sáng tạo', 
'song-toi-gian-ky-thuat-so-bi-quyet-tai-tao-nang-luong', 
'Làm thế nào để duy trì sự tập trung sâu giữa cơn bão thông báo từ điện thoại và mạng xã hội mỗi ngày?', 
'<p class=\"lead\">Trong thời đại mà sự chú ý trở thành loại tiền tệ quý giá nhất, biết cách \"ngắt kết nối để kết nối lại\" là kỹ năng sinh tồn của mọi chuyên gia hiện đại.</p><p>Phương pháp Digital Detox cuối tuần kết hợp cùng quy tắc không dùng màn hình trước khi ngủ đã giúp hàng triệu người cải thiện giấc ngủ.</p>', 
'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1200&auto=format&fit=crop&q=80', 
4, 1, 'published', 0, 0, 482, 'Đời sống số, Tối giản, Sức khỏe tinh thần', NOW()),

(6, 'Bảo mật Web PHP hiện đại: Chống SQL Injection, XSS và CSRF toàn diện', 
'bao-mat-web-php-hien-dai-sql-injection-xss-csrf', 
'Hướng dẫn chi tiết các kỹ thuật phòng thủ chuẩn OWASP Top 10 dành cho lập trình viên phát triển CMS và ứng dụng web PHP.', 
'<p class=\"lead\">Một website tin tức dù đẹp đến đâu nhưng nếu bị lộ lọt dữ liệu hoặc bị tấn công thay đổi giao diện (Deface) thì uy tín thương hiệu sẽ bị tổn hại nghiêm trọng.</p><h3>1. Sử dụng PDO Prepared Statements</h3><p>Không bao giờ nối chuỗi trực tiếp dữ liệu người dùng vào câu truy vấn SQL để triệt tiêu rủi ro SQL Injection.</p>', 
'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=1200&auto=format&fit=crop&q=80', 
5, 1, 'published', 0, 0, 834, 'PHP, Bảo mật, Cybersecurity, PDO, OWASP', NOW())
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);

-- 3. Thêm Bình luận mẫu
INSERT INTO `comments` (`article_id`, `author_name`, `author_email`, `content`, `status`, `created_at`) VALUES
(1, 'Nguyễn Văn Minh', 'minh.nguyen@gmail.com', 'Bài viết phân tích về AI Agent rất sâu sắc và đúng với thực tế doanh nghiệp hiện nay. Cảm ơn tòa soạn!', 'approved', NOW()),
(1, 'Lê Thị Thu', 'thule.tech@yahoo.com', 'Mình đang áp dụng AI Agent để kiểm thử phần mềm, năng suất tăng rõ rệt.', 'approved', NOW()),
(2, 'Trần Hoàng Long', 'long.tran@outlook.com', 'Bài hướng dẫn trỏ DNS rất chi tiết, mình đã trỏ thành công tên miền về hosting.', 'approved', NOW());
