<?php
session_start();
require_once('./db/database.php');

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

$email = $_SESSION['email'];

// Fetch the user_id based on the email
$user_sql = "SELECT id FROM accounts_tbl WHERE email = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("s", $email);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows == 0) {
    // User not found, redirect to homepage or login
    header('Location: index.php');
    exit();
}

$user = $user_result->fetch_assoc();
$user_id = $user['id'];

// Begin a transaction to ensure data consistency
$conn->begin_transaction();

try {
    // Delete user-related records from orders_tbl (if applicable)
    $delete_orders_sql = "DELETE FROM orders_tbl WHERE user_id = ?";
    $delete_orders_stmt = $conn->prepare($delete_orders_sql);
    $delete_orders_stmt->bind_param("i", $user_id);
    $delete_orders_stmt->execute();

    // Delete the user's account from accounts_tbl
    $delete_user_sql = "DELETE FROM accounts_tbl WHERE id = ?";
    $delete_user_stmt = $conn->prepare($delete_user_sql);
    $delete_user_stmt->bind_param("i", $user_id);
    $delete_user_stmt->execute();

    // Commit the transaction
    $conn->commit();

    // Destroy session and redirect user
    session_destroy();
    header('Location: index.php?message=account_deleted');
    exit();
} catch (Exception $e) {
    // Rollback the transaction in case of error
    $conn->rollback();
    // Redirect to an error page or show a message
    header('Location: error.php?message=error_deleting_account');
    exit();
}

// Close prepared statements
$user_stmt->close();
$delete_orders_stmt->close();
$delete_user_stmt->close();

// Close database connection
$conn->close();
?>
