<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="breadcrumb">
    <div class="container">
        <ul>
            <li><a href="<?php echo URL_ROOT; ?>/">Trang chủ</a></li>
            <li><a href="<?php echo URL_ROOT; ?>/users/profile">Thông tin tài khoản</a></li>
            <li class="active">Đổi mật khẩu</li>
        </ul>
    </div>
</div>

<main class="main-content">
    <div class="container">
        <div class="auth-form">
            <h1>Đổi mật khẩu</h1>
            <form action="<?php echo URL_ROOT; ?>/users/changePassword" method="POST">
                <div class="form-group">
                    <label for="current_password">Mật khẩu hiện tại: <span class="required">*</span></label>
                    <input type="password" name="current_password" id="current_password" class="form-control <?php echo (!empty($data['current_password_err'])) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $data['current_password_err']; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="new_password">Mật khẩu mới: <span class="required">*</span></label>
                    <input type="password" name="new_password" id="new_password" class="form-control <?php echo (!empty($data['new_password_err'])) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $data['new_password_err']; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="confirm_new_password">Xác nhận mật khẩu mới: <span class="required">*</span></label>
                    <input type="password" name="confirm_new_password" id="confirm_new_password" class="form-control <?php echo (!empty($data['confirm_new_password_err'])) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $data['confirm_new_password_err']; ?></span>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                    <a href="<?php echo URL_ROOT; ?>/users/profile" class="btn btn-outline">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?> 