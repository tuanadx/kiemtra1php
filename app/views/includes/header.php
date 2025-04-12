<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?php echo URL_ROOT; ?>">
    <title><?php echo isset($data['title']) ? $data['title'] . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/public/css/main.css">
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/public/css/cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Định nghĩa baseUrl cho JavaScript
        const baseUrl = '<?php echo URL_ROOT; ?>';
    </script>
</head>
<body>
        <!-- Header Section -->
        <header>
        <div class="top-header">
            <div class="container">
                <div class="logo">
                    <a href="<?php echo URL_ROOT; ?>/">
                        <img src="https://ext.same-assets.com/3715259319/3110586656.png" alt="Nhã Nam">
                    </a>
                </div>
                <div class="search-box">
                    <form action="<?php echo URL_ROOT; ?>/home/search" method="get">
                        <input type="text" placeholder="Tìm kiếm..." name="keyword" class="search-input">
                        <button type="submit" class="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="user-actions">
                    <?php if(isset($_SESSION['user_id'])) : ?>
                        <div class="user-dropdown">
                            <a href="javascript:void(0);" class="user-toggle">
                                <i class="fas fa-user"></i>
                                <span><?php echo $_SESSION['user_name']; ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </a>
                            <div class="user-dropdown-content">
                                <a href="<?php echo URL_ROOT; ?>/users/profile">Thông tin tài khoản</a>
                                <a href="<?php echo URL_ROOT; ?>/carts/orderHistory">Lịch sử đơn hàng</a>
                                <a href="<?php echo URL_ROOT; ?>/users/logout">Đăng xuất</a>
                            </div>
                        </div>
                    <?php else : ?>
                        <a href="<?php echo URL_ROOT; ?>/users/login" class="login">Đăng nhập</a>
                        <a href="<?php echo URL_ROOT; ?>/users/register" class="register">Đăng ký</a>
                    <?php endif; ?>
                    <a href="<?php echo URL_ROOT; ?>/carts" class="cart">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count"><?php echo isset($_SESSION['cart_count']) ? $_SESSION['cart_count'] : 0; ?></span>
                    </a>
                </div>
            </div>
        </div>
        <!-- <div class="main-nav">
            <div class="container">
                <nav>
                    <ul class="nav-links">
                        <li><a href="/" class="active">Trang chủ</a></li>
                        <li class="dropdown">
                            <a href="/tin-sach">Tin Sách</a>
                            <div class="dropdown-content">
                                <a href="/tin-nha-nam">Tin Nhà Nam</a>
                                <a href="/review-sach-cua-doc-gia">Review sách của độc giả</a>
                                <a href="/review-sach-tren-bao-chi">Review sách trên báo chí</a>
                                <a href="/bien-tap-vien-gioi-thieu">Biên tập viên giới thiệu</a>
                            </div>
                        </li>
                        <li class="dropdown">
                            <a href="/collections/all">Sách Nhà Nam</a>
                            <div class="dropdown-content mega-menu">
                                <div class="mega-menu-column">
                                    <h3>Hư cấu</h3>
                                    <a href="/van-hoc-hien-dai">Văn học hiện đại</a>
                                    <a href="/van-hoc-kinh-dien">Văn học kinh điển</a>
                                    <a href="/lich-su">Văn học thiếu nhi</a>
                                    <a href="/lang-man">Lãng mạn</a>
                                    <a href="/ky-ao">Kỳ ảo</a>
                                    <a href="/trinh-tham-kinh-di">Trinh thám - Kinh dị</a>
                                    <a href="/vien-tuong">Khoa học Viễn tưởng</a>
                                    <a href="/phieu-luu-ly-ky">Phiêu lưu ly kỳ</a>
                                    <a href="/tan-van">Tản văn</a>
                                    <a href="/truyen-tranh-graphic-novel">Truyện tranh (graphic novel)</a>
                                    <a href="/sach-tranh-picture-book">Sách tranh (Picture book)</a>
                                    <a href="/tho-kich">Thơ - kịch</a>
                                    <a href="/light-novel">Light novel</a>
                                    <a href="/sach-to-mau">Sách tô màu</a>
                                </div>
                                <div class="mega-menu-column">
                                    <h3>Phi hư cấu</h3>
                                    <a href="/triet-hoc">Triết học</a>
                                    <a href="/lich-su-1">Sử học</a>
                                    <a href="/khoa-hoc">Khoa học</a>
                                    <a href="/kinh-doanh">Kinh doanh</a>
                                    <a href="/kinh-te-chinh-tri">Kinh tế chính trị</a>
                                    <a href="/ky-nang">Kỹ năng</a>
                                    <a href="/nghe-thuat">Nghệ thuật</a>
                                    <a href="/nuoi-day-con">Nuôi dạy con</a>
                                    <a href="/tieu-luan-phe-binh">Tiểu luận - phê bình</a>
                                    <a href="/phat-trien-ban-than">Tâm lý ứng dụng</a>
                                    <a href="/tam-ly-hoc">Tâm lý học</a>
                                    <a href="/hoi-ky">Hồi ký</a>
                                    <a href="/y-hoc-suc-khoe">Y học - Sức khỏe</a>
                                    <a href="/tam-linh-ton-giao">Tâm linh - Tôn giáo</a>
                                    <a href="/kien-thuc-pho-thong">Kiến thức phổ thông</a>
                                    <a href="/phong-cach-song">Phong cách sống</a>
                                </div>
                                <div class="mega-menu-column">
                                    <h3>Thiếu nhi</h3>
                                    <a href="/0-5-tuoi">0-5 tuổi</a>
                                    <a href="/6-8-tuoi">6-8 tuổi</a>
                                    <a href="/9-12-tuoi">9-12 tuổi</a>
                                    <a href="/13-15-tuoi">13-15 tuổi</a>
                                </div>
                                <div class="mega-menu-column">
                                    <h3>Phân loại khác</h3>
                                    <a href="/sach-ban-chay">Sách bán chạy</a>
                                    <a href="/sach-moi-xuat-ban">Sách mới xuất bản</a>
                                    <a href="/sach-sap-xuat-ban">Sách sắp xuất bản</a>
                                    <a href="/sach-duoc-giai-thuong">Sách được giải thưởng</a>
                                    <a href="/sach-pop-up-lift-the-flaps">Sách pop-up, lift-the-flaps</a>
                                    <a href="/sach-chu-de-dong-duong">Nghiên cứu Việt Nam</a>
                                    <a href="/viet-nam-danh-tac">Việt Nam danh tác</a>
                                    <a href="/tac-gia-viet-nam">Tác giả Việt Nam</a>
                                    <a href="/ban-dac-biet">Bản đặc biệt</a>
                                    <a href="/phu-kien-qua-tang">Phụ kiện - Quà tặng</a>
                                </div>
                            </div>
                        </li>
                        <li><a href="/tac-gia">Tác giả</a></li>
                        <li class="dropdown">
                            <a href="/cuoc-thi">Cuộc Thi</a>
                            <div class="dropdown-content">
                                <a href="/ai-do-doc-cung-ta">AI ĐỌC CÙNG TA</a>
                            </div>
                        </li>
                        <li><a href="/gioi-thieu">Về Nhà Nam</a></li>
                        <li class="dropdown">
                            <a href="/lien-he">Liên hệ</a>
                            <div class="dropdown-content">
                                <a href="/he-thong-hieu-sach">Hệ Thống Hiệu Sách</a>
                                <a href="/he-thong-cua-hang">Hệ Thống Phát Hành</a>
                                <a href="/gui-thu-cho-nha-nam">Gửi Thư Cho Nhà Nam</a>
                                <a href="/tuyen-dung">Tuyển Dụng</a>
                            </div>
                        </li>
                    </ul>
                </nav>
            </div>
        </div> -->
    </header> 
</body>
</html>