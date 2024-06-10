<?php
$pdo = new PDO('mysql:host=localhost;dbname=openmarket', 'root', '');
$stmt = $pdo->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($products);
?>
