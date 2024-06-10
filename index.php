<?php
session_start();
$notification = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $quantity = 1; // Default quantity to add

    // Conecte ao banco de dados
    $pdo = new PDO('mysql:host=localhost;dbname=openmarket', 'root', '');

    // Verifique a quantidade disponível no estoque
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        $available_stock = $product['stock'];

        // Verifique a quantidade atual no carrinho
        $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        $cart_item = $stmt->fetch();

        $current_cart_quantity = $cart_item ? $cart_item['quantity'] : 0;

        if ($current_cart_quantity + $quantity <= $available_stock) {
            if ($cart_item) {
                // Atualize a quantidade se o item já estiver no carrinho
                $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$quantity, $_SESSION['user_id'], $product_id]);
            } else {
                // Adicione o novo item ao carrinho
                $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $product_id, $quantity]);
            }
            $notification = 'Produto adicionado ao carrinho com sucesso!';
        } else if($available_stock === 0){
            $notification = 'Não existe estoque disponível.';
        } else {
            $notification = 'Quantidade excede o estoque disponível.';
        }
    } else {
        $notification = 'Produto não encontrado.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial | OpenMarket</title>
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
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        .product {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .product img {
            max-width: 100%;
            border-radius: 10px;
        }
        .product h3 {
            margin: 10px 0;
        }
        .product p {
            color: #555;
        }
        .btn {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Bem-vindo ao OpenMarket</h1>
            <p>Encontre os melhores produtos dos melhores vendedores.</p>

            <form action="#" method="get" style="background: none; box-shadow: none; padding: 30px;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['is_seller']): ?>
                        <a href="dashboard.php" class="btn">Dashboard</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn">Logout</a>
                    <a href="checkout.php" class="btn">Ir para o Carrinho</a>
                <?php else: ?>
                    <a href="login.php" class="btn">Login</a>
                    <a href="register.html" class="btn">Criar Conta</a>
                <?php endif; ?>
                    <input style="margin-left: 100px; width: 200px;" type="text" id="search" name="search" placeholder="Buscar produtos..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    <button type="submit" class="btn">Buscar</button>
            </form>
        </div>
    </header>

    <?php if ($notification): ?>
        <div class="notification" id="notification">
            <p><?= $notification ?></p>
        </div>
    <?php endif; ?>

    <section class="featured-products">
        <div class="container">
            <h1 style="padding: 10px; font-size: 30px;">Produtos em Destaque</h1>
            <div class="product-grid" id="product-list">
                <?php
                // Conecte ao banco de dados
                $pdo = new PDO('mysql:host=localhost;dbname=openmarket', 'root', '');
                $search_term = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';
                $stmt = $pdo->prepare("SELECT * FROM products WHERE is_active = TRUE AND (name LIKE ? OR description LIKE ?)");
                $stmt->execute([$search_term, $search_term]);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if(isset($_SESSION['user_id'])){
                    foreach ($products as $product) {
                        echo '<div class="product">';
                        echo '<img src="' . $product['image'] . '" alt="' . $product['name'] . '">';
                        echo '<h3>' . $product['name'] . '</h3>';
                        echo '<p>' . $product['description'] . '</p>';
                        echo '<span>$' . $product['price'] . '</span>';
                        echo '<p>Disponível: ' . $product['stock'] . '</p>';
                        echo '<form action="" method="POST" style="box-shadow:none; padding: 0px;">';
                        echo '<input type="hidden" name="product_id" value="' . $product['id'] . '">';
                        echo '<button type="submit" class="btn">Adicionar ao Carrinho</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                }
                else{
                    foreach ($products as $product) {
                        echo '<div class="product">';
                        echo '<img src="' . $product['image'] . '" alt="' . $product['name'] . '">';
                        echo '<h3>' . $product['name'] . '</h3>';
                        echo '<p>' . $product['description'] . '</p>';
                        echo '<span>$' . $product['price'] . '</span>';
                        echo '<p>Disponível: ' . $product['stock'] . '</p>';
                        echo '</div>';
                    }
                }

                ?>
            </div>
        </div>
    </section>

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
