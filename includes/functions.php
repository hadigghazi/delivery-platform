<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "toters_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function register_user($name, $email, $phone_number, $password) {
    global $conn;
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (name, email, phone_number, password_hash) VALUES ('$name', '$email', '$phone_number', '$password_hash')";
    $conn->query($sql);
}

function login_user($email, $password) {
    global $conn;
    
    $sql = "SELECT user_id, password_hash FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password === $row['password_hash']) {
            return ['user_id' => $row['user_id'], 'is_admin' => false];
        }
    }
    
    $sql = "SELECT admin_id FROM admins WHERE email = '$email' AND password_hash = '$password'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return ['user_id' => $row['admin_id'], 'is_admin' => true];
    }
    
    return false;
}

function get_user_profile($user_id) {
    global $conn;
    $sql = "SELECT * FROM user_profiles WHERE user_id = $user_id";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

function update_user_profile($user_id, $address, $profile_picture) {
    global $conn;
    $sql = "UPDATE user_profiles SET address = '$address', profile_picture = '$profile_picture' WHERE user_id = $user_id";
    $conn->query($sql);
}

function get_stores() {
    global $conn;
    $result = $conn->query("SELECT * FROM stores");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_all_stores() {
    global $conn;
    $stores = [];
    $result = $conn->query("SELECT store_id, name FROM stores ORDER BY name");
    while ($row = $result->fetch_assoc()) {
        $stores[] = $row;
    }
    return $stores;
}

function add_product($store_id, $name, $description, $price, $image, $popularity, $availability) {
    global $conn;
    $sql = "INSERT INTO products (store_id, name, description, price, image, popularity, availability) 
            VALUES ($store_id, '$name', '$description', $price, '$image', $popularity, $availability)";
    return $conn->query($sql);
}

function get_store_products($store_id) {
    global $conn;
    $sql = "SELECT * FROM products WHERE store_id = $store_id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function add_to_cart($user_id, $product_id, $quantity, $special_instructions = '') {
    global $conn;
    
    $cart_id = get_user_cart_id($user_id);
    if (!$cart_id) {
        $conn->query("INSERT INTO carts (user_id) VALUES ($user_id)");
        $cart_id = $conn->insert_id;
    }
    
    $sql = "SELECT cart_item_id FROM cart_items WHERE cart_id = $cart_id AND product_id = $product_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $conn->query("UPDATE cart_items SET quantity = quantity + $quantity, special_instructions = '$special_instructions' WHERE cart_item_id = " . $row['cart_item_id']);
    } else {
        $conn->query("INSERT INTO cart_items (cart_id, product_id, quantity, special_instructions) VALUES ($cart_id, $product_id, $quantity, '$special_instructions')");
    }
    
    return true;
}

function get_cart_items($user_id) {
    global $conn;
    
    $cart_id = get_user_cart_id($user_id);
    if (!$cart_id) {
        return [];
    }
    
    $sql = "SELECT ci.cart_item_id, ci.product_id, p.name, p.description, p.price, ci.quantity, ci.special_instructions
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.product_id
            WHERE ci.cart_id = $cart_id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_cart($user_id) {
    global $conn;
    $sql = "SELECT cart_items.*, products.name, products.price 
            FROM cart_items 
            INNER JOIN products ON cart_items.product_id = products.product_id 
            WHERE cart_items.cart_id = (SELECT cart_id FROM carts WHERE user_id = $user_id)";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_user_cart_id($user_id) {
    global $conn;
    
    $sql = "SELECT cart_id FROM carts WHERE user_id = $user_id";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        return $row['cart_id'];
    }
    return null;
}

function update_cart_quantity($user_id, $product_id, $quantity) {
    global $conn;
    $sql = "UPDATE cart_items SET quantity = $quantity WHERE cart_id = (SELECT cart_id FROM carts WHERE user_id = $user_id) AND product_id = $product_id";
    $conn->query($sql);
}

function remove_from_cart($user_id, $product_id) {
    global $conn;

    $cart_id = get_user_cart_id($user_id);
    if (!$cart_id) {
        return false;
    }

    $sql = "DELETE FROM cart_items WHERE cart_id = $cart_id AND product_id = $product_id";
    return $conn->query($sql);
}
?>
