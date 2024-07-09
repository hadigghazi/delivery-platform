<?php
session_start();
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['cart_item_id'])) {
    $cart_item_id = $_GET['cart_item_id'];
    remove_from_cart($cart_item_id);
}

header("Location: view_cart.php");
exit;
?>
