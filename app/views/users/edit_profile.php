<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="breadcrumb">
    <div class="container">
        <ul>
            <li><a href="<?php echo URL_ROOT; ?>/">Trang chủ</a></li>
            <li><a href="<?php echo URL_ROOT; ?>/users/profile">Thông tin tài khoản</a></li>
            <li class="active">Chỉnh sửa thông tin</li>
        </ul>
    </div>
</div>

<main class="main-content">
    <div class="container">
        <div class="auth-form">
            <h1>Chỉnh sửa thông tin</h1>
            <form action="<?php echo URL_ROOT; ?>/users/updateProfile" method="POST">
                <div class="form-group">
                    <label for="ho_ten">Họ tên: <span class="required">*</span></label>
                    <input type="text" name="ho_ten" id="ho_ten" class="form-control <?php echo (!empty($data['ho_ten_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['ho_ten']; ?>">
                    <span class="invalid-feedback"><?php echo $data['ho_ten_err']; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="so_dien_thoai">Số điện thoại:</label>
                    <input type="text" name="so_dien_thoai" id="so_dien_thoai" class="form-control" value="<?php echo $data['so_dien_thoai']; ?>">
                </div>
                
                <div class="form-group">
                    <label for="dia_chi">Địa chỉ:</label>
                    <textarea name="dia_chi" id="dia_chi" class="form-control" rows="3"><?php echo $data['dia_chi']; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="ngay_sinh">Ngày sinh:</label>
                    <input type="date" name="ngay_sinh" id="ngay_sinh" class="form-control" value="<?php echo $data['ngay_sinh']; ?>">
                </div>
                
                <div class="form-group">
                    <label>Giới tính:</label>
                    <div class="radio-group">
                        <label class="radio-container">
                            <input type="radio" name="gioi_tinh" value="1" <?php echo ($data['gioi_tinh'] == 1) ? 'checked' : ''; ?>>
                            <span class="radio-mark"></span>
                            Nam
                        </label>
                        <label class="radio-container">
                            <input type="radio" name="gioi_tinh" value="0" <?php echo ($data['gioi_tinh'] == 0) ? 'checked' : ''; ?>>
                            <span class="radio-mark"></span>
                            Nữ
                        </label>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="<?php echo URL_ROOT; ?>/users/profile" class="btn btn-outline">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?> 