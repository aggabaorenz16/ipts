<?php
session_start();
require_once('./db/database.php');

// Redirect to login if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'], $_POST['product_name'], $_POST['product_price'], $_POST['quantity'])) {
    // Get product details from the POST request
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $quantity = $_POST['quantity'];
    $total_price = $product_price * $quantity;

    // Get user ID and address from accounts_tbl
    $stmt = $conn->prepare("SELECT id, address FROM accounts_tbl WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->bind_result($user_id, $address);
    $stmt->fetch();
    $stmt->close();

    // If the address is not found, display an error
    if (empty($address)) {
        echo "No address found for this user.";
        exit();
    }

    // Insert order into orders_tbl
    $order_date = date('Y-m-d H:i:s');
    $insert = $conn->prepare("INSERT INTO orders_tbl (user_id, product_id, product_name, quantity, total_price, payment_method, address, order_date) VALUES (?, ?, ?, ?, ?, 'Cash on Delivery', ?, ?)");
    $insert->bind_param("iisidss", $user_id, $product_id, $product_name, $quantity, $total_price, $address, $order_date);
    $insert->execute();
    $insert->close();

    // Decrease the quantity of the ordered product in the products table
    $update_product = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
    $update_product->bind_param("ii", $quantity, $product_id);
    $update_product->execute();
    $update_product->close();

    // Optionally, remove item from cart after checkout
    $delete = $conn->prepare("DELETE FROM cart_tbl WHERE product_id = ? AND user_email = ?");
    $delete->bind_param("is", $product_id, $user_email);
    $delete->execute();
    $delete->close();

    // Redirect to a confirmation page or show a success message
    echo "<script>alert('Item checkout completed!'); window.location.href='cart.php';</script>";
} else {
    echo "Invalid request.";
}

$conn->close();
?>
