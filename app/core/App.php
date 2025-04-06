<?php
/**
 * Lớp App
 * Tạo URL và load controller
 * URL format: /controller/method/params
 */
class App {
    protected $controller = 'Home';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();
        
        // Tìm controller
        if(isset($url[0]) && file_exists('../app/controllers/' . ucwords($url[0]) . '.php')) {
            // Nếu tồn tại, set làm controller
            $this->controller = ucwords($url[0]);
            // Xóa từ mảng
            unset($url[0]);
        }

        // Yêu cầu controller
        require_once '../app/controllers/' . $this->controller . '.php';

        // Khởi tạo controller
        $this->controller = new $this->controller;

        // Kiểm tra method thứ 2 trong URL
        if(isset($url[1])) {
            // Kiểm tra nếu method tồn tại trong controller
            if(method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                // Xóa từ mảng
                unset($url[1]);
            }
        }

        // Lấy params
        $this->params = $url ? array_values($url) : [];

        // Gọi callback với mảng params
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function getUrl() {
        if(isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        
        return ['home', 'index'];
    }
} 