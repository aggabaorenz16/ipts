<?php
session_start();
require_once('./db/database.php');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $user_email = $_SESSION['email'];

    // Fetch product details
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();

        // Check if product already exists in user's cart
        $check_sql = "SELECT * FROM cart_tbl WHERE user_email = ? AND product_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $user_email, $product_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // If already in cart, update quantity
            $update_sql = "UPDATE cart_tbl SET quantity = quantity + ? WHERE user_email = ? AND product_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("isi", $quantity, $user_email, $product_id);
            $update_stmt->execute();
        } else {
            // Insert new cart item
            $insert_sql = "INSERT INTO cart_tbl (user_email, product_id, product_name, price, quantity, added_at)
                           VALUES (?, ?, ?, ?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("sisdi", $user_email, $product_id, $product['name'], $product['price'], $quantity);
            $insert_stmt->execute();
        }

        // Redirect back to shop
        header("Location: shop.php");
        exit();
    } else {
        echo "Product not found.";
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
