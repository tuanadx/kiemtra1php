<?php
// Định nghĩa đường dẫn gốc
define('ROOT_PATH', __DIR__);

// Nếu yêu cầu đến /public/*, phục vụ file tĩnh
if (preg_match('/^\/public\//', $_SERVER['REQUEST_URI'])) {
    // Cắt bỏ '/public/' và trả về file tĩnh từ thư mục public
    $file = ROOT_PATH . $_SERVER['REQUEST_URI'];
    if (file_exists($file)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mime_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
        ];
        
        if (isset($mime_types[$ext])) {
            header('Content-Type: ' . $mime_types[$ext]);
        }
        
        readfile($file);
        exit;
    }
}

// Mặc định, chuyển hướng đến public/index.php
require_once __DIR__ . '/public/index.php'; 