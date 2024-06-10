<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | OpenMarket</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
        .login-form {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .login-form header {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .login-form form div {
            margin-bottom: 15px;
        }
        .login-form input[type="email"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-form input[type="checkbox"] {
            margin-right: 10px;
        }
        .login-form button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .login-form button:hover {
            background-color: #218838;
        }
        .login-form a {
            color: #007bff;
            text-decoration: none;
        }
        .login-form a:hover {
            text-decoration: underline;
        }
        .notification {
            background-color: #ff4444;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
    <script src="js/login.js" defer></script>
</head>
<body>
    <div class="login-form" style="width: 500px">
        <header>Login</header>
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="notification">
                <?= $_SESSION['login_error'] ?>
            </div>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>
        <form id="loginForm" action="process_login.php" method="post">
            <div>
                <input type="email" name="email" placeholder="Email" autocomplete="off" required>
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" autocomplete="off" required>
            </div>
            <div>
                <label>
                    <input type="checkbox" name="remember"> Lembrar
                </label>
                <a href="#">Esqueceu password</a>
            </div>
            <div>
                <button type="submit">Logar</button>
            </div>
            <div>
                <p>Ainda não tem conta? <a href="register.html">Registar</a></p>
            </div>
            <div>
                <a href="index.php">Voltar</a>
            </div>
        </form>
    </div>
</body>
</html>
