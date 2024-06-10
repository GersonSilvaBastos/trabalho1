<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial | OpenMarket</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/index.js" defer></script>
</head>
<body>
    <header>
        <div class="container">
            <h1>Bem-vindo ao OpenMarket</h1>
            <p>Encontre os melhores produtos dos melhores vendedores.</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['is_seller']): ?>
                    <a href="dashboard.php" class="btn">Dashboard</a>
                <?php endif; ?>
                <a href="logout.php" class="btn">Logout</a>
            <?php else: ?>
                <a href="login.html" class="btn">Login</a>
                <a href="register.html" class="btn">Criar Conta</a>
            <?php endif; ?>
            <form action="#" method="get">
                <input type="text" id="search" placeholder="Buscar produtos...">
                <button type="submit" class="btn">Buscar</button>
            </form>
        </div>
    </header>

    <section class="featured-products">
        <div class="container">
            <h2>Produtos em Destaque</h2>
            <div class="product-card" id="product-list">
                <!-- Products will be loaded here dynamically -->
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('fetch_products.php')
                .then(response => response.json())
                .then(data => {
                    const productContainer = document.getElementById('product-list');
                    productContainer.innerHTML = '';
                    data.forEach(product => {
                        const productElement = document.createElement('div');
                        productElement.className = 'product';
                        productElement.innerHTML = `
                            <img src="${product.image}" alt="${product.name}">
                            <h3>${product.name}</h3>
                            <p>${product.description}</p>
                            <span>$${product.price}</span>
                            <button class="btn add-to-cart" data-id="${product.id}">Adicionar ao Carrinho</button>
                        `;
                        productContainer.appendChild(productElement);
                    });

                    // Add event listeners to "Add to Cart" buttons
                    document.querySelectorAll('.add-to-cart').forEach(button => {
                        button.addEventListener('click', function() {
                            const productId = this.getAttribute('data-id');
                            addToCart(productId);
                        });
                    });
                });
        });

        function addToCart(productId) {
            fetch('cart_actions.php?action=add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'product_id': productId,
                    'quantity': 1
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'checkout.php';
                } else {
                    displayMessage('Erro ao adicionar ao carrinho', 'error');
                }
            });
        }

        function displayMessage(message, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}`;
            messageDiv.innerText = message;
            document.body.prepend(messageDiv);
            setTimeout(() => messageDiv.remove(), 3000);
        }
    </script>
</body>
</html>
