<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $image = $_POST['image'];

        $sql = "INSERT INTO products (name, description, price, image) VALUES (:name, :description, :price, :image)";
        $params = [':name' => $name, ':description' => $description, ':price' => $price, ':image' => $image];

        if ($db->query($sql, $params)) {
            echo "New product added successfully";
        } else {
            echo "Error: " . $db->lastErrorMsg();
        }
    } elseif (isset($_POST['edit_product'])) {
        // Handle product edit
    } elseif (isset($_POST['delete_product'])) {
        // Handle product deletion
    }
}

$products = $db->fetchAll("SELECT * FROM products");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | OpenMarket</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <header>Dashboard</header>

        <section class="product-list">
            <h2>Meus Produtos</h2>
            <ul>
                <?php foreach ($products as $product): ?>
                <li>
                    <span><?php echo $product['name']; ?></span>
                    <span>Preço: <?php echo $product['price']; ?></span>
                    <span>Disponível: <?php echo $product['available']; ?></span>
                    <button class="edit-product">Editar</button>
                    <button class="delete-product">Eliminar</button>
                </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section class="add-product">
            <h2>Adicionar Produto</h2>
            <form action="dashboard.php" method="post">
                <input type="text" name="name" placeholder="Nome do Produto" required>
                <input type="text" name="description" placeholder="Descrição do Produto" required>
                <input type="number" name="price" placeholder="Preço" required>
                <input type="text" name="image" placeholder="Imagem URL" required>
                <button type="submit" name="add_product">Adicionar Produto</button>
            </form>
        </section>
    </div>
</body>
</html>

