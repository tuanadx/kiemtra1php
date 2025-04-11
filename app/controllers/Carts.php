<?php
class Carts extends Controller {
    private $cartModel;
    private $bookModel;
    private $userModel;

    public function __construct() {
        $this->cartModel = $this->model('Cart');
        $this->bookModel = $this->model('Book');
        $this->userModel = $this->model('User');
        
        // Kiểm tra giỏ hàng tạm thời khi người dùng đăng nhập
        if(isset($_SESSION['user_id'])) {
            $this->cartModel->restoreTemporaryCart($_SESSION['user_id']);
        }
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
            
            // Tính toán thuế, phí vận chuyển và tổng đơn hàng
            $vat = $this->cartModel->calculateVAT($cart->id);
            $shipping = $this->cartModel->calculateShippingFee($cart->id);
            $orderTotal = $this->cartModel->calculateOrderTotal($cart->id);
            
            // Lưu số lượng sản phẩm trong giỏ hàng vào session
            $totalQuantity = $this->cartModel->getTotalQuantity($cart->id);
            $_SESSION['cart_count'] = $totalQuantity;
            
            $data = [
                'title' => 'Giỏ hàng',
                'cart' => $cart,
                'cartItems' => $cartItems,
                'vat' => $vat,
                'shipping' => $shipping,
                'orderTotal' => $orderTotal,
                'free_shipping_threshold' => $this->cartModel->getFreeShippingThreshold()
            ];

            $this->view('carts/index', $data);
        } else {
            redirect('home');
        }
    }

    // Phương thức thêm sản phẩm vào giỏ hàng
    public function add() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Log request
            error_log('Yêu cầu thêm vào giỏ hàng: ' . json_encode($_POST));
            
            // Xử lý dữ liệu
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            error_log('bookId: ' . $bookId . ', quantity: ' . $quantity);
            
            if($quantity <= 0) {
                $quantity = 1;
            }
            
            // Lấy thông tin sách
            $book = $this->bookModel->getBookById($bookId);
            
            error_log('Book info: ' . ($book ? json_encode($book) : 'null'));
            
            if(!$book) {
                $this->jsonResponse(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
                return;
            }
            
            // Kiểm tra đăng nhập
            if(isset($_SESSION['user_id'])) {
                // Đã đăng nhập - thêm vào giỏ hàng trong cơ sở dữ liệu
                $cart = $this->cartModel->getCartByUserId($_SESSION['user_id']);
                
                // Thêm sản phẩm vào giỏ hàng
                if($this->cartModel->addItem($cart->id, $book->id, $quantity, $book->gia_tien)) {
                    // Lấy số lượng sản phẩm trong giỏ hàng
                    $totalQuantity = $this->cartModel->getTotalQuantity($cart->id);
                    $_SESSION['cart_count'] = $totalQuantity;
                    
                    $this->jsonResponse([
                        'success' => true, 
                        'message' => 'Đã thêm sản phẩm vào giỏ hàng', 
                        'count' => $totalQuantity
                    ]);
                } else {
                    $this->jsonResponse(['success' => false, 'message' => 'Không thể thêm sản phẩm vào giỏ hàng']);
                }
            } else {
                // Chưa đăng nhập - sử dụng Session để lưu giỏ hàng
                if(!isset($_SESSION['temp_cart'])) {
                    $_SESSION['temp_cart'] = [];
                    $_SESSION['temp_cart_total'] = 0;
                }
                
                // Kiểm tra sản phẩm đã tồn tại trong giỏ hàng chưa
                $found = false;
                foreach($_SESSION['temp_cart'] as &$item) {
                    if($item['book_id'] == $book->id) {
                        $item['quantity'] += $quantity;
                        $item['total'] = $item['quantity'] * $item['price'];
                        $found = true;
                        break;
                    }
                }
                
                // Nếu chưa tồn tại, thêm mới
                if(!$found) {
                    $_SESSION['temp_cart'][] = [
                        'book_id' => $book->id,
                        'name' => $book->ten_sach,
                        'price' => $book->gia_tien,
                        'quantity' => $quantity,
                        'image' => $book->anh,
                        'total' => $quantity * $book->gia_tien
                    ];
                }
                
                // Tính lại tổng tiền
                $_SESSION['temp_cart_total'] = 0;
                foreach($_SESSION['temp_cart'] as $item) {
                    $_SESSION['temp_cart_total'] += $item['total'];
                }
                
                // Lấy số lượng sản phẩm trong giỏ hàng
                $count = count($_SESSION['temp_cart']);
                $_SESSION['cart_count'] = $count;
                
                $this->jsonResponse([
                    'success' => true, 
                    'message' => 'Đã thêm sản phẩm vào giỏ hàng. Đăng nhập để lưu giỏ hàng của bạn!', 
                    'count' => $count
                ]);
            }
        } else {
            redirect('home');
        }
    }

    // Phương thức cập nhật số lượng sản phẩm
    public function update() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Xử lý dữ liệu
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $cartDetailId = isset($_POST['cart_detail_id']) ? (int)$_POST['cart_detail_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            // Kiểm tra đăng nhập
            if(isset($_SESSION['user_id'])) {
                // Đã đăng nhập - cập nhật giỏ hàng trong cơ sở dữ liệu
                if($this->cartModel->updateQuantity($cartDetailId, $quantity)) {
                    // Lấy giỏ hàng của người dùng
                    $cart = $this->cartModel->getCartByUserId($_SESSION['user_id']);
                    
                    // Lấy sản phẩm trong giỏ hàng
                    $cartItems = $this->cartModel->getCartItems($cart->id);
                    
                    // Tính toán thuế, phí vận chuyển và tổng đơn hàng
                    $vat = $this->cartModel->calculateVAT($cart->id);
                    $shipping = $this->cartModel->calculateShippingFee($cart->id);
                    $orderTotal = $this->cartModel->calculateOrderTotal($cart->id);
                    
                    // Cập nhật số lượng sản phẩm trong giỏ hàng vào session
                    $totalQuantity = $this->cartModel->getTotalQuantity($cart->id);
                    $_SESSION['cart_count'] = $totalQuantity;
                    
                    $this->jsonResponse([
                        'success' => true, 
                        'message' => 'Đã cập nhật giỏ hàng', 
                        'cart' => $cart,
                        'cartItems' => $cartItems,
                        'vat' => $vat,
                        'shipping' => $shipping,
                        'orderTotal' => $orderTotal,
                        'count' => $totalQuantity
                    ]);
                } else {
                    $this->jsonResponse(['success' => false, 'message' => 'Không thể cập nhật giỏ hàng']);
                }
            } else {
                // Chưa đăng nhập - cập nhật giỏ hàng trong session
                if(!isset($_SESSION['temp_cart']) || empty($_SESSION['temp_cart'])) {
                    $this->jsonResponse(['success' => false, 'message' => 'Giỏ hàng trống']);
                    return;
                }
                
                // Tìm sản phẩm trong giỏ hàng
                $index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
                
                if($index >= 0 && $index < count($_SESSION['temp_cart'])) {
                    if($quantity <= 0) {
                        // Xóa sản phẩm
                        array_splice($_SESSION['temp_cart'], $index, 1);
                    } else {
                        // Cập nhật số lượng
                        $_SESSION['temp_cart'][$index]['quantity'] = $quantity;
                        $_SESSION['temp_cart'][$index]['total'] = $quantity * $_SESSION['temp_cart'][$index]['price'];
                    }
                    
                    // Tính lại tổng tiền
                    $_SESSION['temp_cart_total'] = 0;
                    foreach($_SESSION['temp_cart'] as $item) {
                        $_SESSION['temp_cart_total'] += $item['total'];
                    }
                    
                    // Lấy số lượng sản phẩm trong giỏ hàng
                    $count = count($_SESSION['temp_cart']);
                    $_SESSION['cart_count'] = $count;
                    
                    $this->jsonResponse([
                        'success' => true, 
                        'message' => 'Đã cập nhật giỏ hàng', 
                        'cart' => ['tong_tien' => $_SESSION['temp_cart_total']],
                        'cartItems' => $_SESSION['temp_cart'],
                        'count' => $count
                    ]);
                } else {
                    $this->jsonResponse(['success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ hàng']);
                }
            }
        } else {
            redirect('carts');
        }
    }

    // Phương thức xóa sản phẩm khỏi giỏ hàng
    public function remove($id = 0) {
        // Kiểm tra đăng nhập
        if(isset($_SESSION['user_id'])) {
            // Đã đăng nhập - xóa sản phẩm khỏi giỏ hàng trong cơ sở dữ liệu
            $id = (int)$id;
            
            if($id <= 0) {
                redirect('carts');
            }
            
            // Xóa sản phẩm khỏi giỏ hàng
            if($this->cartModel->removeItem($id)) {
                // Cập nhật số lượng sản phẩm trong giỏ hàng vào session
                $cart = $this->cartModel->getCartByUserId($_SESSION['user_id']);
                $totalQuantity = $this->cartModel->getTotalQuantity($cart->id);
                $_SESSION['cart_count'] = $totalQuantity;
                
                // Set flash message
                // flash('cart_message', 'Đã xóa sản phẩm khỏi giỏ hàng');
            }
        } else {
            // Chưa đăng nhập - xóa sản phẩm khỏi giỏ hàng trong session
            $index = (int)$id;
            
            if(isset($_SESSION['temp_cart']) && !empty($_SESSION['temp_cart']) && $index >= 0 && $index < count($_SESSION['temp_cart'])) {
                // Xóa sản phẩm
                array_splice($_SESSION['temp_cart'], $index, 1);
                
                // Tính lại tổng tiền
                $_SESSION['temp_cart_total'] = 0;
                foreach($_SESSION['temp_cart'] as $item) {
                    $_SESSION['temp_cart_total'] += $item['total'];
                }
                
                // Lấy số lượng sản phẩm trong giỏ hàng
                $count = count($_SESSION['temp_cart']);
                $_SESSION['cart_count'] = $count;
            }
        }
        
        redirect('carts');
    }

    // Phương thức xóa toàn bộ giỏ hàng
    public function clear() {
        // Kiểm tra đăng nhập
        if(isset($_SESSION['user_id'])) {
            // Đã đăng nhập - xóa toàn bộ giỏ hàng trong cơ sở dữ liệu
            $cart = $this->cartModel->getCartByUserId($_SESSION['user_id']);
            
            // Xóa toàn bộ giỏ hàng
            if($this->cartModel->clearCart($cart->id)) {
                // Cập nhật số lượng sản phẩm trong giỏ hàng vào session
                $_SESSION['cart_count'] = 0;
                
                // Set flash message
                // flash('cart_message', 'Đã xóa toàn bộ giỏ hàng');
            }
        } else {
            // Chưa đăng nhập - xóa toàn bộ giỏ hàng trong session
            if(isset($_SESSION['temp_cart'])) {
                $_SESSION['temp_cart'] = [];
                $_SESSION['temp_cart_total'] = 0;
                $_SESSION['cart_count'] = 0;
            }
        }
        
        redirect('carts');
    }

    // Phương thức mua ngay
    public function buyNow() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            
            // Kiểm tra đăng nhập
            if(isset($_SESSION['user_id'])) {
                // Đã đăng nhập - thêm vào giỏ hàng trong cơ sở dữ liệu
                $cart = $this->cartModel->getCartByUserId($_SESSION['user_id']);
                
                // Xóa toàn bộ giỏ hàng trước khi thêm sản phẩm mới
                $this->cartModel->clearCart($cart->id);
                
                // Thêm sản phẩm vào giỏ hàng
                if($this->cartModel->addItem($cart->id, $book->id, $quantity, $book->gia_tien)) {
                    // Cập nhật số lượng sản phẩm trong giỏ hàng vào session
                    $_SESSION['cart_count'] = $quantity;
                    
                    $this->jsonResponse([
                        'success' => true, 
                        'message' => 'Đã thêm sản phẩm vào giỏ hàng', 
                        'redirect' => URL_ROOT . '/carts'
                    ]);
                } else {
                    $this->jsonResponse(['success' => false, 'message' => 'Không thể thêm sản phẩm vào giỏ hàng']);
                }
            } else {
                // Chưa đăng nhập - sử dụng Session để lưu giỏ hàng
                $_SESSION['temp_cart'] = [];
                $_SESSION['temp_cart_total'] = 0;
                
                // Thêm sản phẩm vào giỏ hàng tạm thời
                $_SESSION['temp_cart'][] = [
                    'book_id' => $book->id,
                    'name' => $book->ten_sach,
                    'price' => $book->gia_tien,
                    'quantity' => $quantity,
                    'image' => $book->anh,
                    'total' => $quantity * $book->gia_tien
                ];
                
                // Tính tổng tiền
                $_SESSION['temp_cart_total'] = $quantity * $book->gia_tien;
                $_SESSION['cart_count'] = 1;
                
                $this->jsonResponse([
                    'success' => true, 
                    'message' => 'Đã thêm sản phẩm vào giỏ hàng. Đăng nhập để tiếp tục mua hàng!', 
                    'redirect' => URL_ROOT . '/users/login'
                ]);
            }
        } else {
            redirect('home');
        }
    }
    
    // Phương thức tạm lưu giỏ hàng
    public function saveTemporary() {
        if(isset($_SESSION['user_id'])) {
            $cart = $this->cartModel->getCartByUserId($_SESSION['user_id']);
            $this->cartModel->saveTemporaryCart($_SESSION['user_id'], $cart->id);
            $this->jsonResponse(['success' => true, 'message' => 'Đã lưu giỏ hàng tạm thời']);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Vui lòng đăng nhập để lưu giỏ hàng']);
        }
    }
    
    // Phương thức chuyển đổi giỏ hàng từ session sang cơ sở dữ liệu sau khi đăng nhập
    public function transferTempCart() {
        if(isset($_SESSION['user_id']) && isset($_SESSION['temp_cart']) && !empty($_SESSION['temp_cart'])) {
            $cart = $this->cartModel->getCartByUserId($_SESSION['user_id']);
            
            foreach($_SESSION['temp_cart'] as $item) {
                $this->cartModel->addItem($cart->id, $item['book_id'], $item['quantity'], $item['price']);
            }
            
            // Xóa giỏ hàng tạm thời trong session sau khi đã chuyển đổi
            unset($_SESSION['temp_cart']);
            unset($_SESSION['temp_cart_total']);
            
            // Cập nhật số lượng sản phẩm trong giỏ hàng vào session
            $totalQuantity = $this->cartModel->getTotalQuantity($cart->id);
            $_SESSION['cart_count'] = $totalQuantity;
            
            redirect('carts');
        } else {
            redirect('home');
        }
    }
    
    // Method kiểm tra debug
    public function debug() {
        echo "<h1>Debug Cart</h1>";
        
        // Kiểm tra xem có sách trong CSDL không
        $books = $this->bookModel->getBooks(10);
        
        echo "<h2>Sách trong CSDL:</h2>";
        if (count($books) > 0) {
            echo "<ul>";
            foreach($books as $book) {
                echo "<li>ID: {$book->id}, Tên: {$book->ten_sach}, Giá: " . number_format($book->gia_tien, 0, ',', '.') . " VND</li>";
            }
            echo "</ul>";
            
            echo "<h2>Thử thêm sách vào giỏ hàng</h2>";
            $firstBook = $books[0];
            
            echo "<form method='post' action='" . URL_ROOT . "/carts/add'>";
            echo "<input type='hidden' name='book_id' value='{$firstBook->id}'>";
            echo "<input type='hidden' name='quantity' value='1'>";
            echo "<button type='submit'>Thêm sách '{$firstBook->ten_sach}' vào giỏ hàng</button>";
            echo "</form>";
            
            echo "<h2>Kiểm tra JavaScript</h2>";
            echo "<button class='debug-add-cart' data-book-id='{$firstBook->id}'>Thêm sách '{$firstBook->ten_sach}' bằng JavaScript</button>";
            
            echo "<script>
                document.querySelector('.debug-add-cart').addEventListener('click', function() {
                    const bookId = this.dataset.bookId;
                    const formData = new FormData();
                    formData.append('book_id', bookId);
                    formData.append('quantity', 1);
                    
                    console.log('Debug: sending request to add book', bookId);
                    
                    fetch('" . URL_ROOT . "/carts/add', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Response:', data);
                        alert(data.message || 'Response received');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra: ' + error);
                    });
                });
            </script>";
        } else {
            echo "<p>Không có sách nào trong CSDL. Vui lòng thêm sách trước.</p>";
        }
        
        // Kiểm tra biến session
        echo "<h2>Session hiện tại:</h2>";
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";
    }
    
    // Hàm JSON Response
    public function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}