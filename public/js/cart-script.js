document.addEventListener('DOMContentLoaded', function() {
    // Find all quantity control elements
    const quantityControls = document.querySelectorAll('.quantity-controls');

    // Update cart total
    function updateCartTotal() {
        let total = 0;
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const priceText = row.querySelector('.price').textContent;
            const price = parseInt(priceText.replace(/[^0-9]/g, ''));
            const quantity = parseInt(row.querySelector('.quantity-input').value);
            const rowTotal = price * quantity;

            // Update row total
            row.querySelector('.total').textContent = rowTotal.toLocaleString('vi-VN') + '₫';

            // Add to cart total
            total += rowTotal;
        });

        // Update total amount in summary
        document.querySelector('.total-amount').textContent = total.toLocaleString('vi-VN') + '₫';
    }

    // Add event listeners to all quantity controls
    quantityControls.forEach(control => {
        const decreaseBtn = control.querySelector('.decrease');
        const increaseBtn = control.querySelector('.increase');
        const input = control.querySelector('.quantity-input');

        // Decrease quantity
        decreaseBtn.addEventListener('click', function() {
            let value = parseInt(input.value);
            if (value > 1) {
                input.value = value - 1;
                updateCartTotal();
            }
        });

        // Increase quantity
        increaseBtn.addEventListener('click', function() {
            let value = parseInt(input.value);
            input.value = value + 1;
            updateCartTotal();
        });

        // Input change
        input.addEventListener('change', function() {
            let value = parseInt(input.value);
            if (isNaN(value) || value < 1) {
                input.value = 1;
            }
            updateCartTotal();
        });
    });

    // Remove item functionality
    const removeButtons = document.querySelectorAll('.remove-item');

    removeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const row = this.closest('tr');

            // Add a fade-out animation
            row.style.transition = 'opacity 0.3s ease';
            row.style.opacity = '0';

            // Remove the row after animation completes
            setTimeout(() => {
                row.remove();
                updateCartTotal();

                // Check if cart is empty
                const remainingRows = document.querySelectorAll('tbody tr');
                if (remainingRows.length === 0) {
                    displayEmptyCart();
                }
            }, 300);
        });
    });

    // Update cart button
    const updateCartBtn = document.querySelector('.update-btn');

    updateCartBtn.addEventListener('click', function() {
        updateCartTotal();

        // Show a confirmation message
        const message = document.createElement('div');
        message.className = 'update-message';
        message.textContent = 'Giỏ hàng đã được cập nhật';
        message.style.position = 'fixed';
        message.style.top = '20px';
        message.style.left = '50%';
        message.style.transform = 'translateX(-50%)';
        message.style.backgroundColor = 'var(--primary-color)';
        message.style.color = '#fff';
        message.style.padding = '10px 20px';
        message.style.borderRadius = '4px';
        message.style.zIndex = '1000';

        document.body.appendChild(message);

        // Remove the message after 3 seconds
        setTimeout(() => {
            message.style.opacity = '0';
            message.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                message.remove();
            }, 500);
        }, 3000);
    });

    // Function to display empty cart
    function displayEmptyCart() {
        const tableContainer = document.querySelector('.cart-table');
        const actionsContainer = document.querySelector('.cart-actions');
        const summaryContainer = document.querySelector('.cart-summary');

        // Clear the containers
        tableContainer.innerHTML = '';
        actionsContainer.style.display = 'none';
        summaryContainer.style.display = 'none';

        // Create empty cart message
        const emptyCartMessage = document.createElement('div');
        emptyCartMessage.className = 'empty-cart';
        emptyCartMessage.style.textAlign = 'center';
        emptyCartMessage.style.padding = '40px 0';

        // Create empty cart icon
        const icon = document.createElement('div');
        icon.innerHTML = '<i class="fas fa-shopping-cart" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>';

        // Create message text
        const messageText = document.createElement('p');
        messageText.textContent = 'Không có sản phẩm nào trong giỏ hàng của bạn';
        messageText.style.fontSize = '18px';
        messageText.style.marginBottom = '20px';

        // Create continue shopping button
        const continueBtn = document.createElement('a');
        continueBtn.href = 'index.php';
        continueBtn.className = 'continue-btn';
        continueBtn.textContent = 'Tiếp tục mua hàng';
        continueBtn.style.display = 'inline-block';

        // Append elements to empty cart message
        emptyCartMessage.appendChild(icon);
        emptyCartMessage.appendChild(messageText);
        emptyCartMessage.appendChild(continueBtn);

        // Add empty cart message to the container
        tableContainer.appendChild(emptyCartMessage);
    }
});
