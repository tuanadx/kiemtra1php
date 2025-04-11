<?php
/**
 * Lớp Controller cơ sở
 * Tải models và views
 */
class Controller {
    // Phương thức load model
    public function model($model) {
        // Yêu cầu file model
        require_once '../app/models/' . $model . '.php';

        // Khởi tạo model
        return new $model();
    }

    // Phương thức load view
    public function view($view, $data = []) {
        // Kiểm tra file view tồn tại
        if(file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            // View không tồn tại
            die('View không tồn tại');
        }
    }

    // Phương thức chuyển hướng
    public function redirect($page) {
        header('location: ' . URL_ROOT . '/' . $page);
    }

    // Phương thức trả về JSON
    public function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
} 