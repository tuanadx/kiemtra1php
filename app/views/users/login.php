<?php require_once APPROOT . '/views/includes/header.php'; ?>

<div class="breadcrumb">
    <div class="container">
        <ul>
            <li><a href="<?php echo URL_ROOT; ?>/">Trang chủ</a></li>
            <li class="active">Đăng nhập</li>
        </ul>
    </div>
</div>

<main class="main-content">
    <div class="container">
        <div class="auth-form">
            <h1>Đăng nhập</h1>
            <form action="<?php echo URL_ROOT; ?>/users/login" method="POST">
                <div class="form-group">
                    <label for="email">Email: <span class="required">*</span></label>
                    <input type="email" name="email" id="email" class="form-control <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['email']; ?>">
                    <span class="invalid-feedback"><?php echo $data['email_err']; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="mat_khau">Mật khẩu: <span class="required">*</span></label>
                    <input type="password" name="mat_khau" id="mat_khau" class="form-control <?php echo (!empty($data['mat_khau_err'])) ? 'is-invalid' : ''; ?>" value="<?php echo $data['mat_khau']; ?>">
                    <span class="invalid-feedback"><?php echo $data['mat_khau_err']; ?></span>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Đăng nhập</button>
                </div>
                
                <div class="form-footer">
                    <p>Chưa có tài khoản? <a href="<?php echo URL_ROOT; ?>/users/register">Đăng ký</a></p>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once APPROOT . '/views/includes/footer.php'; ?> 