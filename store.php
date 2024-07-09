<?php
require_once 'includes/functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$store_id = $_GET['store_id'];
$products = get_store_products($store_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Products</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Products</h1>
    </header>
    
    <main>
        <ul>
            <?php foreach ($products as $product): ?>
                <li>
                    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <p>Price: $<?php echo htmlspecialchars($product['price']); ?></p>
                    <p>Availability: <?php echo htmlspecialchars($product['availability'] ? 'In Stock' : 'Out of Stock'); ?></p>
                    <form method="POST" action="add_to_cart.php">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" min="1" value="1">
                        <label for="special_instructions">Special Instructions:</label>
                        <input type="text" id="special_instructions" name="special_instructions">
                        <button type="submit">Add to Cart</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>
    
    <footer>
        <p>&copy; 2024 Toters-like Delivery Platform</p>
    </footer>
</body>
</html>
