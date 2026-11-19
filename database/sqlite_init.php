<?php
/**
 * V-NEWS CMS - Khởi tạo CSDL SQLite tự động & Nạp dữ liệu mẫu chất lượng cao
 */

function initialize_sqlite_database(PDO $pdo) {
    // 1. Tạo bảng Users
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            full_name TEXT NOT NULL,
            role TEXT DEFAULT 'editor',
            avatar TEXT DEFAULT NULL,
            bio TEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // 2. Tạo bảng Categories
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT DEFAULT NULL,
            color TEXT DEFAULT 'indigo',
            sort_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // 3. Tạo bảng Articles
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS articles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            excerpt TEXT DEFAULT NULL,
            content TEXT NOT NULL,
            thumbnail TEXT DEFAULT NULL,
            category_id INTEGER NOT NULL,
            author_id INTEGER NOT NULL,
            status TEXT DEFAULT 'published',
            is_featured INTEGER DEFAULT 0,
            is_breaking INTEGER DEFAULT 0,
            views_count INTEGER DEFAULT 0,
            tags TEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
            FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ");

    // 4. Tạo bảng Comments
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            article_id INTEGER NOT NULL,
            author_name TEXT NOT NULL,
            author_email TEXT NOT NULL,
            content TEXT NOT NULL,
            status TEXT DEFAULT 'approved',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
        );
    ");

    // 5. Tạo bảng Settings
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            setting_key TEXT NOT NULL UNIQUE,
            setting_value TEXT DEFAULT NULL
        );
    ");

    // Nạp cài đặt mặc định
    $settings = [
        'site_name' => 'V-NEWS 24/7',
        'site_tagline' => 'Thời sự - Công nghệ - Kinh doanh - Đời sống số',
        'site_description' => 'Hệ thống Báo Điện Tử & Quản Trị Nội Dung Đa Phương Tiện hiện đại, tốc độ cao và tối ưu SEO hàng đầu.',
        'site_keywords' => 'tin tức, công nghệ, kinh doanh, thời sự, blog php, cms, tin nhanh 24h, mua domain, tên miền',
        'primary_domain' => 'https://vnews247.vn',
        'contact_email' => 'toasoan@vnews247.vn',
        'contact_hotline' => '1900 6868',
        'facebook_url' => 'https://facebook.com/vnews247',
        'youtube_url' => 'https://youtube.com/@vnews247',
        'footer_copyright' => '© 2026 V-NEWS Media. Bản quyền thuộc về V-NEWS CMS.'
    ];

    $stmtSetting = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($settings as $k => $v) {
        $stmtSetting->execute([$k, $v]);
    }

    // Tạo tài khoản Admin mặc định: admin / admin123
    $adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
    $stmtUser = $pdo->prepare("
        INSERT INTO users (username, email, password, full_name, role, bio) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmtUser->execute([
        'admin',
        'admin@vnews.vn',
        $adminPassword,
        'Tổng Biên Tập',
        'admin',
        'Trưởng ban biên tập tin tức V-NEWS. Hơn 10 năm kinh nghiệm trong ngành báo chí công nghệ số.'
    ]);
    $adminId = $pdo->lastInsertId();

    // Nạp các Chuyên mục mẫu
    $categories = [
        ['Công nghệ', 'cong-nghe', 'Cập nhật xu hướng công nghệ, phần cứng, phần mềm và tiện ích', 'indigo', 1],
        ['Trí tuệ nhân tạo', 'tri-tue-nhan-tao', 'Đột phá AI, Machine Learning, Robot và tương lai số', 'purple', 2],
        ['Kinh doanh', 'kinh-doanh', 'Kinh tế thị trường, tài chính số, khởi nghiệp và đầu tư', 'emerald', 3],
        ['Đời sống số', 'doi-song-so', 'Phong cách sống hiện đại, trải nghiệm du lịch và văn hóa', 'amber', 4],
        ['Bảo mật & Web', 'bao-mat-web', 'Bảo mật mạng, an ninh dữ liệu, SEO và hạ tầng tên miền', 'rose', 5],
    ];

    $stmtCat = $pdo->prepare("
        INSERT INTO categories (name, slug, description, color, sort_order) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $catMap = [];
    foreach ($categories as $cat) {
        $stmtCat->execute($cat);
        $catMap[$cat[1]] = $pdo->lastInsertId();
    }

    // Nạp danh sách bài viết phong phú chất lượng cao
    $articles = [
        [
            'title' => 'Kỷ nguyên AI Agent 2026: Trợ lý thông minh thay đổi cách con người làm việc như thế nào?',
            'slug' => 'ky-nguyen-ai-agent-2026-tro-ly-thong-minh-thay-doi-cong-viec',
            'excerpt' => 'Sự trỗi dậy của các mô hình đa tác nhân (Multi-Agent Systems) đang định hình lại toàn bộ quy trình vận hành của các tập đoàn công nghệ toàn cầu trong năm 2026.',
            'content' => '<p class="lead">Từ việc tự động hóa viết mã nguồn, phân tích dữ liệu thị trường cho đến điều phối chuỗi cung ứng, các tác nhân AI (AI Agents) thế hệ mới đang vượt xa giới hạn của chatbot truyền thống.</p>
            <h3>1. Bước nhảy vọt từ đối thoại sang hành động thực tế</h3>
            <p>Nếu như những năm 2023 - 2024 thế giới chứng kiến sự bùng nổ của các mô hình ngôn ngữ lớn (LLMs) hỗ trợ hỏi đáp, thì bước sang năm 2026, trọng tâm đã dịch chuyển hoàn toàn sang <strong>Agentic AI</strong>. Đây là các hệ thống có khả năng tự lập kế hoạch nhiều bước (Reasoning & Planning), tự gọi công cụ (Tool calling) và phối hợp với các agent khác để hoàn thành mục tiêu phức tạp.</p>
            <blockquote>"AI Agent không chỉ đưa ra câu trả lời; chúng thực thi hành động, kiểm thử kết quả và tự sửa sai nếu phát sinh lỗi." - Chuyên gia công nghệ nhận định.</blockquote>
            <h3>2. Lợi ích vượt trội đối với doanh nghiệp</h3>
            <p>Các khảo sát mới nhất cho thấy các doanh nghiệp áp dụng giải pháp AI tự động hóa đã tăng 45% năng suất lao động trong các tác vụ văn phòng và kiểm thử phần mềm.</p>
            <ul>
                <li><strong>Giảm 70% thời gian xử lý sự cố kỹ thuật:</strong> AI có thể đọc log hệ thống và đề xuất bản vá tự động.</li>
                <li><strong>Cá nhân hóa dịch vụ khách hàng 24/7:</strong> Tương tác bằng ngôn ngữ tự nhiên theo ngữ cảnh sâu.</li>
                <li><strong>Tối ưu hóa chi phí vận hành:</strong> Giúp các nhóm nhỏ đạt hiệu suất tương đương các phòng ban lớn.</li>
            </ul>
            <p>Tương lai việc làm không phải là con người bị thay thế bởi AI, mà là những người biết làm chủ và cộng tác cùng AI sẽ thay thế những người không sử dụng công cụ này.</p>',
            'thumbnail' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200&auto=format&fit=crop&q=80',
            'category_slug' => 'tri-tue-nhan-tao',
            'is_featured' => 1,
            'is_breaking' => 1,
            'views' => 1542,
            'tags' => 'AI, Trí tuệ nhân tạo, Agentic AI, Xu hướng 2026'
        ],
        [
            'title' => 'Hướng dẫn đăng ký Tên miền & Thiết lập Hosting PHP từ A đến Z cho người mới bắt đầu',
            'slug' => 'huong-dan-dang-ky-ten-mien-va-thiet-lap-hosting-php',
            'excerpt' => 'Toàn bộ quy trình chọn mua tên miền .com, .vn, trỏ bản ghi DNS (A record, CNAME) và cấu hình mã nguồn PHP lên cPanel/VPS để website hoạt động toàn cầu.',
            'content' => '<p class="lead">Sở hữu một website với tên miền riêng (Custom Domain) là bước khởi đầu quan trọng nhất để xây dựng thương hiệu cá nhân hoặc báo điện tử chuyên nghiệp.</p>
            <h3>Bước 1: Chọn và đăng ký tên miền (Domain Name)</h3>
            <p>Tên miền chính là địa chỉ định danh trang web của bạn trên Internet (ví dụ: <code>mywebsite.com</code> hoặc <code>tintuc24h.vn</code>). Bạn có thể đăng ký tại các nhà đăng ký uy tín như:</p>
            <ul>
                <li><strong>Nhà đăng ký quốc tế:</strong> Namecheap, Cloudflare Registrar, GoDaddy.</li>
                <li><strong>Nhà đăng ký Việt Nam (.vn):</strong> Mắt Bão, PA Việt Nam, iNET, Nhân Hòa.</li>
            </ul>
            <h3>Bước 2: Chuẩn bị Hosting hoặc VPS hỗ trợ PHP & MySQL</h3>
            <p>Website PHP cần một máy chủ web (như Apache hoặc Nginx) và cơ sở dữ liệu MySQL/MariaDB. Hãy chọn gói cPanel Hosting phổ thông với PHP 8.1 trở lên.</p>
            <h3>Bước 3: Trỏ DNS tên miền về địa chỉ IP của Hosting</h3>
            <p>Đăng nhập vào trang quản lý tên miền của bạn, truy cập mục <strong>DNS Management</strong> và tạo 2 bản ghi cốt lõi:</p>
            <ul>
                <li><strong>Bản ghi A:</strong> Host = <code>@</code>, Value = <code>IP máy chủ hosting</code> (Ví dụ: 103.20.xxx.xxx)</li>
                <li><strong>Bản ghi CNAME:</strong> Host = <code>www</code>, Value = <code>yourdomain.com</code></li>
            </ul>
            <p>Sau khi lưu, quá trình DNS Propagation thường mất từ 5 phút đến 24 giờ để lan truyền toàn cầu.</p>',
            'thumbnail' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=1200&auto=format&fit=crop&q=80',
            'category_slug' => 'bao-mat-web',
            'is_featured' => 1,
            'is_breaking' => 0,
            'views' => 980,
            'tags' => 'Tên miền, Domain, Hosting, PHP, DNS, cPanel'
        ],
        [
            'title' => 'Thị trường Thương mại Điện tử và Thanh toán số Đông Nam Á tăng tốc mạnh mẽ',
            'slug' => 'thi-truong-thuong-mai-dien-tu-thanh-toan-so-dong-nam-a',
            'excerpt' => 'Báo cáo nền kinh tế số chỉ ra Việt Nam và Indonesia tiếp tục là hai thị trường có tốc độ tăng trưởng GMV thương mại điện tử nhanh nhất khu vực.',
            'content' => '<p class="lead">Xu hướng mua sắm kết hợp giải trí (Shoppertainment) cùng sự phổ cập của mã thanh toán VietQR và ví điện tử đang biến khu vực thành trung tâm phát triển thương mại số năng động bậc nhất châu Á.</p>
            <h3>Đòn bẩy từ hạ tầng thanh toán không tiền mặt</h3>
            <p>Tỷ lệ thanh toán không dùng tiền mặt tại Việt Nam đã tăng hơn 60% chỉ trong hai năm qua. Các tiểu thương và chuỗi bán lẻ đều đã tích hợp thanh toán mã QR tức thì, giúp quy trình giao dịch diễn ra chỉ trong vài giây.</p>
            <p>Bên cạnh đó, việc ứng dụng AI trong quản lý kho hàng và gợi ý sản phẩm cá nhân hóa đang giúp các nhà bán lẻ trực tuyến giảm đáng kể chi phí lưu kho và tỷ lệ hoàn đơn.</p>',
            'thumbnail' => 'https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=1200&auto=format&fit=crop&q=80',
            'category_slug' => 'kinh-doanh',
            'is_featured' => 1,
            'is_breaking' => 0,
            'views' => 745,
            'tags' => 'Kinh doanh, Thương mại điện tử, FinTech, Kinh tế số'
        ],
        [
            'title' => 'Top các xu hướng thiết kế Website Báo chí và Trải nghiệm Người dùng (UX/UI) 2026',
            'slug' => 'top-xu-huong-thiet-ke-website-bao-chi-va-ux-ui-2026',
            'excerpt' => 'Giao diện tối giản, tốc độ tải trang dưới 1 giây, typography chuẩn tạp chí cùng chế độ Dark Mode thích ứng tự động là những chuẩn mực mới.',
            'content' => '<p class="lead">Độc giả hiện đại không chỉ tìm kiếm tin tức chính xác mà còn yêu cầu một trải nghiệm đọc thông suốt, không bị làm phiền bởi quảng cáo rác.</p>
            <h3>Những yếu tố then chốt tạo nên một trang báo điện tử đỉnh cao:</h3>
            <ul>
                <li><strong>Typography tương phản cao:</strong> Sử dụng font chữ có chân (Serif) cho tiêu đề và font không chân (Sans-serif) cho văn bản thân bài giúp mắt người đọc không bị mỏi.</li>
                <li><strong>Mobile-first tuyệt đối:</strong> Hơn 85% lưu lượng đọc tin tức đến từ điện thoại thông minh. Thiết kế phải thân thiện với thao tác vuốt chạm một tay.</li>
                <li><strong>Tối ưu hóa Core Web Vitals:</strong> Giảm thiểu thời gian hiển thị nội dung đầu tiên (LCP) và loại bỏ hiện tượng giật cục khung hình (CLS).</li>
            </ul>',
            'thumbnail' => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=1200&auto=format&fit=crop&q=80',
            'category_slug' => 'cong-nghe',
            'is_featured' => 0,
            'is_breaking' => 0,
            'views' => 610,
            'tags' => 'UI/UX, Thiết kế Web, Báo điện tử, Typography'
        ],
        [
            'title' => 'Sống tối giản kỹ thuật số (Digital Minimalism): Bí quyết tái tạo năng lượng cho người làm sáng tạo',
            'slug' => 'song-toi-gian-ky-thuat-so-bi-quyet-tai-tao-nang-luong',
            'excerpt' => 'Làm thế nào để duy trì sự tập trung sâu giữa cơn bão thông báo từ điện thoại và mạng xã hội mỗi ngày?',
            'content' => '<p class="lead">Trong thời đại mà sự chú ý trở thành loại tiền tệ quý giá nhất, biết cách "ngắt kết nối để kết nối lại" là kỹ năng sinh tồn của mọi chuyên gia hiện đại.</p>
            <p>Phương pháp <em>Digital Detox</em> cuối tuần kết hợp cùng quy tắc <strong>"Không màn hình trước khi đi ngủ 1 tiếng"</strong> đã giúp hàng triệu người cải thiện chất lượng giấc ngủ và khơi dậy nguồn cảm hứng sáng tạo sâu sắc.</p>',
            'thumbnail' => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=1200&auto=format&fit=crop&q=80',
            'category_slug' => 'doi-song-so',
            'is_featured' => 0,
            'is_breaking' => 0,
            'views' => 482,
            'tags' => 'Đời sống số, Tối giản, Sức khỏe tinh thần'
        ],
        [
            'title' => 'Bảo mật Web PHP hiện đại: Chống SQL Injection, XSS và CSRF toàn diện',
            'slug' => 'bao-mat-web-php-hien-dai-sql-injection-xss-csrf',
            'excerpt' => 'Hướng dẫn chi tiết các kỹ thuật phòng thủ chuẩn OWASP Top 10 dành cho lập trình viên phát triển CMS và ứng dụng web PHP.',
            'content' => '<p class="lead">Một website tin tức dù đẹp đến đâu nhưng nếu bị lộ lọt dữ liệu hoặc bị tấn công thay đổi giao diện (Deface) thì uy tín thương hiệu sẽ bị tổn hại nghiêm trọng.</p>
            <h3>1. Sử dụng PDO Prepared Statements</h3>
            <p>Không bao giờ nối chuỗi trực tiếp dữ liệu người dùng vào câu truy vấn SQL. Luôn dùng tham số hóa (Prepared Statements) để triệt tiêu 100% rủi ro SQL Injection.</p>
            <h3>2. Làm sạch dữ liệu đầu ra để chống XSS</h3>
            <p>Bất kỳ nội dung nào do người dùng nhập (như bình luận, form liên hệ) khi xuất ra HTML đều phải qua <code>htmlspecialchars(..., ENT_QUOTES, \'UTF-8\')</code>.</p>
            <h3>3. Mã hóa mật khẩu an toàn với BCRYPT</h3>
            <p>Luôn sử dụng hàm chuẩn <code>password_hash()</code> và <code>password_verify()</code> của PHP hiện đại, tuyệt đối không dùng MD5 hay SHA1 cổ điển.</p>',
            'thumbnail' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=1200&auto=format&fit=crop&q=80',
            'category_slug' => 'bao-mat-web',
            'is_featured' => 0,
            'is_breaking' => 0,
            'views' => 834,
            'tags' => 'PHP, Bảo mật, Cybersecurity, PDO, OWASP'
        ]
    ];

    $stmtArt = $pdo->prepare("
        INSERT INTO articles (title, slug, excerpt, content, thumbnail, category_id, author_id, status, is_featured, is_breaking, views_count, tags, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'published', ?, ?, ?, ?, ?)
    ");

    $articleIds = [];
    $timeOffset = 0;
    foreach ($articles as $art) {
        $catId = $catMap[$art['category_slug']] ?? 1;
        $date = date('Y-m-d H:i:s', time() - ($timeOffset * 3600 * 4));
        $stmtArt->execute([
            $art['title'],
            $art['slug'],
            $art['excerpt'],
            $art['content'],
            $art['thumbnail'],
            $catId,
            $adminId,
            $art['is_featured'],
            $art['is_breaking'],
            $art['views'],
            $art['tags'],
            $date
        ]);
        $articleIds[] = $pdo->lastInsertId();
        $timeOffset++;
    }

    // Nạp bình luận mẫu
    if (!empty($articleIds)) {
        $comments = [
            [$articleIds[0], 'Nguyễn Văn Minh', 'minh.nguyen@gmail.com', 'Bài viết phân tích về AI Agent rất sâu sắc và đúng với thực tế doanh nghiệp hiện nay. Cảm ơn tòa soạn!'],
            [$articleIds[0], 'Lê Thị Thu', 'thule.tech@yahoo.com', 'Mình đang áp dụng AI Agent để kiểm thử API, năng suất tăng rõ rệt.'],
            [$articleIds[1], 'Trần Hoàng Long', 'long.tran@outlook.com', 'Bài hướng dẫn trỏ DNS rất chi tiết, mình đã trỏ thành công tên miền .vn về hosting cPanel chỉ sau 15 phút.'],
            [$articleIds[2], 'Phạm Mai Hương', 'huong.pham@fpt.vn', 'Mã QR thanh toán giờ ở đâu cũng có, rất tiện lợi cho người tiêu dùng.']
        ];

        $stmtCom = $pdo->prepare("
            INSERT INTO comments (article_id, author_name, author_email, content, status, created_at)
            VALUES (?, ?, ?, ?, 'approved', CURRENT_TIMESTAMP)
        ");
        foreach ($comments as $c) {
            $stmtCom->execute($c);
        }
    }
}
