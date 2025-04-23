<?php
// Lấy thông tin kết nối từ Railway hoặc sử dụng local
$dbUrl = getenv('DATABASE_URL');

if ($dbUrl) {
    // Trên Railway
    $dbInfo = parse_url($dbUrl);
    define('DB_HOST', $dbInfo['host']);
    define('DB_USER', $dbInfo['user']);
    define('DB_PASS', $dbInfo['pass']);
    define('DB_NAME', ltrim($dbInfo['path'], '/'));
} else {
    // Local
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'cuahangsach');
}

// Cấu hình URL
$appUrl = getenv('RAILWAY_STATIC_URL') ?: 'http://localhost/ktra2php';
define('BASE_URL', $appUrl);
define('SITE_NAME', 'Nhã Nam');

// Thư mục
define('APP_ROOT', dirname(dirname(__FILE__)) . '/app');
define('APPROOT', dirname(dirname(__FILE__)) . '/app');
define('URL_ROOT', $appUrl);
define('URL_SUBFOLDER', '');

// Cấu hình debug
define('DEBUG', getenv('DEBUG') ?: true); 