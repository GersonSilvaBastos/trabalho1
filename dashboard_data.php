<?php
session_start();
$user_id = $_SESSION['user_id'];

// Conecte ao banco de dados
$pdo = new PDO('mysql:host=localhost;dbname=openmarket', 'root', '');

// Buscar estatísticas de vendas
$sales_stmt = $pdo->prepare("SELECT SUM(price * quantity) AS total_sales, COUNT(*) AS number_of_orders, SUM(quantity) AS products_sold FROM orders WHERE user_id = ?");
$sales_stmt->execute([$user_id]);
$sales_stats = $sales_stmt->fetch(PDO::FETCH_ASSOC);

// Buscar produtos do vendedor logado
$products_stmt = $pdo->prepare("SELECT * FROM products WHERE user_id = ?");
$products_stmt->execute([$user_id]);
$products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar pedidos do vendedor logado
$orders_stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ?");
$orders_stmt->execute([$user_id]);
$orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);

$data = [
    'sales_stats' => $sales_stats,
    'products' => $products,
    'orders' => $orders,
];

echo json_encode($data);
?>
