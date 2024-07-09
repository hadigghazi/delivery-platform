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
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone_number, password_hash) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone_number, $password_hash);
    $stmt->execute();
    $stmt->close();
}

function login_user($email, $password) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT user_id, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $password_hash);
        $stmt->fetch();
        
        if ($password === $password_hash) {
            return ['user_id' => $user_id, 'is_admin' => false];
        }
    }
    
    $stmt->close();
    
    $admin_stmt = $conn->prepare("SELECT admin_id FROM admins WHERE email = ? AND password_hash = ?");
    $admin_stmt->bind_param("ss", $email, $password);
    $admin_stmt->execute();
    $admin_stmt->store_result();
    
    if ($admin_stmt->num_rows > 0) {
        $admin_stmt->bind_result($admin_id);
        $admin_stmt->fetch();
        
        return ['user_id' => $admin_id, 'is_admin' => true];
    }
    
    $admin_stmt->close();
    
    return false;
}

function get_user_profile($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $profile = $result->fetch_assoc();
    $stmt->close();
    return $profile;
}


function update_user_profile($user_id, $address, $profile_picture) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user_profiles SET address = ?, profile_picture = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $address, $profile_picture, $user_id);
    $stmt->execute();
    $stmt->close();
}

function get_stores() {
    global $conn;
    $result = $conn->query("SELECT * FROM stores");
    $stores = $result->fetch_all(MYSQLI_ASSOC);
    return $stores;
}

function get_all_stores() {
    global $conn;
    
    $stores = [];
    $stmt = $conn->prepare("SELECT store_id, name FROM stores ORDER BY name");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $stores[] = $row;
    }
    
    $stmt->close();
    
    return $stores;
}

function add_product($store_id, $name, $description, $price, $image, $popularity, $availability) {
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO products (store_id, name, description, price, image, popularity, availability) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdssi", $store_id, $name, $description, $price, $image, $popularity, $availability);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

function get_store_products($store_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM products WHERE store_id = ?");
    $stmt->bind_param("i", $store_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $products;
}

function add_to_cart($user_id, $product_id, $quantity, $special_instructions = '') {
    global $conn;
    
    $cart_id = get_user_cart_id($user_id);
    if (!$cart_id) {
        $stmt = $conn->prepare("INSERT INTO carts (user_id) VALUES (?)");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
        
        $cart_id = $conn->insert_id;
    }
    
    $stmt = $conn->prepare("SELECT cart_item_id FROM cart_items WHERE cart_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $cart_id, $product_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($cart_item_id);
        $stmt->fetch();
        
        $stmt_update = $conn->prepare("UPDATE cart_items SET quantity = quantity + ?, special_instructions = ? WHERE cart_item_id = ?");
        $stmt_update->bind_param("isi", $quantity, $special_instructions, $cart_item_id);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        $stmt_insert = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, quantity, special_instructions) VALUES (?, ?, ?, ?)");
        $stmt_insert->bind_param("iiis", $cart_id, $product_id, $quantity, $special_instructions);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
    
    $stmt->close();
    
    return true;
}

function get_cart_items($user_id) {
    global $conn;
    
    $cart_id = get_user_cart_id($user_id);
    if (!$cart_id) {
        return [];
    }
    
    $items = [];
    $stmt = $conn->prepare("SELECT ci.cart_item_id, ci.product_id, p.name, p.description, p.price, ci.quantity, ci.special_instructions
                            FROM cart_items ci
                            JOIN products p ON ci.product_id = p.product_id
                            WHERE ci.cart_id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    
    $stmt->close();
    
    return $items;
}

function get_cart($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT cart_items.*, products.name, products.price FROM cart_items INNER JOIN products ON cart_items.product_id = products.product_id WHERE cart_items.cart_id = (SELECT cart_id FROM carts WHERE user_id = ?)");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $cart_items;
}

function get_user_cart_id($user_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT cart_id FROM carts WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($cart_id);
    $stmt->fetch();
    $stmt->close();
    
    return $cart_id;
}

function update_cart_quantity($user_id, $product_id, $quantity) {
    global $conn;
    $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = (SELECT cart_id FROM carts WHERE user_id = ?) AND product_id = ?");
    $stmt->bind_param("iii", $quantity, $user_id, $product_id);
    $stmt->execute();
    $stmt->close();
}

function remove_from_cart($user_id, $product_id) {
    global $conn;

    $cart_id = get_user_cart_id($user_id);
    if (!$cart_id) {
        return false;
    }

    $query = "DELETE FROM cart_items WHERE cart_id = ? AND product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $cart_id, $product_id);

    if ($stmt->execute()) {
        $stmt->close();
        return true; 
    } else {
        $stmt->close();
        return false; 
    }
}
?>
