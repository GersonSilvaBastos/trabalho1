<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_seller']) {
    header('Location: login.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | OpenMarket</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="js/dashboard.js" defer></script>
</head>
<body>
    <div class="container">
        <header>Dashboard</header>
        <nav>
            <a href="index.php" class="btn">Voltar ao Index</a>
            <a href="add_product.html" class="btn">Adicionar Produto</a>
            <a href="logout.php" class="btn">Logout</a>
        </nav>
        <section class="sales-stats">
            <h2>Estatísticas de Vendas</h2>
            <p id="total-sales">Total de Vendas: $0</p>
            <p id="number-of-orders">Número de Pedidos: 0</p>
            <p id="products-sold">Produtos Vendidos: 0</p>
        </section>
        <section class="product-list">
            <h2>Meus Produtos</h2>
            <ul id="product-list">
                <!-- Products will be loaded here dynamically -->
            </ul>
        </section>
        <section class="order-list">
            <h2>Encomendas Recebidas</h2>
            <ul id="order-list">
                <!-- Orders will be loaded here dynamically -->
            </ul>
        </section>
    </div>
</body>
</html>
