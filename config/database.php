<?php
/**
 * V-NEWS CMS - Kết nối Cơ sở dữ liệu Thông minh (Smart Dual-Environment Connector)
 * Tự động chuyển đổi giữa Local SQLite và MySQL Hosting InfinityFree
 */

// Tự động nhận diện môi trường: Nếu chạy trên máy cục bộ (localhost) dùng SQLite, nếu trên Hosting dùng MySQL
$is_localhost = in_array($_SERVER['HTTP_HOST'] ?? 'localhost', ['localhost', 'localhost:8000', '127.0.0.1', '127.0.0.1:8000']);
define('DB_TYPE', $is_localhost ? 'sqlite' : 'mysql');

// Thông số kết nối InfinityFree (Dành cho gennew.rf.gd & novagen.io.vn)
define('DB_HOST', 'sql311.infinityfree.com');
define('DB_NAME', 'if0_43481176_vnews');
define('DB_USER', 'if0_43481176');
define('DB_PASS', '094486274258');
define('DB_CHARSET', 'utf8mb4');

// Cấu hình SQLite cục bộ
define('SQLITE_FILE', STORAGE_PATH . '/database.sqlite');

try {
    if (DB_TYPE === 'sqlite') {
        $need_seed = !file_exists(SQLITE_FILE);
        $pdo = new PDO('sqlite:' . SQLITE_FILE);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON;');
        
        if ($need_seed) {
            require_once ROOT_PATH . '/database/sqlite_init.php';
            initialize_sqlite_database($pdo);
        }
    } else {
        // Kết nối MySQL Hosting
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
} catch (PDOException $e) {
    // Dự phòng: nếu MySQL trên host lỗi thì chuyển sang SQLite
    if (file_exists(SQLITE_FILE)) {
        $pdo = new PDO('sqlite:' . SQLITE_FILE);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } else {
        die('<div style="font-family: sans-serif; padding: 2rem; background: #fff1f2; border: 1px solid #fda4af; border-radius: 8px; margin: 2rem auto; max-width: 600px;">
            <h3 style="color: #9f1239; margin-top: 0;">Lỗi kết nối cơ sở dữ liệu!</h3>
            <p style="color: #4b5563;">' . htmlspecialchars($e->getMessage()) . '</p>
        </div>');
    }
}
