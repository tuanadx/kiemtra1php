<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="breadcrumb">
    <div class="container">
        <ul>
            <li><a href="<?php echo URL_ROOT; ?>/">Trang chủ</a></li>
            <li class="active">Thông tin tài khoản</li>
        </ul>
    </div>
</div>

<main class="main-content">
    <div class="container">
        <div class="profile-container">
            <h1>Thông tin tài khoản</h1>
            
            <div class="profile-info">
                <div class="profile-section">
                    <h2>Thông tin cá nhân</h2>
                    <div class="info-group">
                        <label>Họ tên:</label>
                        <span><?php echo $data['user']->ho_ten; ?></span>
                    </div>
                    <div class="info-group">
                        <label>Email:</label>
                        <span><?php echo $data['user']->email; ?></span>
                    </div>
                    <div class="info-group">
                        <label>Số điện thoại:</label>
                        <span><?php echo !empty($data['user']->so_dien_thoai) ? $data['user']->so_dien_thoai : 'Chưa cập nhật'; ?></span>
                    </div>
                    <div class="info-group">
                        <label>Địa chỉ:</label>
                        <span><?php echo !empty($data['user']->dia_chi) ? $data['user']->dia_chi : 'Chưa cập nhật'; ?></span>
                    </div>
                    <div class="info-group">
                        <label>Ngày sinh:</label>
                        <span><?php echo !empty($data['user']->ngay_sinh) ? $data['user']->ngay_sinh : 'Chưa cập nhật'; ?></span>
                    </div>
                    <div class="info-group">
                        <label>Giới tính:</label>
                        <span>
                            <?php 
                            if(!empty($data['user']->gioi_tinh)) {
                                echo $data['user']->gioi_tinh == 1 ? 'Nam' : 'Nữ';
                            } else {
                                echo 'Chưa cập nhật';
                            }
                            ?>
                        </span>
                    </div>
                </div>
                
                <div class="profile-actions">
                    <a href="<?php echo URL_ROOT; ?>/users/editProfile" class="btn btn-primary">Chỉnh sửa thông tin</a>
                    <a href="<?php echo URL_ROOT; ?>/users/changePassword" class="btn btn-secondary">Đổi mật khẩu</a>
                    <a href="<?php echo URL_ROOT; ?>/carts/orderHistory" class="btn btn-outline">Lịch sử đơn hàng</a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?> 