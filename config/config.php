<?php
// Cấu hình cơ sở dữ liệu
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'cuahangsach');

// Cấu hình URL - Sử dụng URL từ Railway nếu có
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$url_root = getenv('RAILWAY_STATIC_URL') ?: $protocol . $_SERVER['HTTP_HOST'];

// Cấu hình URL
define('BASE_URL', $url_root);
define('SITE_NAME', getenv('SITE_NAME') ?: 'Nhã Nam');

// Thư mục
define('APP_ROOT', dirname(dirname(__FILE__)) . '/app');
define('APPROOT', dirname(dirname(__FILE__)) . '/app');
define('URL_ROOT', $url_root);
define('URL_SUBFOLDER', '');

// Cấu hình debug
define('DEBUG', getenv('APP_ENV') === 'development'); 