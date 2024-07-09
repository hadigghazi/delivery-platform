<?php
require_once 'includes/functions.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $location = $_POST['location'];
    $rating = $_POST['rating'];

    $stmt = $conn->prepare("INSERT INTO stores (name, category, location, rating) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $name, $category, $location, $rating);
    $stmt->execute();
    $stmt->close();
}

$stores = get_stores();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
    </header>
    
    <main>
        <section>
            <h2>Add New Store</h2>
            <form method="POST" action="admin.php">
                <label for="name">Store Name:</label>
                <input type="text" id="name" name="name" required>
                
                <label for="category">Category:</label>
                <input type="text" id="category" name="category" required>
                
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required>
                
                <label for="rating">Rating:</label>
                <input type="number" id="rating" name="rating" step="0.1" min="0" max="5" required>
                
                <button type="submit">Add Store</button>
            </form>
        </section>
        
        <section>
        <a href="add_product.php">Add Product</a>
            <h2>Manage Stores</h2>
            <ul>
                <?php foreach ($stores as $store): ?>
                    <li>
                        <h3><?php echo htmlspecialchars($store['name']); ?></h3>
                        <p>Category: <?php echo htmlspecialchars($store['category']); ?></p>
                        <p>Location: <?php echo htmlspecialchars($store['location']); ?></p>
                        <p>Rating: <?php echo htmlspecialchars($store['rating']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>
    
    <footer>
        <p>&copy; 2024 Toters-like Delivery Platform</p>
    </footer>
</body>
</html>
