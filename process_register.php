<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $is_seller = isset($_POST['is_seller']) ? 1 : 0;

    if ($password !== $confirm_password) {
        echo "As senhas não coincidem.";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Conecte ao banco de dados
    $pdo = new PDO('mysql:host=localhost;dbname=openmarket', 'root', '');
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, is_seller) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $hashed_password, $is_seller]);
    echo "Registro concluído com sucesso!";
    header('Location: login.php');
}
?>
