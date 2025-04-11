<?php
class Users extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    // Phương thức đăng ký
    public function register() {
        // Kiểm tra nếu đã đăng nhập
        if(isset($_SESSION['user_id'])) {
            redirect('home');
        }

        // Kiểm tra POST
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Xử lý form
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // Init data
            $data = [
                'ho_ten' => trim($_POST['ho_ten']),
                'email' => trim($_POST['email']),
                'so_dien_thoai' => trim($_POST['so_dien_thoai']),
                'mat_khau' => trim($_POST['mat_khau']),
                'xac_nhan_mat_khau' => trim($_POST['xac_nhan_mat_khau']),
                'ho_ten_err' => '',
                'email_err' => '',
                'so_dien_thoai_err' => '',
                'mat_khau_err' => '',
                'xac_nhan_mat_khau_err' => ''
            ];

            // Validate Email
            if(empty($data['email'])) {
                $data['email_err'] = 'Vui lòng nhập email';
            } else {
                // Kiểm tra email đã tồn tại
                if($this->userModel->findUserByEmail($data['email'])) {
                    $data['email_err'] = 'Email đã tồn tại';
                }
            }

            // Validate Name
            if(empty($data['ho_ten'])) {
                $data['ho_ten_err'] = 'Vui lòng nhập họ tên';
            }

            // Validate Password
            if(empty($data['mat_khau'])) {
                $data['mat_khau_err'] = 'Vui lòng nhập mật khẩu';
            } elseif(strlen($data['mat_khau']) < 6) {
                $data['mat_khau_err'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            }

            // Validate Confirm Password
            if(empty($data['xac_nhan_mat_khau'])) {
                $data['xac_nhan_mat_khau_err'] = 'Vui lòng xác nhận mật khẩu';
            } else {
                if($data['mat_khau'] != $data['xac_nhan_mat_khau']) {
                    $data['xac_nhan_mat_khau_err'] = 'Mật khẩu không khớp';
                }
            }

            // Make sure errors are empty
            if(empty($data['email_err']) && empty($data['ho_ten_err']) && empty($data['mat_khau_err']) && empty($data['xac_nhan_mat_khau_err'])) {
                // Validated
                
                // Hash Password
                $data['mat_khau'] = password_hash($data['mat_khau'], PASSWORD_DEFAULT);

                // Register User
                if($this->userModel->register($data)) {
                    // Set flash message
                    // flash('register_success', 'Đăng ký thành công, vui lòng đăng nhập');
                    // Redirect to login
                    redirect('users/login');
                } else {
                    die('Đã xảy ra lỗi');
                }
            } else {
                // Load view with errors
                $this->view('users/register', $data);
            }

        } else {
            // Init data
            $data = [
                'ho_ten' => '',
                'email' => '',
                'so_dien_thoai' => '',
                'mat_khau' => '',
                'xac_nhan_mat_khau' => '',
                'ho_ten_err' => '',
                'email_err' => '',
                'so_dien_thoai_err' => '',
                'mat_khau_err' => '',
                'xac_nhan_mat_khau_err' => ''
            ];

            // Load view
            $this->view('users/register', $data);
        }
    }

    // Phương thức đăng nhập
    public function login() {
        // Kiểm tra nếu đã đăng nhập
        if(isset($_SESSION['user_id'])) {
            redirect('home');
        }

        // Kiểm tra POST
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Init data
            $data = [
                'email' => trim($_POST['email']),
                'mat_khau' => trim($_POST['mat_khau']),
                'email_err' => '',
                'mat_khau_err' => '',      
            ];

            // Validate Email
            if(empty($data['email'])) {
                $data['email_err'] = 'Vui lòng nhập email';
            }

            // Validate Password
            if(empty($data['mat_khau'])) {
                $data['mat_khau_err'] = 'Vui lòng nhập mật khẩu';
            }

            // Check for user/email
            if($this->userModel->findUserByEmail($data['email'])) {
                // User found
            } else {
                // User not found
                $data['email_err'] = 'Không tìm thấy người dùng';
            }

            // Make sure errors are empty
            if(empty($data['email_err']) && empty($data['mat_khau_err'])) {
                // Validated
                // Check and set logged in user
                $loggedInUser = $this->userModel->login($data['email'], $data['mat_khau']);

                if($loggedInUser) {
                    // Create Session
                    $_SESSION['user_id'] = $loggedInUser->id;
                    $_SESSION['user_email'] = $loggedInUser->email;
                    $_SESSION['user_name'] = $loggedInUser->ho_ten;
                    
                    // Chuyển giỏ hàng từ session vào database
                    if(isset($_SESSION['temp_cart']) && !empty($_SESSION['temp_cart'])) {
                        redirect('carts/transferTempCart');
                    } else {
                        redirect('home');
                    }
                } else {
                    $data['mat_khau_err'] = 'Mật khẩu không chính xác';
                    $this->view('users/login', $data);
                }
            } else {
                // Load view with errors
                $this->view('users/login', $data);
            }

        } else {
            // Init data
            $data = [
                'email' => '',
                'mat_khau' => '',
                'email_err' => '',
                'mat_khau_err' => '',        
            ];

            // Load view
            $this->view('users/login', $data);
        }
    }

    // Phương thức đăng xuất
    public function logout() {
        // Xóa session
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        session_destroy();
        redirect('users/login');
    }

    // Phương thức hiển thị trang hồ sơ
    public function profile() {
        // Kiểm tra đăng nhập
        if(!isset($_SESSION['user_id'])) {
            redirect('users/login');
        }

        // Lấy thông tin người dùng
        $user = $this->userModel->getUserById($_SESSION['user_id']);

        $data = [
            'user' => $user
        ];

        $this->view('users/profile', $data);
    }

    // Phương thức cập nhật hồ sơ
    public function updateProfile() {
        // Kiểm tra đăng nhập
        if(!isset($_SESSION['user_id'])) {
            redirect('users/login');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Xử lý form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $_SESSION['user_id'],
                'ho_ten' => trim($_POST['ho_ten']),
                'so_dien_thoai' => trim($_POST['so_dien_thoai']),
                'dia_chi' => trim($_POST['dia_chi']),
                'ngay_sinh' => trim($_POST['ngay_sinh']),
                'gioi_tinh' => trim($_POST['gioi_tinh']),
                'ho_ten_err' => ''
            ];

            // Validate Name
            if(empty($data['ho_ten'])) {
                $data['ho_ten_err'] = 'Vui lòng nhập họ tên';
            }

            // Make sure errors are empty
            if(empty($data['ho_ten_err'])) {
                // Cập nhật hồ sơ
                if($this->userModel->updateProfile($data)) {
                    // Cập nhật tên trong session
                    $_SESSION['user_name'] = $data['ho_ten'];
                    redirect('users/profile');
                } else {
                    die('Đã xảy ra lỗi');
                }
            } else {
                // Load view with errors
                $this->view('users/edit_profile', $data);
            }
        } else {
            // Lấy thông tin người dùng
            $user = $this->userModel->getUserById($_SESSION['user_id']);

            $data = [
                'ho_ten' => $user->ho_ten,
                'so_dien_thoai' => $user->so_dien_thoai,
                'dia_chi' => $user->dia_chi,
                'ngay_sinh' => $user->ngay_sinh,
                'gioi_tinh' => $user->gioi_tinh,
                'ho_ten_err' => ''
            ];

            $this->view('users/edit_profile', $data);
        }
    }
    
    // Phương thức đổi mật khẩu
    public function changePassword() {
        // Kiểm tra đăng nhập
        if(!isset($_SESSION['user_id'])) {
            redirect('users/login');
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Xử lý form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $_SESSION['user_id'],
                'current_password' => trim($_POST['current_password']),
                'new_password' => trim($_POST['new_password']),
                'confirm_new_password' => trim($_POST['confirm_new_password']),
                'current_password_err' => '',
                'new_password_err' => '',
                'confirm_new_password_err' => ''
            ];

            // Validate Current Password
            if(empty($data['current_password'])) {
                $data['current_password_err'] = 'Vui lòng nhập mật khẩu hiện tại';
            } else {
                // Kiểm tra mật khẩu hiện tại
                $user = $this->userModel->getUserById($_SESSION['user_id']);
                if(!password_verify($data['current_password'], $user->mat_khau)) {
                    $data['current_password_err'] = 'Mật khẩu hiện tại không đúng';
                }
            }

            // Validate New Password
            if(empty($data['new_password'])) {
                $data['new_password_err'] = 'Vui lòng nhập mật khẩu mới';
            } elseif(strlen($data['new_password']) < 6) {
                $data['new_password_err'] = 'Mật khẩu phải có ít nhất 6 ký tự';
            }

            // Validate Confirm New Password
            if(empty($data['confirm_new_password'])) {
                $data['confirm_new_password_err'] = 'Vui lòng xác nhận mật khẩu mới';
            } else {
                if($data['new_password'] != $data['confirm_new_password']) {
                    $data['confirm_new_password_err'] = 'Mật khẩu không khớp';
                }
            }

            // Make sure errors are empty
            if(empty($data['current_password_err']) && empty($data['new_password_err']) && empty($data['confirm_new_password_err'])) {
                // Hash Password
                $data['new_password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);

                // Cập nhật mật khẩu
                if($this->userModel->changePassword($data['id'], $data['new_password'])) {
                    redirect('users/profile');
                } else {
                    die('Đã xảy ra lỗi');
                }
            } else {
                // Load view with errors
                $this->view('users/change_password', $data);
            }
        } else {
            $data = [
                'current_password' => '',
                'new_password' => '',
                'confirm_new_password' => '',
                'current_password_err' => '',
                'new_password_err' => '',
                'confirm_new_password_err' => ''
            ];

            $this->view('users/change_password', $data);
        }
    }
} 