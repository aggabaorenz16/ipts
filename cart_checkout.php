<?php
session_start();
require_once('./db/database.php');

// Redirect to login if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Check if the cart has items
if (!isset($_POST['items']) || !is_array($_POST['items'])) {
    echo "No items to checkout.";
    exit();
}

$email = $_SESSION['email'];

// Get user ID and address from accounts_tbl based on email
$stmt = $conn->prepare("SELECT id, address FROM accounts_tbl WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id, $address);
$stmt->fetch();
$stmt->close();

// If the address is not found, display an error
if (empty($address)) {
    echo "No address found for this user.";
    exit();
}

foreach ($_POST['items'] as $item) {
    $product_id = intval($item['product_id']);
    $product_name = $item['product_name'];
    $product_price = floatval($item['product_price']);
    $quantity = intval($item['quantity']);
    $total_price = $product_price * $quantity;
    $payment_method = 'Cash on Delivery';  // Default
    $message = '';                        // Optional message
    $order_date = date('Y-m-d H:i:s');

    // Insert order into orders_tbl
    $insert = $conn->prepare("INSERT INTO orders_tbl (user_id, product_id, product_name, quantity, total_price, payment_method, address, message, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insert->bind_param("iisidssss", $user_id, $product_id, $product_name, $quantity, $total_price, $payment_method, $address, $message, $order_date);
    $insert->execute();
    $insert->close();

    // Optionally, remove item from cart after checkout
    $delete = $conn->prepare("DELETE FROM cart_tbl WHERE product_id = ? AND user_email = ?");
    $delete->bind_param("is", $product_id, $email);
    $delete->execute();
    $delete->close();
}

// Redirect user to a confirmation page
echo "<script>alert('Checkout completed!'); window.location.href='shop.php';</script>";
$conn->close();
?>
