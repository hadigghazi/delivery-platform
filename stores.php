<?php
require_once 'includes/functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stores = get_stores();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stores</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Stores</h1>
    </header>
    
    <main>
        <ul>
            <?php foreach ($stores as $store): ?>
                <li>
                    <h2><?php echo htmlspecialchars($store['name']); ?></h2>
                    <p>Category: <?php echo htmlspecialchars($store['category']); ?></p>
                    <p>Location: <?php echo htmlspecialchars($store['location']); ?></p>
                    <p>Rating: <?php echo htmlspecialchars($store['rating']); ?></p>
                    <a href="store.php?store_id=<?php echo $store['store_id']; ?>">View Products</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>
</body>
</html>
