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
        // Khởi tạo biến data
        $data = [
            'title' => 'Giỏ hàng',
            'cartItems' => [],
            'vat' => 0,
            'shipping' => 0,
            'orderTotal' => 0,
            'free_shipping_threshold' => $this->cartModel->getFreeShippingThreshold(),
            'is_logged_in' => isset($_SESSION['user_id'])
        ];

        // Kiểm tra đăng nhập
        if(isset($_SESSION['user_id'])) {
            // Đã đăng nhập - lấy giỏ hàng từ cơ sở dữ liệu
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
                
                $data['cart'] = $cart;
                $data['cartItems'] = $cartItems;
                $data['vat'] = $vat;
                $data['shipping'] = $shipping;
                $data['orderTotal'] = $orderTotal;
            }
        } else {
            // Chưa đăng nhập - lấy giỏ hàng từ session
            if(isset($_SESSION['temp_cart']) && !empty($_SESSION['temp_cart'])) {
                $cartItems = [];
                $subTotal = 0;
                
                // Tính tổng tiền từ các sản phẩm
                foreach($_SESSION['temp_cart'] as $index => $item) {
                    // Lấy thông tin sách mới nhất từ cơ sở dữ liệu
                    $book = $this->bookModel->getBookById($item['book_id']);
                    if($book) {
                        // Cập nhật giá mới nhất
                        $price = $book->gia_tien;
                        $total = $price * $item['quantity'];
                        
                        $cartItem = (object)[
                            'temp_id' => $index, // ID tạm thời để xác định khi xóa
                            'book_id' => $book->id,
                            'ten_sach' => $book->ten_sach,
                            'tac_gia' => $book->tac_gia,
                            'anh' => $book->anh,
                            'gia_tien' => $price,
                            'so_luong' => $item['quantity'],
                            'thanh_tien' => $total
                        ];
                        
                        $cartItems[] = $cartItem;
                        $subTotal += $total;
                    }
                
                }
                
                // Tính thuế VAT (10%)
                $vat = $subTotal * 0.1;
                
                // Tính phí vận chuyển (30,000 VND nếu dưới 300,000 VND)
                $shipping = ($subTotal < 300000) ? 30000 : 0;
                
                // Tính tổng tiền đơn hàng
                $orderTotal = $subTotal + $vat + $shipping;
                
                $data['cartItems'] = $cartItems;
                $data['vat'] = $vat;
                $data['shipping'] = $shipping;
                $data['orderTotal'] = $orderTotal;
                $data['subTotal'] = $subTotal;
            }
        }

        $this->view('carts/index', $data);
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
                
                // Kiểm tra xem sản phẩm đã tồn tại trong giỏ hàng chưa
                $existingItem = $this->cartModel->getCartItemByBookId($cart->id, $book->id);
                
                if($existingItem) {
                    // Nếu sản phẩm đã tồn tại, cập nhật số lượng
                    $newQuantity = $existingItem->so_luong + $quantity;
                    if($this->cartModel->updateQuantity($existingItem->id, $newQuantity)) {
                        // Lấy số lượng sản phẩm trong giỏ hàng
                        $totalQuantity = $this->cartModel->getTotalQuantity($cart->id);
                        $_SESSION['cart_count'] = $totalQuantity;
                        
                        $this->jsonResponse([
                            'success' => true, 
                            'message' => 'Đã cập nhật số lượng sản phẩm trong giỏ hàng', 
                            'count' => $totalQuantity
                        ]);
                    } else {
                        $this->jsonResponse(['success' => false, 'message' => 'Không thể cập nhật số lượng sản phẩm']);
                    }
                } else {
                    // Nếu sản phẩm chưa tồn tại, thêm mới
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
                }
            } else {
                // Chưa đăng nhập - sử dụng Session để lưu giỏ hàng
                if(!isset($_SESSION['temp_cart'])) {
                    $_SESSION['temp_cart'] = [];
                    $_SESSION['temp_cart_total'] = 0;
                }
                
                // Log để debug
                error_log('Temp cart before: ' . json_encode($_SESSION['temp_cart']));
                error_log('Adding book: ' . json_encode($book));
                
                // Kiểm tra sản phẩm đã tồn tại trong giỏ hàng chưa
                $found = false;
                $index = -1;
                
                foreach($_SESSION['temp_cart'] as $key => $item) {
                    if($item['book_id'] == $book->id) {
                        $found = true;
                        $index = $key;
                        break;
                    }
                }
                
                if($found) {
                    // Nếu sản phẩm đã tồn tại, cập nhật số lượng
                    $_SESSION['temp_cart'][$index]['quantity'] += $quantity;
                    $_SESSION['temp_cart'][$index]['total'] = $_SESSION['temp_cart'][$index]['quantity'] * $book->gia_tien;
                } else {
                    // Nếu chưa tồn tại, thêm mới
                    $_SESSION['temp_cart'][] = [
                        'book_id' => $book->id,
                        'name' => $book->ten_sach,
                        'price' => $book->gia_tien,
                        'quantity' => $quantity,
                        'image' => $book->anh,
                        'total' => $quantity * $book->gia_tien
                    ];
                }
                
                // Log để debug
                error_log('Temp cart after: ' . json_encode($_SESSION['temp_cart']));
                
                // Tính lại tổng tiền
                $subTotal = 0;
                foreach($_SESSION['temp_cart'] as $item) {
                    $subTotal += $item['total'];
                }
                $_SESSION['temp_cart_total'] = $subTotal;
                
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
        if(isset($_SESSION['user_id'])) {
            // Đã đăng nhập - xóa giỏ hàng trong cơ sở dữ liệu
            $cart = $this->cartModel->getCartByUserId($_SESSION['user_id']);
            
            if($cart && $this->cartModel->clearCart($cart->id)) {
                $_SESSION['cart_count'] = 0;
                redirect('carts');
            } else {
                redirect('home');
            }
        } else {
            // Chưa đăng nhập - xóa giỏ hàng trong session
            unset($_SESSION['temp_cart']);
            unset($_SESSION['temp_cart_total']);
            $_SESSION['cart_count'] = 0;
            redirect('carts');
        }
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
        
        // Hiển thị trạng thái đăng nhập
        echo "<h2>Trạng thái đăng nhập:</h2>";
        echo isset($_SESSION['user_id']) ? "Đã đăng nhập (ID: {$_SESSION['user_id']})" : "Chưa đăng nhập";
        
        // Kiểm tra giỏ hàng hiện tại
        echo "<h2>Giỏ hàng hiện tại:</h2>";
        if(isset($_SESSION['user_id'])) {
            $cart = $this->cartModel->getCartByUserId($_SESSION['user_id']);
            if($cart) {
                $cartItems = $this->cartModel->getCartItems($cart->id);
                echo "<h3>Giỏ hàng trong Database:</h3>";
                echo "<pre>";
                print_r($cartItems);
                echo "</pre>";
            } else {
                echo "<p>Chưa có giỏ hàng trong database</p>";
            }
        } else {
            echo "<h3>Giỏ hàng tạm thời (Session):</h3>";
            if(isset($_SESSION['temp_cart'])) {
                echo "<pre>";
                print_r($_SESSION['temp_cart']);
                echo "</pre>";
            } else {
                echo "<p>Chưa có giỏ hàng tạm thời</p>";
            }
        }
        
        // Kiểm tra xem có sách trong CSDL không
        $books = $this->bookModel->getBooks(5); // Lấy 5 cuốn sách để test
        
        echo "<h2>Sách có sẵn để test:</h2>";
        if (count($books) > 0) {
            echo "<ul>";
            foreach($books as $book) {
                echo "<li>ID: {$book->id}, Tên: {$book->ten_sach}, Giá: " . number_format($book->gia_tien, 0, ',', '.') . " VND</li>";
            }
            echo "</ul>";
            
            echo "<h2>Test thêm sách vào giỏ hàng:</h2>";
            foreach($books as $book) {
                echo "<div style='margin-bottom: 10px;'>";
                echo "<h3>{$book->ten_sach}</h3>";
                echo "<button class='debug-add-cart' data-book-id='{$book->id}' style='margin-right: 10px;'>Thêm vào giỏ hàng</button>";
                echo "<button class='debug-buy-now' data-book-id='{$book->id}'>Mua ngay</button>";
                echo "</div>";
            }
            
            echo "<script>
                // Hàm hiển thị kết quả debug
                function showDebugResult(data) {
                    console.log('Response:', data);
                    const debugResult = document.getElementById('debug-result');
                    debugResult.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                }
                
                // Xử lý thêm vào giỏ hàng
                document.querySelectorAll('.debug-add-cart').forEach(button => {
                    button.addEventListener('click', function() {
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
                            showDebugResult(data);
                            setTimeout(() => window.location.reload(), 2000);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Có lỗi xảy ra: ' + error);
                        });
                    });
                });
                
                // Xử lý mua ngay
                document.querySelectorAll('.debug-buy-now').forEach(button => {
                    button.addEventListener('click', function() {
                        const bookId = this.dataset.bookId;
                        const formData = new FormData();
                        formData.append('book_id', bookId);
                        formData.append('quantity', 1);
                        
                        console.log('Debug: sending request to buy now', bookId);
                        
                        fetch('" . URL_ROOT . "/carts/buyNow', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            showDebugResult(data);
                            if(data.redirect) {
                                setTimeout(() => window.location.href = data.redirect, 2000);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Có lỗi xảy ra: ' + error);
                        });
                    });
                });
            </script>";
            
            // Div để hiển thị kết quả debug
            echo "<h2>Kết quả thao tác:</h2>";
            echo "<div id='debug-result' style='background: #f5f5f5; padding: 10px; margin-top: 10px;'></div>";
        } else {
            echo "<p>Không có sách nào trong CSDL. Vui lòng thêm sách trước.</p>";
        }
        
        // Hiển thị toàn bộ session để debug
        echo "<h2>Toàn bộ Session:</h2>";
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

    // Phương thức cập nhật giỏ hàng tạm thời
    public function updateTemp() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Xử lý dữ liệu
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $tempId = isset($_POST['temp_id']) ? (int)$_POST['temp_id'] : -1;
            $bookId = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            
            if($quantity <= 0) {
                $quantity = 1;
            }
            
            if($tempId < 0 || !isset($_SESSION['temp_cart']) || !isset($_SESSION['temp_cart'][$tempId])) {
                $this->jsonResponse(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng']);
                return;
            }
            
            // Lấy thông tin sách để đảm bảo giá mới nhất
            $book = $this->bookModel->getBookById($bookId);
            if(!$book) {
                $this->jsonResponse(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
                return;
            }
            
            // Cập nhật số lượng
            $_SESSION['temp_cart'][$tempId]['quantity'] = $quantity;
            $_SESSION['temp_cart'][$tempId]['total'] = $quantity * $book->gia_tien;
            
            // Tính lại tổng tiền
            $subTotal = 0;
            foreach($_SESSION['temp_cart'] as $item) {
                $subTotal += $item['total'];
            }
            
            // Tính VAT và phí vận chuyển
            $vat = $subTotal * 0.1; // 10% VAT
            $shipping = ($subTotal < 300000) ? 30000 : 0; // Miễn phí vận chuyển nếu mua trên 300k
            $orderTotal = $subTotal + $vat + $shipping;
            
            // Lấy số lượng sản phẩm trong giỏ hàng
            $cartCount = count($_SESSION['temp_cart']);
            $_SESSION['cart_count'] = $cartCount;
            
            $this->jsonResponse([
                'success' => true, 
                'message' => 'Đã cập nhật giỏ hàng', 
                'item_total' => $_SESSION['temp_cart'][$tempId]['total'],
                'subTotal' => $subTotal,
                'vat' => $vat,
                'shipping' => $shipping,
                'orderTotal' => $orderTotal,
                'cart_count' => $cartCount,
                'free_shipping_threshold' => 300000 // Ngưỡng miễn phí vận chuyển
            ]);
        } else {
            redirect('home');
        }
    }
    
    // Phương thức xóa sản phẩm khỏi giỏ hàng tạm thời
    public function removeTemp() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Xử lý dữ liệu
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $tempId = isset($_POST['temp_id']) ? (int)$_POST['temp_id'] : -1;
            
            if($tempId < 0 || !isset($_SESSION['temp_cart']) || !isset($_SESSION['temp_cart'][$tempId])) {
                $this->jsonResponse(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ hàng']);
                return;
            }
            
            // Xóa sản phẩm
            unset($_SESSION['temp_cart'][$tempId]);
            
            // Đánh lại index cho mảng
            $_SESSION['temp_cart'] = array_values($_SESSION['temp_cart']);
            
            // Kiểm tra giỏ hàng còn sản phẩm không
            $isEmpty = empty($_SESSION['temp_cart']);
            
            if($isEmpty) {
                unset($_SESSION['temp_cart']);
                unset($_SESSION['temp_cart_total']);
                $_SESSION['cart_count'] = 0;
                
                $this->jsonResponse([
                    'success' => true, 
                    'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
                    'is_empty' => true
                ]);
                return;
            }
            
            // Tính lại tổng tiền
            $subTotal = 0;
            foreach($_SESSION['temp_cart'] as $item) {
                $subTotal += $item['total'];
            }
            
            // Tính VAT và phí vận chuyển
            $vat = $subTotal * 0.1; // 10% VAT
            $shipping = ($subTotal < 300000) ? 30000 : 0; // Miễn phí vận chuyển nếu mua trên 300k
            $orderTotal = $subTotal + $vat + $shipping;
            
            // Lấy số lượng sản phẩm trong giỏ hàng
            $cartCount = count($_SESSION['temp_cart']);
            $_SESSION['cart_count'] = $cartCount;
            
            $this->jsonResponse([
                'success' => true, 
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng', 
                'is_empty' => false,
                'subTotal' => $subTotal,
                'vat' => $vat,
                'shipping' => $shipping,
                'orderTotal' => $orderTotal,
                'cart_count' => $cartCount,
                'free_shipping_threshold' => 300000 // Ngưỡng miễn phí vận chuyển
            ]);
        } else {
            redirect('home');
        }
    }
}