<?php
session_start();
require_once('./db/database.php');

// Redirect if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Check if necessary data is provided
if (!isset($_POST['product_id'], $_POST['quantity'])) {
    echo "Missing data.";
    exit();
}

$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);
$user_email = $_SESSION['email'];

// Fetch user details
$stmt = $conn->prepare("SELECT id, first_name, middle_name, last_name, address FROM accounts_tbl WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($user_id, $first_name, $middle_name, $last_name, $address);
$stmt->fetch();
$stmt->close();

// Fetch product details
$stmt = $conn->prepare("SELECT name, price, image FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->bind_result($product_name, $product_price, $product_image);
$stmt->fetch();
$stmt->close();

$total_price = $product_price * $quantity;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details - QCU Urban Agriculture</title>
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

        .order-box {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 500px;
        }

        .order-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .order-details p {
            margin: 8px 0;
        }

        .order-details input[type="text"], textarea, select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .order-details input[readonly] {
            background-color: #f0f0f0;
        }

        .submit-btn {
            background: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background: #45a049;
        }
    </style>
</head>
<body>

<div class="order-box">
    <h2>Order Details</h2>
    <form method="POST" action="confirm_order.php">
        <!-- Hidden product & user info -->
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
        <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">

        <div class="order-details">
            <p><strong>Name:</strong> <?php echo "$first_name $middle_name $last_name"; ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
            <p><strong>Product:</strong> <?php echo htmlspecialchars($product_name); ?></p>
            <p><strong>Quantity:</strong> <?php echo $quantity; ?></p>
            <p><strong>Price per unit:</strong> ₱<?php echo number_format($product_price, 2); ?></p>
            <p><strong>Total Price:</strong> ₱<?php echo number_format($total_price, 2); ?></p>

            <label for="message">Message (optional):</label>
            <textarea name="message" id="message" rows="3" placeholder="Additional instructions"></textarea>

            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" id="payment_method" required>
                <option value="">Select a payment method</option>
                <option value="Cash on Delivery (COD)">Cash on Delivery (COD)</option>
                <option value="Cash on Site">Cash on Site</option>
            </select>

            <button type="submit" class="submit-btn">Confirm Order</button>
        </div>
    </form>
</div>

</body>
</html>
