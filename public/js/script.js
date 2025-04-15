document.addEventListener('DOMContentLoaded', function() {
    // Chức năng nút "Trở về đầu trang"
    const backToTopButton = document.querySelector('.back-to-top');
    if (backToTopButton) {
        // Xử lý sự kiện click vào nút
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0, // Cuộn lên đầu trang
                behavior: 'smooth' // Hiệu ứng cuộn mượt
            });
        });

        // Hiển thị/ẩn nút dựa trên vị trí cuộn
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) { // Nếu cuộn xuống hơn 300px
                backToTopButton.style.display = 'flex'; // Hiện nút
            } else {
                backToTopButton.style.display = 'none'; // Ẩn nút
            }
        });

        // Ban đầu ẩn nút
        backToTopButton.style.display = 'none';
    }

    
    // Chức năng thêm vào giỏ hàng
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const bookId = this.dataset.bookId;
            const quantity = 1; // Số lượng mặc định
            
            console.log('Thêm vào giỏ hàng - book ID:', bookId);
            
            if (!bookId) {
                console.error('Không tìm thấy ID sách!');
                Swal.fire({
                    title: 'Có lỗi xảy ra!',
                    text: 'Không tìm thấy ID sách',
                    icon: 'error',
                    confirmButtonText: 'Đóng',
                    confirmButtonColor: '#2a5a4c'
                });
                return;
            }
            
            // Hiển thị thông báo đang xử lý
            Swal.fire({
                title: 'Đang xử lý...',
                text: 'Vui lòng đợi trong giây lát',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Kiểm tra và lấy đường dẫn cơ sở của website
            const url = typeof baseUrl !== 'undefined' ? baseUrl + '/carts/add' : '/ktra2php/carts/add';
            
            // Lấy thông tin tên sách từ phần tử cha gần nhất
            const productItem = this.closest('.product-item');
            const bookTitle = productItem ? productItem.querySelector('.product-info h3 a').textContent : 'sản phẩm';
            
            // Gửi yêu cầu AJAX
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `book_id=${bookId}&quantity=${quantity}`
            })
            .then(response => {
                console.log('Trạng thái phản hồi:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Dữ liệu phản hồi:', data);
                if (data.success) {
                    // Cập nhật số lượng sản phẩm trong giỏ hàng trên header nếu có
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement && data.cart_count) {
                        cartCountElement.textContent = data.cart_count;
                    }
                    
                    // Hiển thị thông báo thêm vào giỏ hàng thành công
                    Swal.fire({
                        title: 'Thêm vào giỏ hàng thành công!',
                        text: `Đã thêm "${bookTitle}" vào giỏ hàng`,
                        icon: 'success',
                        confirmButtonText: 'Xem giỏ hàng',
                        showCancelButton: true,
                        cancelButtonText: 'Tiếp tục mua sắm',
                        confirmButtonColor: '#2a5a4c',
                        cancelButtonColor: '#6c757d'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `${baseUrl}/carts`;
                        }
                    });
                } else {
                    // Hiển thị thông báo khi có lỗi xảy ra
                    Swal.fire({
                        title: 'Có lỗi xảy ra!',
                        text: data.message || 'Không thể thêm sản phẩm vào giỏ hàng',
                        icon: 'error',
                        confirmButtonText: 'Đóng',
                        confirmButtonColor: '#2a5a4c'
                    });
                }
            })
            .catch(error => {
                console.error('Lỗi:', error);
                // Hiển thị thông báo khi không thể kết nối server
                Swal.fire({
                    title: 'Có lỗi xảy ra!',
                    text: 'Không thể kết nối đến máy chủ',
                    icon: 'error',
                    confirmButtonText: 'Đóng',
                    confirmButtonColor: '#2a5a4c'
                });
            });
        });
    });

    // // Chức năng Mua ngay
    // const buyNowButtons = document.querySelectorAll('.buy-now');
    // buyNowButtons.forEach(function(button) {
    //     button.addEventListener('click', function(e) {
    //         e.preventDefault(); // Ngăn chặn hành vi mặc định của nút
            
    //         const bookId = this.dataset.bookId; // Lấy ID sách từ thuộc tính data
    //         const quantity = 1; // Số lượng mặc định là 1
            
    //         console.log('Mua ngay:', bookId, quantity); // In thông tin ra console để debug
            
    //         if (!bookId) { // Kiểm tra nếu không có ID sách
    //             console.error('Không tìm thấy ID sách!'); // Ghi log lỗi
    //             Swal.fire({ // Hiển thị thông báo lỗi bằng SweetAlert2
    //                 title: 'Có lỗi xảy ra!',
    //                 text: 'Không tìm thấy ID sách',
    //                 icon: 'error',
    //                 confirmButtonText: 'Đóng',
    //                 confirmButtonColor: '#2a5a4c'
    //             });
    //             return; // Dừng thực thi hàm
    //         }
            
    //         // Hiển thị loading
    //         Swal.fire({
    //             title: 'Đang xử lý...',
    //             text: 'Vui lòng đợi trong giây lát',
    //             allowOutsideClick: false,
    //             didOpen: () => {
    //                 Swal.showLoading();
    //             }
    //         });
            
    //         // Kiểm tra xem baseUrl có tồn tại không
    //         const url = typeof baseUrl !== 'undefined' ? baseUrl + '/carts/add' : '/ktra2php/carts/add';
            
    //         // Send AJAX request để thêm vào giỏ hàng rồi chuyển đến trang thanh toán
    //         fetch(url, {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/x-www-form-urlencoded',
    //             },
    //             body: `book_id=${bookId}&quantity=${quantity}&buy_now=1`
    //         })
    //         .then(response => response.json())
    //         .then(data => {
    //             if (data.success) {
    //                 // Chuyển đến trang thanh toán
    //                 window.location.href = `${baseUrl}/carts/checkout`;
    //             } else {
    //                 // Hiển thị thông báo lỗi
    //                 Swal.fire({
    //                     title: 'Có lỗi xảy ra!',
    //                     text: data.message || 'Không thể tiến hành mua ngay',
    //                     icon: 'error',
    //                     confirmButtonText: 'Đóng',
    //                     confirmButtonColor: '#2a5a4c'
    //                 });
    //             }
    //         })
    //         .catch(error => {
    //             console.error('Error:', error);
    //             // Hiển thị thông báo lỗi
    //             Swal.fire({
    //                 title: 'Có lỗi xảy ra!',
    //                 text: 'Không thể kết nối đến máy chủ',
    //                 icon: 'error',
    //                 confirmButtonText: 'Đóng',
    //                 confirmButtonColor: '#2a5a4c'
    //             });
    //         });
    //     });
    // });
});
