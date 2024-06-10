<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Conecte ao banco de dados
    $pdo = new PDO('mysql:host=localhost;dbname=openmarket', 'root', '');
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['is_seller'] = $user['is_seller'];
        if ($user['is_seller']) {
            header('Location: dashboard.php');
        } else {
            header('Location: index.php');
        }
    } else {
        echo "Email ou senha inválidos.";
    }
}
?>
