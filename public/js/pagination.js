
document.addEventListener('DOMContentLoaded', function() {
    // Lấy các liên kết phân trang
    const setupPagination = () => {
        const paginationLinks = document.querySelectorAll('.pagination a:not(.disabled)');
        
        paginationLinks.forEach(link => {
            if (link.classList.contains('ajax-loaded')) return;
            
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const url = this.getAttribute('href');
                if (!url || url === '#') return;
                
                // Lưu vị trí cuộn hiện tại
                const currentScrollPosition = window.scrollY;
                
                // Giữ nguyên nội dung hiện tại và thêm overlay loading
                const productGrid = document.querySelector('.product-grid');
                const loadingOverlay = document.createElement('div');
                loadingOverlay.className = 'loading-overlay';
                loadingOverlay.innerHTML = '<div class="loading-spinner"></div>';
                document.querySelector('.product-content').appendChild(loadingOverlay);
                
                // Cập nhật URL trước để không có cảm giác trễ
                history.pushState({}, '', url);
                
                // Ngăn chặn việc cuộn lên đầu trang
                setTimeout(() => {
                    window.scrollTo(0, currentScrollPosition);
                }, 0);
                
                // Fetch dữ liệu trang mới
                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        // Parse HTML
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Tạo đối tượng chứa nội dung mới
                        const newProductGrid = doc.querySelector('.product-grid');
                        const newPagination = doc.querySelector('.pagination');
                        const newTitle = doc.querySelector('.collection-header h1');
                        
                        // Chuẩn bị nội dung mới trước khi hiển thị
                        if (newProductGrid && productGrid) {
                            const tempContainer = document.createElement('div');
                            tempContainer.innerHTML = newProductGrid.innerHTML;
                            
                            // Tạo hiệu ứng mượt mà
                            setTimeout(() => {
                                // Cập nhật nội dung
                                productGrid.innerHTML = tempContainer.innerHTML;
                                
                                // Cập nhật phân trang
                                if (newPagination) {
                                    document.querySelector('.pagination').innerHTML = newPagination.innerHTML;
                                }
                                
                                // Cập nhật tiêu đề
                                if (newTitle) {
                                    document.querySelector('.collection-header h1').innerHTML = newTitle.innerHTML;
                                }
                                
                                // Xóa overlay loading
                                document.querySelector('.loading-overlay').remove();
                                
                                // Cuộn đến phần "Tất cả sản phẩm - Trang"
                                const productSection = document.getElementById('product-section');
                                if (productSection) {
                                    // Thêm một chút độ trễ để đảm bảo DOM đã được cập nhật đầy đủ
                                    setTimeout(() => {
                                        productSection.scrollIntoView({ 
                                            behavior: 'smooth', 
                                            block: 'start'
                                        });
                                    }, 100);
                                }
                                
                                // Thiết lập lại pagination cho trang mới
                                setupPagination();
                                
                                // Thêm hiệu ứng fade-in cho các sản phẩm mới
                                const productItems = document.querySelectorAll('.product-item');
                                productItems.forEach((item, index) => {
                                    item.style.opacity = '0';
                                    setTimeout(() => {
                                        item.style.opacity = '1';
                                        item.style.transform = 'translateY(0)';
                                    }, index * 50);
                                });
                            }, 300);
                        } else {
                            document.querySelector('.loading-overlay').remove();
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi khi tải trang:', error);
                        document.querySelector('.loading-overlay').remove();
                        const errorMessage = document.createElement('div');
                        errorMessage.className = 'error';
                        errorMessage.textContent = 'Có lỗi xảy ra. Vui lòng thử lại.';
                        productGrid.appendChild(errorMessage);
                    });
            });
            
            link.classList.add('ajax-loaded');
        });
    };
    
    // Thiết lập phân trang ban đầu
    setupPagination();
    
    // Thêm CSS cho loading và chuyển trang
    const style = document.createElement('style');
    style.textContent = `
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }
        
        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2a5a4c;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        .product-item {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.4s ease, transform 0.4s ease;
        }
        
        .product-content {
            position: relative;
            min-height: 400px;
        }
        
        .error {
            text-align: center;
            padding: 30px;
            font-size: 18px;
            color: #e74c3c;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
}); 