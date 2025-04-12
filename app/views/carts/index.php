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
                        <tr data-cart-detail-id="<?php echo isset($item->id) ? $item->id : 'temp_'.$item->temp_id; ?>">
                            <td class="image-col">
                                <a href="<?php echo URL_ROOT; ?>/books/detail/<?php echo isset($item->id_sach) ? $item->id_sach : $item->book_id; ?>">
                                    <img src="<?php echo !empty($item->anh) ? URL_ROOT . '/public/uploads/images/' . $item->anh : URL_ROOT . '/public/images/default-book.jpg'; ?>" alt="<?php echo $item->ten_sach; ?>">
                                </a>
                            </td>
                            <td class="item-col">
                                <a href="<?php echo URL_ROOT; ?>/books/detail/<?php echo isset($item->id_sach) ? $item->id_sach : $item->book_id; ?>"><?php echo $item->ten_sach; ?></a>
                                <?php if(!$data['is_logged_in']): ?>
                                <div class="author"><?php echo isset($item->tac_gia) ? $item->tac_gia : ''; ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="price-col">
                                <span class="price"><?php echo formatCurrency($item->gia_tien); ?></span>
                            </td>
                            <td class="quantity-col">
                                <div class="quantity-controls">
                                    <button class="decrease"><i class="fas fa-minus"></i></button>
                                    <input type="text" value="<?php echo $item->so_luong; ?>" class="quantity-input" 
                                        data-cart-detail-id="<?php echo isset($item->id) ? $item->id : 'temp_'.$item->temp_id; ?>"
                                        data-book-id="<?php echo isset($item->id_sach) ? $item->id_sach : $item->book_id; ?>">
                                    <button class="increase"><i class="fas fa-plus"></i></button>
                                </div>
                            </td>
                            <td class="total-col">
                                <span class="total"><?php echo formatCurrency($item->thanh_tien); ?></span>
                            </td>
                            <td class="remove-col">
                                <?php if($data['is_logged_in']): ?>
                                <a href="<?php echo URL_ROOT; ?>/carts/remove/<?php echo $item->id; ?>" class="remove-item"><i class="fas fa-trash-alt"></i></a>
                                <?php else: ?>
                                <a href="javascript:void(0)" class="remove-temp-item" data-index="<?php echo $item->temp_id; ?>"><i class="fas fa-trash-alt"></i></a>
                                <?php endif; ?>
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
                        <span class="total-label">Tạm tính:</span>
                        <span class="total-amount"><?php echo formatCurrency(isset($data['cart']->tong_tien) ? $data['cart']->tong_tien : $data['subTotal']); ?></span>
                    </div>
                    <div class="total-line">
                        <span class="total-label">VAT (10%):</span>
                        <span class="total-amount"><?php echo formatCurrency($data['vat']); ?></span>
                    </div>
                    <div class="total-line">
                        <span class="total-label">Phí vận chuyển:</span>
                        <span class="total-amount">
                            <?php if($data['shipping'] > 0) : ?>
                                <?php echo formatCurrency($data['shipping']); ?>
                            <?php else : ?>
                                <span class="free-shipping">Miễn phí</span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php if($data['shipping'] > 0) : ?>
                    <div class="shipping-note">
                        <i class="fas fa-info-circle"></i>
                        Mua thêm <?php echo formatCurrency($data['free_shipping_threshold'] - (isset($data['cart']->tong_tien) ? $data['cart']->tong_tien : $data['subTotal'])); ?> để được miễn phí vận chuyển
                    </div>
                    <?php endif; ?>
                    <div class="total-line grand-total">
                        <span class="total-label">Tổng cộng:</span>
                        <span class="total-amount"><?php echo formatCurrency($data['orderTotal']); ?></span>
                    </div>
                    <p class="vat-note">Đã bao gồm VAT (nếu có)</p>
                    
                    <?php if($data['is_logged_in']): ?>
                    <button class="checkout-btn" id="checkout-btn">Thanh toán</button>
                    <?php else: ?>
                    <div class="login-notice">
                        <div class="login-btn-container">
                            <a href="<?php echo URL_ROOT; ?>/users/login" class="checkout-btn">Đăng nhập để thanh toán</a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý tăng giảm số lượng
        const decreaseBtns = document.querySelectorAll('.decrease');
        const increaseBtns = document.querySelectorAll('.increase');
        const quantityInputs = document.querySelectorAll('.quantity-input');
        const isLoggedIn = <?php echo $data['is_logged_in'] ? 'true' : 'false'; ?>;
        
        // Xử lý khi nhấn nút giảm
        decreaseBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.quantity-input');
                let value = parseInt(input.value);
                
                if(value > 1) {
                    value--;
                    input.value = value;
                    updateQuantity(input);
                }
            });
        });
        
        // Xử lý khi nhấn nút tăng
        increaseBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.quantity-input');
                let value = parseInt(input.value);
                
                value++;
                input.value = value;
                updateQuantity(input);
            });
        });
        
        // Xử lý khi thay đổi số lượng
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                let value = parseInt(this.value);
                
                if(isNaN(value) || value < 1) {
                    value = 1;
                    this.value = value;
                }
                
                updateQuantity(this);
            });
        });
        
        // Hàm cập nhật số lượng
        function updateQuantity(input) {
            const cartDetailId = input.getAttribute('data-cart-detail-id');
            const quantity = parseInt(input.value);
            
            if (isLoggedIn) {
                // Đã đăng nhập - cập nhật qua API
                fetch(`<?php echo URL_ROOT; ?>/carts/update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `cart_detail_id=${cartDetailId}&quantity=${quantity}`
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Update all cart item totals with the new data
                        data.cartItems.forEach(item => {
                            const itemRow = document.querySelector(`tr[data-cart-detail-id="${item.id}"]`);
                            if (itemRow) {
                                const totalCell = itemRow.querySelector('.total');
                                if (totalCell) {
                                    totalCell.textContent = jsFormatCurrency(item.thanh_tien);
                                }
                            }
                        });
                        
                        // Cập nhật tổng tiền
                        updateCartSummary(data);
                    } else {
                        alert(data.message || 'Có lỗi xảy ra khi cập nhật giỏ hàng.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra.');
                });
            } else {
                // Chưa đăng nhập - cập nhật giỏ hàng tạm thời
                const bookId = input.getAttribute('data-book-id');
                const tempId = cartDetailId.replace('temp_', '');
                
                // Cập nhật giỏ hàng tạm thời
                fetch(`<?php echo URL_ROOT; ?>/carts/updateTemp`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `temp_id=${tempId}&book_id=${bookId}&quantity=${quantity}`
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Cập nhật giá trị hiển thị
                        const itemRow = input.closest('tr');
                        const totalCell = itemRow.querySelector('.total');
                        if (totalCell) {
                            totalCell.textContent = jsFormatCurrency(data.item_total);
                        }
                        
                        // Cập nhật tổng tiền
                        updateCartSummary(data);
                    } else {
                        alert(data.message || 'Có lỗi xảy ra khi cập nhật giỏ hàng.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra.');
                });
            }
        }
        
        // Xử lý xóa sản phẩm tạm thời
        const removeTempButtons = document.querySelectorAll('.remove-temp-item');
        removeTempButtons.forEach(button => {
            button.addEventListener('click', function() {
                const itemIndex = this.getAttribute('data-index');
                
                if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
                    fetch(`<?php echo URL_ROOT; ?>/carts/removeTemp`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `temp_id=${itemIndex}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            // Xóa dòng sản phẩm
                            const itemRow = this.closest('tr');
                            itemRow.remove();
                            
                            // Cập nhật tổng tiền
                            updateCartSummary(data);
                            
                            // Nếu giỏ hàng trống, tải lại trang
                            if(data.is_empty) {
                                window.location.reload();
                            }
                        } else {
                            alert(data.message || 'Có lỗi xảy ra khi xóa sản phẩm.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra.');
                    });
                }
            });
        });
        
        // Hàm cập nhật tổng tiền
        function updateCartSummary(data) {
            // Cập nhật tạm tính
            const subtotalAmount = document.querySelector('.total-line:nth-child(1) .total-amount');
            if (subtotalAmount && data.subTotal !== undefined) {
                subtotalAmount.textContent = jsFormatCurrency(data.subTotal);
            }
            
            // Cập nhật VAT
            const vatAmount = document.querySelector('.total-line:nth-child(2) .total-amount');
            if (vatAmount && data.vat !== undefined) {
                vatAmount.textContent = jsFormatCurrency(data.vat);
            }
            
            // Cập nhật phí vận chuyển
            const shippingAmount = document.querySelector('.total-line:nth-child(3) .total-amount');
            if (shippingAmount && data.shipping !== undefined) {
                if (data.shipping > 0) {
                    shippingAmount.innerHTML = jsFormatCurrency(data.shipping);
                } else {
                    shippingAmount.innerHTML = '<span class="free-shipping">Miễn phí</span>';
                }
                
                // Cập nhật thông báo miễn phí vận chuyển
                const shippingNote = document.querySelector('.shipping-note');
                if (shippingNote && data.shipping > 0 && data.free_shipping_threshold !== undefined) {
                    const amountToFree = data.free_shipping_threshold - data.subTotal;
                    shippingNote.innerHTML = `<i class="fas fa-info-circle"></i> Mua thêm ${jsFormatCurrency(amountToFree)} để được miễn phí vận chuyển`;
                    shippingNote.style.display = 'block';
                } else if (shippingNote) {
                    shippingNote.style.display = 'none';
                }
            }
            
            // Cập nhật tổng cộng
            const totalAmount = document.querySelector('.grand-total .total-amount');
            if (totalAmount && data.orderTotal !== undefined) {
                totalAmount.textContent = jsFormatCurrency(data.orderTotal);
            }
            
            // Cập nhật số lượng trong giỏ hàng
            const cartCount = document.querySelector('.cart-count');
            if (cartCount && data.cart_count !== undefined) {
                cartCount.textContent = data.cart_count;
            }
        }
        
        // Hàm format tiền tệ cho JavaScript
        function jsFormatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        }
    });
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?> 