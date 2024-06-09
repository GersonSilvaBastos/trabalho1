<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
    $params = [':name' => $name, ':email' => $email, ':password' => $password];

    try {
        $db->query($sql, $params);
        echo "New record created successfully";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>



