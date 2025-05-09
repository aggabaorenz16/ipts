<?php
session_start();
require_once('./db/database.php');

if (isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];

    $stmt = $conn->prepare("DELETE FROM cart_tbl WHERE id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
}

header("Location: cart.php");
exit();
?>
