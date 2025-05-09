<?php
session_start();
require_once('./db/database.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/PHPMailer.php';
require './PHPMailer/SMTP.php';
require './PHPMailer/Exception.php';

// Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Validate POST data
if (!isset($_POST['user_id'], $_POST['product_id'], $_POST['quantity'], $_POST['total_price'], $_POST['payment_method'])) {
    echo "Missing order information.";
    exit();
}

$user_id = intval($_POST['user_id']);
$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);
$total_price = floatval($_POST['total_price']);
$payment_method = $_POST['payment_method'];
$message = isset($_POST['message']) ? $_POST['message'] : "";

// Get user email
$user_email = $_SESSION['email'];

// Fetch user details
$stmt = $conn->prepare("SELECT first_name, last_name FROM accounts_tbl WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name);
$stmt->fetch();
$stmt->close();

// Fetch product details (to ensure valid and updated data)
$stmt = $conn->prepare("SELECT name, quantity FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->bind_result($product_name, $available_quantity);
$stmt->fetch();
$stmt->close();

// Check for stock availability
if ($available_quantity < $quantity) {
    echo "Not enough stock available.";
    exit();
}

// Insert order
$stmt = $conn->prepare("INSERT INTO orders_tbl (user_id, product_id, product_name, quantity, total_price, payment_method, message, order_date)
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("iisidss", $user_id, $product_id, $product_name, $quantity, $total_price, $payment_method, $message);
$stmt->execute();

$order_id = $stmt->insert_id; // get inserted order ID
$stmt->close();

// Update product stock
$new_quantity = $available_quantity - $quantity;
$stmt = $conn->prepare("UPDATE products SET quantity = ? WHERE id = ?");
$stmt->bind_param("ii", $new_quantity, $product_id);
$stmt->execute();
$stmt->close();

// Generate cancel link
$cancel_link = "http://localhost/IPT/cancel_order.php?order_id=$order_id";

// Send email using PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'contactusforrecovery@gmail.com';
    $mail->Password = 'purq hvhp pyda ifgn'; // Gmail app password
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('contactusforrecovery@gmail.com', 'Quezon City University -
Center for Urban Agriculture and Innovation');
    $mail->addAddress($user_email); // Use user email
    $mail->Subject = 'Your Order Confirmation';

    // Email content
    $mail->isHTML(true);
    $mail->Body = "
        <h3>Hi $first_name,</h3>
        <p>Thank you for your order of <strong>$quantity x $product_name</strong> (₱" . number_format($total_price, 2) . ").</p>
        <p><strong>Payment Method:</strong> $payment_method</p>
        <p>If you wish to cancel this order, click the link below:</p>
        <a href='$cancel_link'>$cancel_link</a>
        <br><br>
        <p>Regards,<br>QCU Urban Agriculture Team</p>
    ";

    $mail->send();
} catch (Exception $e) {
    // Optional: log or notify admin
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

// Redirect to success page
header("Location: success.php");
exit();
?>
