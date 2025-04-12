<?php require_once APPROOT . '/views/includes/header.php'; ?>
<!-- Thêm CSS trang chi tiết sách -->
<link rel="stylesheet" href="<?php echo URL_ROOT; ?>/public/css/detail.css">
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">

<div class="book-detail-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <ul>
                <li><a href="<?php echo URL_ROOT; ?>/">Trang chủ</a></li>
                <li><a href="<?php echo URL_ROOT; ?>/books">Sách</a></li>
                <li class="active"><?php echo $data['book']->ten_sach; ?></li>
            </ul>
        </div>
    </div>

    <!-- Book Detail Section -->
    <section class="book-detail">
        <div class="container">
            <div class="book-wrapper">
                <div class="book-image-container">
                    <div class="main-image">
                        <img src="<?php echo !empty($data['book']->anh) ? URL_ROOT . '/public/uploads/images/' . $data['book']->anh : URL_ROOT . '/public/images/default-book.jpg'; ?>" 
                             alt="<?php echo $data['book']->ten_sach; ?>" class="img-fluid">
                    </div>
                </div>
                
                <div class="book-info">
                    <div class="book-header">
                        <h1 class="book-title"><?php echo $data['book']->ten_sach; ?></h1>
                        <div class="book-meta">
                            <p class="book-author">
                                <span class="label">Tác giả:</span> 
                                <span class="value"><?php echo $data['book']->tac_gia; ?></span>
                            </p>
                            <div class="stock-status">
                                <?php if($data['book']->so_luong > 0): ?>
                                    <span class="in-stock"><i class="fas fa-check-circle"></i> Còn hàng</span>
                                <?php else: ?>
                                    <span class="out-of-stock"><i class="fas fa-times-circle"></i> Hết hàng</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="book-pricing">
                        <div class="price-wrapper">
                            <span class="current-price"><?php echo number_format($data['book']->gia_tien, 0, ',', '.'); ?>₫</span>
                        </div>
                    </div>
                    
                    <div class="book-actions">
                        <div class="quantity-selector">
                            <span class="label">Số lượng:</span>
                            <div class="quantity-input">
                                <button type="button" class="quantity-btn minus">-</button>
                                <input type="number" id="quantity" name="quantity" min="1" max="<?php echo $data['book']->so_luong; ?>" value="1" />
                                <button type="button" class="quantity-btn plus">+</button>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <?php if($data['book']->so_luong > 0): ?>
                                <button class="add-to-cart-btn" data-book-id="<?php echo $data['book']->id; ?>">
                                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                                </button>
                                <button class="buy-now-btn" data-book-id="<?php echo $data['book']->id; ?>">
                                    <i class="fas fa-bolt"></i> Mua ngay
                                </button>
                            <?php else: ?>
                                <button class="notify-btn" data-book-id="<?php echo $data['book']->id; ?>">
                                    <i class="fas fa-bell"></i> Thông báo khi có hàng
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Book Info Tabs -->
                    <div class="book-info-tabs">
                        <div class="tab-headers">
                            <div class="tab-header active" data-tab="description">Chi tiết sản phẩm</div>
                            <div class="tab-header" data-tab="delivery">Thông tin giao hàng</div>
                        </div>
                        <div class="tab-content">
                            <div id="description" class="tab-pane active">
                                <div class="book-info-table">
                                    <table>
                                        <tr>
                                            <td class="label">Tác giả</td>
                                            <td><?php echo $data['book']->tac_gia; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="label">Số lượng</td>
                                            <td><?php echo $data['book']->so_luong; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div id="delivery" class="tab-pane">
                                <div class="delivery-info">
                                    <div class="delivery-item">
                                        <i class="fas fa-truck"></i>
                                        <div class="delivery-text">
                                            <h4>Giao hàng nhanh</h4>
                                            <p>Giao hàng nhanh chóng trong vòng 2-3 ngày</p>
                                        </div>
                                    </div>
                                    <div class="delivery-item">
                                        <i class="fas fa-shield-alt"></i>
                                        <div class="delivery-text">
                                            <h4>Đảm bảo chất lượng</h4>
                                            <p>Sách mới 100%, bảo đảm chất lượng</p>
                                        </div>
                                    </div>
                                    <div class="delivery-item">
                                        <i class="fas fa-undo"></i>
                                        <div class="delivery-text">
                                            <h4>Đổi trả dễ dàng</h4>
                                            <p>Đổi trả sản phẩm trong vòng 7 ngày</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Books Section -->
    <section class="related-books">
        <div class="container">
            <h2 class="section-title">Sách liên quan</h2>
            <div class="books-slider">
                <!-- Placeholder for related books -->
                <div class="book-slide-message">Đang tải sách liên quan...</div>
            </div>
        </div>
    </section>
</div>

<!-- SweetAlert2 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<!-- Thêm JavaScript cho trang chi tiết sách -->
<script src="<?php echo URL_ROOT; ?>/public/js/detail.js"></script>

<?php require_once APPROOT . '/views/includes/footer.php'; ?> 