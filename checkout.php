<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | OpenMarket</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/checkout.js" defer></script>
</head>
<body>
    <div class="container">
        <header>Checkout</header>
        <div class="cart-list" id="cart-list">
            <!-- Cart items will be loaded here dynamically -->
        </div>
        <div class="total" id="total">
            Total: $0.00
        </div>
        <button class="checkout">Finalizar Compra</button>
        <a href="index.php" class="btn">Voltar ao Index</a>
    </div>
</body>
</html>
