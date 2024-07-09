<?php
require_once 'includes/functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $special_instructions = $_POST['special_instructions'];

    add_to_cart($user_id, $product_id, $quantity, $special_instructions);
    header("Location: cart.php");
    exit;
}
?>
