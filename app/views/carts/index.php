<?php require APPROOT . '/views/includes/header.php'; ?>

<div class="breadcrumb">
    <div class="container">
        <ul>
            <li><a href="<?php echo URL_ROOT; ?>/">Trang chủ</a></li>
            <li class="active">Giỏ hàng</li>
        </ul>
    </div>
</div>

<div class="cart-container">
    <div class="container">
        <div class="cart-content">
            <h1 class="cart-title">Giỏ hàng của bạn</h1>

            <?php if(empty($data['cartItems'])) : ?>
                <div class="empty-cart">
                    <p>Giỏ hàng của bạn hiện đang trống.</p>
                    <a href="<?php echo URL_ROOT; ?>" class="continue-btn">Tiếp tục mua hàng</a>
                </div>
            <?php else : ?>

            <div class="cart-table">
                <table>
                    <thead>
                        <tr>
                            <th class="image-col">Ảnh</th>
                            <th class="item-col">Sản phẩm</th>
                            <th class="price-col">Đơn giá</th>
                            <th class="quantity-col">Số lượng</th>
                            <th class="total-col">Thành tiền</th>
                            <th class="remove-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['cartItems'] as $item) : ?>
                        <tr data-cart-detail-id="<?php echo $item->id; ?>">
                            <td class="image-col">
                                <a href="<?php echo URL_ROOT; ?>/books/detail/<?php echo $item->id_sach; ?>">
                                    <img src="<?php echo !empty($item->anh) ? URL_ROOT . '/public/uploads/images/' . $item->anh : URL_ROOT . '/public/images/default-book.jpg'; ?>" alt="<?php echo $item->ten_sach; ?>">
                                </a>
                            </td>
                            <td class="item-col">
                                <a href="<?php echo URL_ROOT; ?>/books/detail/<?php echo $item->id_sach; ?>"><?php echo $item->ten_sach; ?></a>
                            </td>
                            <td class="price-col">
                                <span class="price"><?php echo formatCurrency($item->gia_tien); ?></span>
                            </td>
                            <td class="quantity-col">
                                <div class="quantity-controls">
                                    <button class="decrease"><i class="fas fa-minus"></i></button>
                                    <input type="text" value="<?php echo $item->so_luong; ?>" class="quantity-input" data-cart-detail-id="<?php echo $item->id; ?>">
                                    <button class="increase"><i class="fas fa-plus"></i></button>
                                </div>
                            </td>
                            <td class="total-col">
                                <span class="total"><?php echo formatCurrency($item->thanh_tien); ?></span>
                            </td>
                            <td class="remove-col">
                                <a href="<?php echo URL_ROOT; ?>/carts/remove/<?php echo $item->id; ?>" class="remove-item"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="cart-actions">
                <div class="continue-shopping">
                    <a href="<?php echo URL_ROOT; ?>" class="continue-btn">Tiếp tục mua hàng</a>
                </div>
                <div class="update-cart">
                    <a href="<?php echo URL_ROOT; ?>/carts/clear" class="clear-btn" onclick="return confirm('Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng?')">Xóa giỏ hàng</a>
                </div>
            </div>

            <div class="cart-summary">
                <div class="customer-note">
                    <h3>Ghi chú đơn hàng</h3>
                    <textarea placeholder="Ghi chú"></textarea>
                </div>

                <div class="order-total">
                    <div class="total-line">
                        <span class="total-label">Tổng tiền:</span>
                        <span class="total-amount"><?php echo formatCurrency($data['cart']->tong_tien); ?></span>
                    </div>
                    <p class="vat-note">Đã bao gồm VAT (nếu có)</p>
                    <button class="checkout-btn">Thanh toán</button>
                </div>
            </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<?php require APPROOT . '/views/includes/footer.php'; ?> 