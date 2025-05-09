<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .success-box {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
    </style>
</head>
<body>
<div class="success-box">
    <h2>Order Successful!</h2>
    <p>Thank you for your purchase, <?php echo $_SESSION['email']; ?>.</p>
    <p>Your order has been placed and will be processed soon.</p>
    <a href="shop.php">Back to Shop</a>
</div>
</body>
</html>
