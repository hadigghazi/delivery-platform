<?php
require_once 'includes/functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$profile = get_user_profile($user_id);

$address = isset($profile['address']) ? htmlspecialchars($profile['address']) : 'No address provided';
$profile_picture = isset($profile['profile_picture']) ? htmlspecialchars($profile['profile_picture']) : 'No profile picture';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = $_POST['address'];
    $profile_picture = $_POST['profile_picture'];
    update_user_profile($user_id, $address, $profile_picture);
    $profile = get_user_profile($user_id);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Profile</h1>
    </header>
    
    <main>
        <form method="POST" action="profile.php">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo $address; ?>">
            <br>
            <label for="profile_picture">Profile Picture:</label>
            <input type="text" id="profile_picture" name="profile_picture" value="<?php echo $profile_picture; ?>">
            <br>
            <button type="submit">Update Profile</button>
        </form>
    </main>
    
    <footer>
        <p>&copy; 2024 Toters-like Delivery Platform</p>
    </footer>
</body>
</html>
