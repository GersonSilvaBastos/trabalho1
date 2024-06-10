<?php
session_start();
$notification = '';

if (!isset($_SESSION['user_id']) || (!$_SESSION['is_seller'])) {
    header('Location: login.php');
    exit;
}

// Conecte ao banco de dados
$pdo = new PDO('mysql:host=localhost;dbname=openmarket', 'root', '');

// Verifique se uma ação foi solicitada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'deactivate' && isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        $stmt = $pdo->prepare("UPDATE products SET is_active = FALSE WHERE id = ?");
        if ($stmt->execute([$product_id])) {
            $notification = 'Produto marcado como indisponível com sucesso!';
        } else {
            $notification = 'Falha ao marcar o produto como indisponível.';
        }
    } elseif ($_POST['action'] == 'activate' && isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        $stmt = $pdo->prepare("UPDATE products SET is_active = TRUE WHERE id = ?");
        if ($stmt->execute([$product_id])) {
            $notification = 'Produto ativado com sucesso!';
        } else {
            $notification = 'Falha ao ativar o produto.';
        }
    } elseif ($_POST['action'] == 'delete' && isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        $pdo->beginTransaction();
        try {
            // Remova referências na tabela order_items
            $stmt = $pdo->prepare("DELETE FROM order_items WHERE product_id = ?");
            $stmt->execute([$product_id]);

            // Remova o produto da tabela products
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$product_id]);

            $pdo->commit();
            $notification = 'Produto removido permanentemente com sucesso!';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $notification = 'Falha ao remover o produto permanentemente: ' . $e->getMessage();
        }
    } elseif ($_POST['action'] == 'restock' && isset($_POST['restock_product_id']) && isset($_POST['restock_quantity'])) {
        $product_id = $_POST['restock_product_id'];
        $restock_quantity = $_POST['restock_quantity'];
        $stmt = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        if ($stmt->execute([$restock_quantity, $product_id])) {
            $notification = 'Estoque atualizado com sucesso!';
        } else {
            $notification = 'Falha ao atualizar o estoque.';
        }
    } elseif ($_POST['action'] == 'complete_order' && isset($_POST['complete_order_id'])) {
        $order_id = $_POST['complete_order_id'];
        $stmt = $pdo->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
        if ($stmt->execute([$order_id])) {
            $notification = 'Encomenda marcada como completa!';
        } else {
            $notification = 'Falha ao completar a encomenda.';
        }
    }
}

// Buscar produtos do vendedor logado
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE user_id = ?");
$stmt->execute([$user_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar todas as encomendas para estatísticas e exibição
$stmt = $pdo->prepare("
    SELECT order_items.*, orders.user_id AS buyer_id, orders.id AS order_id, products.name AS product_name, orders.status AS order_status
    FROM order_items
    JOIN orders ON order_items.order_id = orders.id
    JOIN products ON order_items.product_id = products.id
    WHERE order_items.seller_id = ?
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular estatísticas de vendas
$total_sales = 0;
$number_of_orders = 0;
$products_sold = 0;

foreach ($orders as $order) {
    $total_sales += $order['price'] * $order['quantity'];
    $number_of_orders++;
    $products_sold += $order['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | OpenMarket</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .notification {
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #444;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            display: none;
        }
        .notification.show {
            display: block;
        }
        .product-list ul, .order-list ul {
            list-style: none;
            padding: 0;
        }
        .product-list li, .order-list li {
            background: #f9f9f9;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .product-list li form, .order-list li form {
            display: inline;
        }
        .container {
            margin: 0 auto;
            padding: 20px;
        }
        nav a {
            margin-right: 10px;
        }
        .btn-restock {
            margin-left: 10px;
        }
        .order-list-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header style="margin-bottom: 20px; padding: 20px;">
            <h2>Dashboard</h2>
            <nav style="padding-top: 10px;">
                <a href="index.php" class="btn">Voltar ao Index</a>
                <a href="add_product.html" class="btn">Adicionar Produto</a>
                <a href="logout.php" class="btn">Logout</a>
            </nav>
        </header>
        <?php if ($notification): ?>
            <div class="notification show" id="notification">
                <p><?= $notification ?></p>
            </div>
        <?php endif; ?>
        <section class="sales-stats">
            <h2>Estatísticas de Vendas</h2>
            <p>Total de Vendas: $<?= number_format($total_sales, 2) ?></p>
            <p>Número de Pedidos: <?= $number_of_orders ?></p>
            <p>Produtos Vendidos: <?= $products_sold ?></p>
        </section>
        <section style="margin-top: 20px" class="product-list">
            <h2>Meus Produtos</h2>
            <ul style="padding-top: 34px; margin-left: -185px;" id="product-list">
                <?php foreach ($products as $product): ?>
                    <li style="margin-bottom: 8px;">
                        <p>ID: <?= $product['id'] ?></p>
                        <p>Nome: <?= $product['name'] ?></p>
                        <form action="dashboard.php" method="post" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="action" value="deactivate">
                            <button type="submit" class="btn">Marcar como Indisponível</button>
                        </form>
                        <form action="dashboard.php" method="post" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="action" value="activate">
                            <button type="submit" class="btn">Ativar</button>
                        </form>
                        <form action="dashboard.php" method="post" style="display:inline;">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn">Excluir Permanentemente</button>
                        </form>
                        <form style="margin-left: 20px" action="dashboard.php" method="post" style="display:inline;">
                            <input type="hidden" name="restock_product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="action" value="restock">
                            <input style="width: 50px;" type="number" name="restock_quantity" min="1">
                            <button style="margin-left: 0px" type="submit" class="btn btn-restock">Restock</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <section class="order-list">
            <h2>Encomendas Recebidas</h2>
            <div style="padding-top: 44px; margin-left: -288px;" class="order-list-grid" id="order-list">
                <?php foreach ($orders as $order): ?>
                    <div style="border: 1px solid #ddd; border-radius: 5px; padding: 10px; background: #f9f9f9;">
                        <p>ID do Pedido: <?= $order['order_id'] ?></p>
                        <p>ID do Produto: <?= $order['product_id'] ?></p>
                        <p>Nome do Produto: <?= $order['product_name'] ?></p>
                        <p>Quantidade: <?= $order['quantity'] ?></p>
                        <?php if ($order['order_status'] === 'pending'): ?>
                            <form action="dashboard.php" method="post">
                                <input type="hidden" name="complete_order_id" value="<?= $order['order_id'] ?>">
                                <input type="hidden" name="action" value="complete_order">
                                <button type="submit" class="btn">Pronto</button>
                            </form>
                        <?php else: ?>
                            <p>Status: Completo</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const notification = document.getElementById('notification');
            if (notification) {
                notification.classList.add('show');
                setTimeout(() => {
                    notification.classList.remove('show');
                }, 3000);
            }
        });
    </script>
</body>
</html>
