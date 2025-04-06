<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="breadcrumb">
    <div class="container">
        <ul>
            <li><a href="<?php echo URL_ROOT; ?>/">Trang chủ</a></li>
            <li class="active">Đăng ký tài khoản</li>
        </ul>
    </div>
</div>

<main class="main-content">
    <div class="container">
        <div class="auth-form">
            <h1>Đăng ký tài khoản</h1>
            <form action="<?php echo URL_ROOT; ?>/users/register" method="POST">
                <div class="form-group">
                    <label for="ho_ten">Họ tên: <span class="required">*</span></label>
                    <input type="text" name="ho_ten" id="ho_ten" class="form-control <?php echo (!empty($data['ho_ten_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['ho_ten']; ?>">
                    <span class="invalid-feedback"><?php echo $data['ho_ten_err']; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="email">Email: <span class="required">*</span></label>
                    <input type="email" name="email" id="email" class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>">
                    <span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="so_dien_thoai">Số điện thoại:</label>
                    <input type="text" name="so_dien_thoai" id="so_dien_thoai" class="form-control <?php echo (!empty($data['so_dien_thoai_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['so_dien_thoai']; ?>">
                    <span class="invalid-feedback"><?php echo $data['so_dien_thoai_err']; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="mat_khau">Mật khẩu: <span class="required">*</span></label>
                    <input type="password" name="mat_khau" id="mat_khau" class="form-control <?php echo (!empty($data['mat_khau_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['mat_khau']; ?>">
                    <span class="invalid-feedback"><?php echo $data['mat_khau_err']; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="xac_nhan_mat_khau">Xác nhận mật khẩu: <span class="required">*</span></label>
                    <input type="password" name="xac_nhan_mat_khau" id="xac_nhan_mat_khau" class="form-control <?php echo (!empty($data['xac_nhan_mat_khau_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['xac_nhan_mat_khau']; ?>">
                    <span class="invalid-feedback"><?php echo $data['xac_nhan_mat_khau_err']; ?></span>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Đăng ký</button>
                </div>
                
                <div class="form-footer">
                    <p>Đã có tài khoản? <a href="<?php echo URL_ROOT; ?>/users/login">Đăng nhập</a></p>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?> 