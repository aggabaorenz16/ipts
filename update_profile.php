<?php
session_start();
require_once('./db/database.php');

if (!isset($_SESSION['email'])) {
    echo "You must be logged in to update your profile.";
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_POST['name'];
$address = $_POST['address'];
$email = $_POST['email'];

// Update the user profile in the database
$update_sql = "UPDATE accounts_tbl SET first_name = ?, last_name = ?, address = ?, email = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$name_parts = explode(' ', $name);
$first_name = $name_parts[0];
$last_name = isset($name_parts[1]) ? $name_parts[1] : ''; // Handle single name input

$update_stmt->bind_param('ssssi', $first_name, $last_name, $address, $email, $user_id);
if ($update_stmt->execute()) {
    echo "Profile updated successfully!";
} else {
    echo "Error updating profile.";
}

$update_stmt->close();
$conn->close();
?>
