
// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabHeaders = document.querySelectorAll('.tab-header');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all headers and panes
            tabHeaders.forEach(h => h.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));
            
            // Add active class to current header and pane
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Quantity selector functionality
    const minusBtn = document.querySelector('.quantity-btn.minus');
    const plusBtn = document.querySelector('.quantity-btn.plus');
    const quantityInput = document.querySelector('#quantity');
    const maxQuantity = parseInt(quantityInput.getAttribute('max'));
    
    minusBtn.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        if(value > 1) {
            quantityInput.value = value - 1;
        }
    });
    
    plusBtn.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        if(value < maxQuantity) {
            quantityInput.value = value + 1;
        }
    });
    
    // Add to cart functionality
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    if(addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const bookId = this.getAttribute('data-book-id');
            const quantity = document.querySelector('#quantity').value;
            const bookTitle = document.querySelector('.book-title').textContent;
            
            // Hiển thị loading
            Swal.fire({
                title: 'Đang xử lý...',
                text: 'Vui lòng đợi trong giây lát',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`${baseUrl}/carts/add`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `book_id=${bookId}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Cập nhật số lượng giỏ hàng
                    document.querySelector('.cart-count').textContent = data.cart_count;
                    
                    // Hiển thị thông báo thành công
                    Swal.fire({
                        title: 'Thêm vào giỏ hàng thành công!',
                        text: `Đã thêm ${quantity} cuốn "${bookTitle}" vào giỏ hàng`,
                        icon: 'success',
                        confirmButtonText: 'Xem giỏ hàng',
                        showCancelButton: true,
                        cancelButtonText: 'Tiếp tục mua sắm',
                        customClass: {
                            confirmButton: 'swal-confirm-button',
                            cancelButton: 'swal-cancel-button'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `${baseUrl}/carts`;
                        }
                    });
                } else {
                    // Hiển thị thông báo lỗi
                    Swal.fire({
                        title: 'Có lỗi xảy ra!',
                        text: data.message || 'Không thể thêm sản phẩm vào giỏ hàng',
                        icon: 'error',
                        confirmButtonText: 'Đóng'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Hiển thị thông báo lỗi
                Swal.fire({
                    title: 'Có lỗi xảy ra!',
                    text: 'Không thể kết nối đến máy chủ',
                    icon: 'error',
                    confirmButtonText: 'Đóng'
                });
            });
        });
    }
    
    // Buy now functionality
    const buyNowBtn = document.querySelector('.buy-now-btn');
    if(buyNowBtn) {
        buyNowBtn.addEventListener('click', function() {
            const bookId = this.getAttribute('data-book-id');
            const quantity = document.querySelector('#quantity').value;
            
            // Hiển thị loading
            Swal.fire({
                title: 'Đang xử lý...',
                text: 'Vui lòng đợi trong giây lát',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // First add to cart then redirect to checkout
            fetch(`${baseUrl}/carts/add`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `book_id=${bookId}&quantity=${quantity}&buy_now=1`
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.href = `${baseUrl}/carts/checkout`;
                } else {
                    // Hiển thị thông báo lỗi
                    Swal.fire({
                        title: 'Có lỗi xảy ra!',
                        text: data.message || 'Không thể tiến hành mua ngay',
                        icon: 'error',
                        confirmButtonText: 'Đóng'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Hiển thị thông báo lỗi
                Swal.fire({
                    title: 'Có lỗi xảy ra!',
                    text: 'Không thể kết nối đến máy chủ',
                    icon: 'error',
                    confirmButtonText: 'Đóng'
                });
            });
        });
    }
    
    // Thêm style cho SweetAlert2
    const style = document.createElement('style');
    style.textContent = `
        .swal-confirm-button {
            background-color: #2a5a4c !important;
        }
        .swal-cancel-button {
            background-color: #6c757d !important;
        }
    `;
    document.head.appendChild(style);
});

