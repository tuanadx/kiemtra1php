<?php
class Carts extends Controller {
    private $cartModel;
    private $bookModel;

    public function __construct() {
        $this->cartModel = $this->model('Cart');
        $this->bookModel = $this->model('Book');
    }

    // Phương thức hiển thị giỏ hàng
    public function index() {
        // Kiểm tra đăng nhập
        if(!isset($_SESSION['user_id'])) {
            redirect('users/login');
        }

        // Lấy giỏ hàng của người dùng
        $cart = $this->cartModel->getCartByUserId($_SESSION['user_id']);
        
        if($cart) {
            // Lấy sản phẩm trong giỏ hàng
            $cartItems = $this->cartModel->getCartItems($cart->id);
            
            $data = [
                'title' => 'Giỏ hàng',
                'cart' => $cart,
                'cartItems' => $cartItems
            ];

            $this->view('carts/index', $data);
        } else {
            redirect('home');
        }
    }

    // Phương thức thêm sản phẩm vào giỏ hàng
    public function add() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Kiểm tra đăng nhập
            if(!isset($_SESSION['user_id'])) {
                $this->jsonResponse(['success' => false, 'message' => 'Vui lòng đăng nhập để mua hàng']);
                return;
            }

            // Xử lý dữ liệu
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            if($quantity <= 0) {
                $quantity = 1;
            }
            
            // Lấy thông tin sách
            $book = $this->bookModel->getBookById($bookId);
            
            if(!$book) {
                $this->jsonResponse(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
                return;
            }
            
            // Lấy giỏ hàng của người dùng
            $cart = $this->cartModel->getCartByUserId($_SESSION['user_id']);
            
            // Thêm sản phẩm vào giỏ hàng
            if($this->cartModel->addItem($cart->id, $book->id, $quantity, $book->gia_tien)) {
                // Lấy số lượng sản phẩm trong giỏ hàng
                $itemsCount = $this->cartModel->countItems($cart->id);
                
                $this->jsonResponse([
                    'success' => true, 
                    'message' => 'Đã thêm sản phẩm vào giỏ hàng', 
                    'count' => $itemsCount
                ]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Không thể thêm sản phẩm vào giỏ hàng']);
            }
        } else {
            redirect('home');
        }
    }

    // Phương thức cập nhật số lượng sản phẩm
    public function update() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Kiểm tra đăng nhập
            if(!isset($_SESSION['user_id'])) {
                $this->jsonResponse(['success' => false, 'message' => 'Vui lòng đăng nhập để cập nhật giỏ hàng']);
                return;
            }

            // Xử lý dữ liệu
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $cartDetailId = isset($_POST['cart_detail_id']) ? (int)$_POST['cart_detail_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            // Cập nhật số lượng
            if($this->cartModel->updateQuantity($cartDetailId, $quantity)) {
                // Lấy giỏ hàng của người dùng
                $cart = $this->cartModel->getCartByUserId($_SESSION['user_id']);
                
                // Lấy sản phẩm trong giỏ hàng
                $cartItems = $this->cartModel->getCartItems($cart->id);
                
                $this->jsonResponse([
                    'success' => true, 
                    'message' => 'Đã cập nhật giỏ hàng', 
                    'cart' => $cart,
                    'cartItems' => $cartItems
                ]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Không thể cập nhật giỏ hàng']);
            }
        } else {
            redirect('carts');
        }
    }

    // Phương thức xóa sản phẩm khỏi giỏ hàng
    public function remove($id = 0) {
        // Kiểm tra đăng nhập
        if(!isset($_SESSION['user_id'])) {
            redirect('users/login');
        }

        $id = (int)$id;
        
        if($id <= 0) {
            redirect('carts');
        }
        
        // Xóa sản phẩm khỏi giỏ hàng
        if($this->cartModel->removeItem($id)) {
            // Set flash message
            // flash('cart_message', 'Đã xóa sản phẩm khỏi giỏ hàng');
        }
        
        redirect('carts');
    }

    // Phương thức xóa toàn bộ giỏ hàng
    public function clear() {
        // Kiểm tra đăng nhập
        if(!isset($_SESSION['user_id'])) {
            redirect('users/login');
        }

        // Lấy giỏ hàng của người dùng
        $cart = $this->cartModel->getCartByUserId($_SESSION['user_id']);
        
        // Xóa toàn bộ giỏ hàng
        if($this->cartModel->clearCart($cart->id)) {
            // Set flash message
            // flash('cart_message', 'Đã xóa toàn bộ giỏ hàng');
        }
        
        redirect('carts');
    }

    // Phương thức mua ngay
    public function buyNow() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Kiểm tra đăng nhập
            if(!isset($_SESSION['user_id'])) {
                $this->jsonResponse(['success' => false, 'message' => 'Vui lòng đăng nhập để mua hàng']);
                return;
            }

            // Xử lý dữ liệu
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            if($quantity <= 0) {
                $quantity = 1;
            }
            
            // Lấy thông tin sách
            $book = $this->bookModel->getBookById($bookId);
            
            if(!$book) {
                $this->jsonResponse(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
                return;
            }
            
            // Lấy giỏ hàng của người dùng
            $cart = $this->cartModel->getCartByUserId($_SESSION['user_id']);
            
            // Xóa toàn bộ giỏ hàng trước khi thêm sản phẩm mới
            $this->cartModel->clearCart($cart->id);
            
            // Thêm sản phẩm vào giỏ hàng
            if($this->cartModel->addItem($cart->id, $book->id, $quantity, $book->gia_tien)) {
                $this->jsonResponse([
                    'success' => true, 
                    'message' => 'Đã thêm sản phẩm vào giỏ hàng', 
                    'redirect' => URL_ROOT . '/carts'
                ]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Không thể thêm sản phẩm vào giỏ hàng']);
            }
        } else {
            redirect('home');
        }
    }
}