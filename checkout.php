<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Conecte ao banco de dados
$pdo = new PDO('mysql:host=localhost;dbname=openmarket', 'root', '');

// Inicialize a variável $total
$total = 0;

// Verifique se uma ação de remoção ou atualização foi solicitada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_item_id'])) {
        $remove_item_id = $_POST['remove_item_id'];
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $remove_item_id]);
    } elseif (isset($_POST['update_item_id']) && isset($_POST['new_quantity'])) {
        $update_item_id = $_POST['update_item_id'];
        $new_quantity = $_POST['new_quantity'];

        // Verifique a quantidade disponível no estoque
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$update_item_id]);
        $product = $stmt->fetch();

        if ($product && $new_quantity <= $product['stock']) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$new_quantity, $_SESSION['user_id'], $update_item_id]);
        } else {
            $error_message = 'Quantidade excede o estoque disponível.';
        }
    } elseif (isset($_POST['finalize_purchase'])) {
        // Começar transação
        $pdo->beginTransaction();

        // Buscar itens do carrinho
        $stmt = $pdo->prepare("SELECT cart.*, products.name, products.price, products.stock, products.user_id as seller_id FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $order_id = null;
        try {
            // Calcular o total do pedido
            $total = 0;
            foreach ($cart_items as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            // Registrar pedido
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
            $stmt->execute([$_SESSION['user_id'], $total]);
            $order_id = $pdo->lastInsertId();

            foreach ($cart_items as $item) {
                // Atualizar estoque
                $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stmt->execute([$item['quantity'], $item['product_id']]);

                // Registrar itens do pedido
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, seller_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price'], $item['seller_id']]);
            }

            // Limpar carrinho
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);

            // Commit da transação
            $pdo->commit();
            $success_message = 'Compra finalizada com sucesso!';
        } catch (Exception $e) {
            // Rollback da transação em caso de erro
            $pdo->rollBack();
            $error_message = 'Erro ao finalizar a compra: ' . $e->getMessage();
        }
    }
}

// Buscar itens do carrinho
$stmt = $pdo->prepare("SELECT cart.*, products.name, products.price, products.stock FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular o total do carrinho
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | OpenMarket</title>
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
    </style>
</head>
<body>
    <div class="container">
        <header>Checkout</header>
        <?php if (isset($error_message)): ?>
            <div class="notification" id="notification">
                <p><?= $error_message ?></p>
            </div>
        <?php endif; ?>
        <?php if (isset($success_message)): ?>
            <div class="notification show" id="notification">
                <p><?= $success_message ?></p>
            </div>
        <?php endif; ?>
        <div class="cart-list" id="cart-list">
            <?php if (count($cart_items) > 0): ?>
                <?php foreach ($cart_items as $item): ?>
                    <div class="item">
                        <span><?= htmlspecialchars($item['name']) ?></span>
                        <span>$<?= htmlspecialchars($item['price']) ?></span>
                        <form action="checkout.php" method="post" style="display:inline;">
                            <input type="hidden" name="update_item_id" value="<?= $item['product_id'] ?>">
                            <input type="number" name="new_quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock'] ?>">
                            <button type="submit" class="btn">Atualizar</button>
                        </form>
                        <form action="checkout.php" method="post" style="display:inline;">
                            <input type="hidden" name="remove_item_id" value="<?= $item['product_id'] ?>">
                            <button type="submit" class="btn">Remover</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Seu carrinho está vazio.</p>
            <?php endif; ?>
        </div>
        <div class="total">
            Total: $<?= number_format($total, 2) ?>
        </div>
        <form action="checkout.php" method="post">
            <input type="hidden" name="finalize_purchase" value="1">
            <button type="submit" class="btn checkout" style="padding: 16px">Finalizar Compra</button>
        </form>
        <a href="index.php" class="btn" style="margin-top: 10px">Voltar ao Index</a>
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
