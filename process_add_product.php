<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_seller']) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_FILES['image'];

    // Defina o diretório de destino para as imagens
    $target_dir = "images/";
    $target_file = $target_dir . basename($image["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verifique se o arquivo é uma imagem
    $check = getimagesize($image["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "O arquivo não é uma imagem.";
        $uploadOk = 0;
    }

    // Verifique se o arquivo já existe
    // if (file_exists($target_file)) {
    //     echo "Desculpe, o arquivo já existe.";
    //     $uploadOk = 0;
    // }

    // Verifique o tamanho do arquivo
    if ($image["size"] > 500000) {
        echo "Desculpe, o seu arquivo é muito grande.";
        $uploadOk = 0;
    }

    // Permitir certos formatos de arquivo
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Desculpe, apenas arquivos JPG, JPEG, PNG e GIF são permitidos.";
        $uploadOk = 0;
    }

    // Verifique se $uploadOk está definido como 0 por algum erro
    if ($uploadOk == 0) {
        echo "Desculpe, o seu arquivo não foi enviado.";
    } else {
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            // Conecte ao banco de dados
            $pdo = new PDO('mysql:host=localhost;dbname=openmarket', 'root', '');
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, image, user_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $stock, $target_file, $_SESSION['user_id']]);
            echo "O arquivo " . htmlspecialchars(basename($image["name"])) . " foi enviado.";
            header('Location: dashboard.php');
        } else {
            echo "Desculpe, houve um erro ao enviar o seu arquivo.";
        }
    }
}
?>
