<?php
session_start();
if (!isset($_SESSION['user_id']) || (!$_SESSION['is_seller'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];

    // Conecte ao banco de dados
    $pdo = new PDO('mysql:host=localhost;dbname=openmarket', 'root', '');

    // Comece uma transação
    $pdo->beginTransaction();

    try {
        // Remova todas as referências ao produto na tabela `cart`
        $stmt = $pdo->prepare("DELETE FROM cart WHERE product_id = ?");
        $stmt->execute([$product_id]);

        // Remova o produto
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND user_id = ?");
        $stmt->execute([$product_id, $_SESSION['user_id']]);

        // Confirme a transação
        $pdo->commit();

        header('Location: dashboard.php');
        exit;
    } catch (Exception $e) {
        // Desfaça a transação em caso de erro
        $pdo->rollBack();
        echo "Failed: " . $e->getMessage();
    }
}
?>
