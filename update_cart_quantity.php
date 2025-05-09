<?php
session_start();
require_once('./db/database.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cart_id = $_POST['cart_id'];
    $action = $_POST['action'];

    // Get the current quantity of the cart item
    $sql = "SELECT quantity FROM cart_tbl WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $current_quantity = $row['quantity'];

    // Modify the quantity based on the action
    if ($action == 'increase') {
        $new_quantity = $current_quantity + 1;
    } elseif ($action == 'decrease' && $current_quantity > 1) {
        $new_quantity = $current_quantity - 1;
    } else {
        // Prevent quantity from going below 1
        $new_quantity = $current_quantity;
    }

    // Update the cart with the new quantity
    $sql_update = "UPDATE cart_tbl SET quantity = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ii", $new_quantity, $cart_id);
    $stmt_update->execute();

    // Redirect back to the cart page
    header("Location: cart.php");
    exit();
}

$stmt->close();
$conn->close();
?>
