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
                        <span class="total-label">Tạm tính:</span>
                        <span class="total-amount"><?php echo formatCurrency($data['cart']->tong_tien); ?></span>
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
                        Mua thêm <?php echo formatCurrency($data['free_shipping_threshold'] - $data['cart']->tong_tien); ?> để được miễn phí vận chuyển
                    </div>
                    <?php endif; ?>
                    <div class="total-line grand-total">
                        <span class="total-label">Tổng cộng:</span>
                        <span class="total-amount"><?php echo formatCurrency($data['orderTotal']); ?></span>
                    </div>
                    <p class="vat-note">Đã bao gồm VAT (nếu có)</p>
                    <button class="checkout-btn" id="checkout-btn">Thanh toán</button>
                    <button class="save-cart-btn" id="save-cart-btn">Lưu giỏ hàng</button>
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
                    const subtotalAmount = document.querySelector('.total-line:first-child .total-amount');
                    if (subtotalAmount) {
                        subtotalAmount.textContent = jsFormatCurrency(data.cart.tong_tien);
                    }
                    
                    // Cập nhật VAT
                    const vatAmount = document.querySelector('.total-line:nth-child(2) .total-amount');
                    if (vatAmount) {
                        vatAmount.textContent = jsFormatCurrency(data.vat);
                    }
                    
                    // Cập nhật phí vận chuyển
                    const shippingAmount = document.querySelector('.total-line:nth-child(3) .total-amount');
                    if (shippingAmount) {
                        if(data.shipping > 0) {
                            shippingAmount.innerHTML = jsFormatCurrency(data.shipping);
                        } else {
                            shippingAmount.innerHTML = '<span class="free-shipping">Miễn phí</span>';
                        }
                    }
                    
                    // Cập nhật tổng cộng
                    const totalAmount = document.querySelector('.grand-total .total-amount');
                    if (totalAmount) {
                        totalAmount.textContent = jsFormatCurrency(data.orderTotal);
                    }
                    
                    // Cập nhật số lượng sản phẩm trong giỏ hàng
                    updateCartCount(data.count);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        // Lưu giỏ hàng tạm thời
        const saveCartBtn = document.getElementById('save-cart-btn');
        if(saveCartBtn) {
            saveCartBtn.addEventListener('click', function() {
                fetch(`<?php echo URL_ROOT; ?>/carts/saveTemporary`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        }
        
        // Hàm cập nhật số lượng sản phẩm trong giỏ hàng hiển thị ở header
        function updateCartCount(count) {
            const cartCount = document.querySelector('.cart-count');
            if(cartCount) {
                cartCount.textContent = count;
            }
        }
        
        // Hàm format tiền tệ cho JavaScript
        function jsFormatCurrency(value) {
            return new Intl.NumberFormat('vi-VN', { 
                style: 'currency', 
                currency: 'VND',
                maximumFractionDigits: 0 
            }).format(value).replace('₫', 'đ');
        }
    });
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?> 