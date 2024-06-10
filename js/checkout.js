document.addEventListener('DOMContentLoaded', function() {
    fetchCartItems();

    function fetchCartItems() {
        fetch('cart_actions.php?action=fetch')
            .then(response => response.json())
            .then(data => {
                const cartList = document.getElementById('cart-list');
                const totalElement = document.getElementById('total');
                cartList.innerHTML = '';
                let total = 0;
                data.forEach(item => {
                    const itemElement = document.createElement('div');
                    itemElement.className = 'item';
                    itemElement.innerHTML = `
                        <span>${item.name}</span>
                        <span>$${item.price}</span>
                        <input type="number" value="${item.quantity}" data-price="${item.price}" data-id="${item.id}">
                        <button class="remove-item" data-id="${item.id}">Remover</button>
                    `;
                    cartList.appendChild(itemElement);
                    total += item.price * item.quantity;
                });
                totalElement.innerText = `Total: $${total.toFixed(2)}`;

                // Add event listeners for item removal and quantity changes
                document.querySelectorAll('.remove-item').forEach(button => {
                    button.addEventListener('click', function() {
                        const itemId = this.getAttribute('data-id');
                        removeCartItem(itemId);
                    });
                });

                document.querySelectorAll('.item input').forEach(input => {
                    input.addEventListener('change', function() {
                        const itemId = this.getAttribute('data-id');
                        const newQuantity = this.value;
                        updateCartItem(itemId, newQuantity);
                    });
                });
            });
    }

    function removeCartItem(itemId) {
        fetch('cart_actions.php?action=remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'id': itemId
            })
        })
        .then(response => response.json())
        .then(() => {
            displayMessage('Item removido com sucesso!', 'success');
            fetchCartItems();
        });
    }

    function updateCartItem(itemId, newQuantity) {
        fetch('cart_actions.php?action=update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'id': itemId,
                'quantity': newQuantity
            })
        })
        .then(response => response.json())
        .then(() => {
            displayMessage('Quantidade atualizada com sucesso!', 'success');
            fetchCartItems();
        });
    }

    function displayMessage(message, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.innerText = message;
        document.body.prepend(messageDiv);
        setTimeout(() => messageDiv.remove(), 3000);
    }
});
