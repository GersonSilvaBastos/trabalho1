<?php
session_start();
$user_id = $_SESSION['user_id'];

// Conecte ao banco de dados
$pdo = new PDO('mysql:host=localhost;dbname=openmarket', 'root', '');

// Verifique a ação solicitada
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'fetch':
        fetchCartItems($pdo, $user_id);
        break;

    case 'add':
        addItemToCart($pdo, $user_id);
        break;

    case 'update':
        updateCartItem($pdo, $user_id);
        break;

    case 'remove':
        removeCartItem($pdo, $user_id);
        break;

    default:
        echo json_encode(['error' => 'Ação inválida']);
        break;
}

function fetchCartItems($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT cart.*, products.name, products.price FROM cart JOIN products ON cart.product_id = products.id WHERE cart.user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($cart_items);
}

function addItemToCart($pdo, $user_id) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Verifique a quantidade disponível no estoque
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        $available_stock = $product['stock'];

        // Verifique a quantidade atual no carrinho
        $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $cart_item = $stmt->fetch();

        $current_cart_quantity = $cart_item ? $cart_item['quantity'] : 0;

        if ($current_cart_quantity + $quantity <= $available_stock) {
            if ($cart_item) {
                // Atualize a quantidade se o item já estiver no carrinho
                $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$quantity, $user_id, $product_id]);
            } else {
                // Adicione o novo item ao carrinho
                $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $product_id, $quantity]);
            }

            echo json_encode(['success' => 'Item adicionado ao carrinho']);
        } else {
            echo json_encode(['error' => 'Quantidade excede o estoque disponível']);
        }
    } else {
        echo json_encode(['error' => 'Produto não encontrado']);
    }
}

function updateCartItem($pdo, $user_id) {
    $item_id = $_POST['id'];
    $new_quantity = $_POST['quantity'];

    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND id = ?");
    $stmt->execute([$new_quantity, $user_id, $item_id]);

    echo json_encode(['success' => 'Quantidade atualizada com sucesso']);
}

function removeCartItem($pdo, $user_id) {
    $item_id = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND id = ?");
    $stmt->execute([$user_id, $item_id]);

    echo json_encode(['success' => 'Item removido com sucesso']);
}
?>
