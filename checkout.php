<?php
session_start();
require_once('./db/database.php');

// Redirect to login if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Get product ID from query
if (!isset($_GET['product_id'])) {
    echo "Product ID is missing.";
    exit();
}

$product_id = intval($_GET['product_id']);

// Fetch product details from DB
$stmt = $conn->prepare("SELECT name, price, image, quantity FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->bind_result($product_name, $product_price, $product_image, $available_quantity);
if (!$stmt->fetch()) {
    echo "Product not found.";
    exit();
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout - QCU Urban Agriculture</title>
  <link rel="stylesheet" href="./css/style.css">
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

    .checkout-box {
      background: #fff;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      width: 400px;
      text-align: center;
    }

    .checkout-box h2 {
      margin-bottom: 20px;
    }

    .product-image {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 15px;
    }

    input[type="number"] {
      padding: 10px;
      width: 100%;
      font-size: 16px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      background: #4CAF50;
      color: white;
      border: none;
      padding: 12px;
      font-size: 16px;
      width: 100%;
      border-radius: 5px;
      cursor: pointer;
    }

    button:hover {
      background: #45a049;
    }
  </style>
</head>
<body>

<div class="checkout-box">
  <h2>Product Information</h2>
  <img src="<?php echo htmlspecialchars($product_image); ?>" class="product-image" alt="Product Image">

  <p>Name : <?php echo htmlspecialchars($product_name); ?><br></p>
  <p>Price: ₱<?php echo number_format($product_price, 2); ?></p>
  <p>Available: <?php echo $available_quantity; ?></p>

  <form method="POST" action="process_checkout.php">
    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>">
    <input type="hidden" name="product_price" value="<?php echo $product_price; ?>">
    <input type="hidden" name="available_quantity" value="<?php echo $available_quantity; ?>">
    <br><input 
  type="number" 
  id="quantity" 
  name="quantity" 
  placeholder="Enter quantity"
  min="1" 
  max="<?php echo $available_quantity; ?>" 
  required 
  oninput="validateQuantity(this)">

    <button type="submit" id="checkout-btn" disabled>Proceed to Payment</button>
  </form>
</div>

</body>
<script>
  function validateQuantity(input) {
    const btn = document.getElementById('checkout-btn');
    const value = input.value;

    // Valid if only digits and within range
    const isValid = /^\d+$/.test(value) && parseInt(value) >= 1 && parseInt(value) <= parseInt(input.max);

    btn.disabled = !isValid;
  }

  // Optional: run validation on page load in case browser autofills the value
  window.onload = function() {
    const input = document.getElementById('quantity');
    validateQuantity(input);
  };
</script>


</html>
