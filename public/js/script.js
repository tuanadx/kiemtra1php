document.addEventListener('DOMContentLoaded', function() {
    // Back to top button functionality
    const backToTopButton = document.querySelector('.back-to-top');
    if (backToTopButton) {
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Show/hide back to top button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopButton.style.display = 'flex';
            } else {
                backToTopButton.style.display = 'none';
            }
        });

        // Initially hide the button
        backToTopButton.style.display = 'none';
    }

    // Mobile menu toggle for smaller screens
    const createMobileMenu = function() {
        const header = document.querySelector('header');
        const navList = document.querySelector('.nav-list');

        if (header && navList && window.innerWidth <= 768) {
            // Create mobile menu button if it doesn't exist already
            if (!document.querySelector('.mobile-menu-toggle')) {
                const mobileMenuToggle = document.createElement('button');
                mobileMenuToggle.className = 'mobile-menu-toggle';
                mobileMenuToggle.innerHTML = '<i class="fas fa-bars"></i>';
                header.querySelector('.top-header .container').appendChild(mobileMenuToggle);

                // Add click event to toggle mobile menu
                mobileMenuToggle.addEventListener('click', function() {
                    navList.classList.toggle('active');
                    this.classList.toggle('active');

                    if (this.classList.contains('active')) {
                        this.innerHTML = '<i class="fas fa-times"></i>';
                    } else {
                        this.innerHTML = '<i class="fas fa-bars"></i>';
                    }
                });
            }
        }
    };

    // Initialize mobile menu
    createMobileMenu();

    // Handle window resize for mobile menu
    window.addEventListener('resize', createMobileMenu);

    // Dropdown hover enhancement for accessibility
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(function(dropdown) {
        dropdown.addEventListener('mouseenter', function() {
            this.querySelector('.dropdown-content').style.display = 'block';
        });

        dropdown.addEventListener('mouseleave', function() {
            this.querySelector('.dropdown-content').style.display = 'none';
        });

        // Handle touch events for mobile
        dropdown.addEventListener('touchstart', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                const dropdownContent = this.querySelector('.dropdown-content');

                // Close all other dropdowns
                dropdowns.forEach(function(other) {
                    if (other !== dropdown) {
                        other.querySelector('.dropdown-content').style.display = 'none';
                    }
                });

                // Toggle this dropdown
                if (dropdownContent.style.display === 'block') {
                    dropdownContent.style.display = 'none';
                } else {
                    dropdownContent.style.display = 'block';
                }
            }
        });
    });

    // Handle filters in sidebar
    const filterCheckboxes = document.querySelectorAll('.filter-options input[type="checkbox"]');
    filterCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            // In a real implementation, this would trigger a filter action
            // For demo purposes, we'll just log the selected filters
            const selectedFilters = Array.from(filterCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.parentElement.textContent.trim());

            console.log('Selected filters:', selectedFilters);
        });
    });

    // Sorting options
    const sortOptions = document.querySelectorAll('.sort-options a');
    sortOptions.forEach(function(option) {
        option.addEventListener('click', function(e) {
            e.preventDefault();

            // Remove active class from all options
            sortOptions.forEach(opt => opt.classList.remove('active'));

            // Add active class to clicked option
            this.classList.add('active');

            // In a real implementation, this would trigger a sort action
            // For demo purposes, we'll just log the selected sort option
            console.log('Sort by:', this.textContent.trim());
        });
    });

    // Add to Cart functionality
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const bookId = this.dataset.bookId;
            const quantity = 1; // Default quantity
            
            console.log('Thêm vào giỏ hàng - book ID:', bookId);
            
            if (!bookId) {
                console.error('Không tìm thấy ID sách!');
                alert('Có lỗi xảy ra: Không tìm thấy ID sách');
                return;
            }
            
            // Kiểm tra xem baseUrl có tồn tại không
            const url = typeof baseUrl !== 'undefined' ? baseUrl + '/carts/add' : '/ktra2php/carts/add';
            
            // Create form data
            const formData = new FormData();
            formData.append('book_id', bookId);
            formData.append('quantity', quantity);
            
            // Send AJAX request
            fetch(url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Show success message
                    alert(data.message);
                    
                    // Update cart count in header if it exists
                    const cartCountElement = document.querySelector('.cart-count');
                    if (cartCountElement && data.count) {
                        cartCountElement.textContent = data.count;
                    }
                } else {
                    // Show error message
                    alert(data.message || 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng');
            });
        });
    });

    // Buy Now functionality
    const buyNowButtons = document.querySelectorAll('.buy-now');
    buyNowButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const bookId = this.dataset.bookId;
            const quantity = 1; // Default quantity
            
            console.log('Mua ngay:', bookId, quantity);
            
            // Kiểm tra xem baseUrl có tồn tại không
            const url = typeof baseUrl !== 'undefined' ? baseUrl + '/carts/buyNow' : '/ktra2php/carts/buyNow';
            
            // Create form data
            const formData = new FormData();
            formData.append('book_id', bookId);
            formData.append('quantity', quantity);
            
            // Send AJAX request
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Show success message
                    alert(data.message);
                    
                    // Redirect to specified URL if provided
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } else {
                    // Show error message
                    alert(data.message || 'Có lỗi xảy ra khi thực hiện chức năng mua ngay');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi thực hiện chức năng mua ngay');
            });
        });
    });
});
