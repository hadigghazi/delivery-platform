<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit;
}

require_once 'includes/functions.php';

$stores = get_all_stores(); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $store_id = $_POST['store_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = ''; 
    $popularity = $_POST['popularity'];
    $availability = isset($_POST['availability']) ? 1 : 0;

    $result = add_product($store_id, $name, $description, $price, $image, $popularity, $availability);
    if ($result) {
        $success_message = "Product added successfully!";
    } else {
        $error_message = "Failed to add product. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Add Product</h1>
    </header>
    
    <main>
        <form method="POST" action="add_product.php">
            <label for="store_id">Select Store:</label>
            <select id="store_id" name="store_id" required>
                <?php foreach ($stores as $store): ?>
                    <option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
                <?php endforeach; ?>
            </select>
            
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea>
            
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>
            
            <label for="image">Image URL:</label>
            <input type="text" id="image" name="image">
            
            <label for="popularity">Popularity:</label>
            <input type="number" id="popularity" name="popularity" required>
            
            <label for="availability">Availability:</label>
            <input type="checkbox" id="availability" name="availability" value="1">
            
            <button type="submit">Add Product</button>
        </form>
        
        <?php if (isset($success_message)): ?>
            <p style="color: green;"><?php echo $success_message; ?></p>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </main>
    
    <footer>
        <p>&copy; 2024 Toters-like Delivery Platform</p>
    </footer>
</body>
</html>
