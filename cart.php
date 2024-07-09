<?php
require_once 'includes/functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_items = get_cart($user_id);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_quantity'])) {
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        update_cart_quantity($user_id, $product_id, $quantity);
    } elseif (isset($_POST['remove_item'])) {
        $product_id = $_POST['product_id'];
        remove_from_cart($user_id, $product_id);
    }
    $cart_items = get_cart($user_id); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Cart</h1>
    </header>
    
    <main>
        <form method="POST" action="cart.php">
            <ul>
                <?php foreach ($cart_items as $item): ?>
                    <li>
                        <h2><?php echo htmlspecialchars($item['name']); ?></h2>
                        <p>Price: $<?php echo htmlspecialchars($item['price']); ?></p>
                        <p>Quantity: 
                            <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">
                            <button type="submit" name="update_quantity">Update Quantity</button>
                        </p>
                        <p>Special Instructions: <?php echo htmlspecialchars($item['special_instructions']); ?></p>
                        <button type="submit" name="remove_item">Remove</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </form>
    </main>
    
    <footer>
        <p>&copy; 2024 Toters-like Delivery Platform</p>
    </footer>
</body>
</html>
