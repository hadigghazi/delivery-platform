<?php
require_once 'includes/functions.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    update_cart_quantity($_SESSION['user_id'], $product_id, $quantity);
    header("Location: cart.php");
    exit;
}
?>
