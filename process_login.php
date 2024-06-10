<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=openmarket', 'root', '');

// Obtenha os dados do formulário de login
$email = $_POST['email'];
$password = $_POST['password'];

// Verifique se o usuário existe
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    // Login bem-sucedido
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['is_seller'] = $user['is_seller'];
    header('Location: index.php');
    exit;
} else {
    // Login falhou
    $_SESSION['login_error'] = 'E-mail ou senha incorretos.';
    header('Location: login.php');
    exit;
}
?>
