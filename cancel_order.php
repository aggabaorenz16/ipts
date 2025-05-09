<?php
require_once('./db/database.php');

if (!isset($_GET['order_id'])) {
    echo "Invalid request.";
    exit();
}

$order_id = intval($_GET['order_id']);

// Fetch the order to cancel
$stmt = $conn->prepare("SELECT * FROM orders_tbl WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Order not found.";
    exit();
}

$order = $result->fetch_assoc();
$stmt->close();

// Insert into cancelled_orders table using your schema
$stmt = $conn->prepare("INSERT INTO `cancelled_orders`(
    `order_id`,
    `customer_name`,
    `cancel_date`,
    `reason`
) 
                        VALUES (?, ?, ?, ?)");
$default_reason = "Cancelled by user via email link";

// Update bind parameters to match the column types
$stmt->bind_param("isss", 
    $order['id'],              // Using order's ID
    $order['user_id'],         // Assuming `user_id` is the customer name or a reference to it
    date('Y-m-d H:i:s'),      // Using current date and time for cancel_date
    $default_reason           // Reason for cancellation
);

$stmt->execute();
$stmt->close();

// Delete from orders_tbl
$stmt = $conn->prepare("DELETE FROM orders_tbl WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$stmt->close();

echo "<h2>Your order has been successfully cancelled.</h2>";
?>
