<?php 
session_start();
require_once('./db/database.php');

$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shop - QCU Urban Agriculture</title>
  <link rel="stylesheet" href="./css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    footer {
      background-color: #4CAF50;
      color: white;
      padding: 20px 0;
      text-align: center;
    }
    footer .social-icons {
      margin-top: 10px;
    }
    footer .social-icons a {
      color: white;
      margin: 0 10px;
      font-size: 24px;
      text-decoration: none;
    }
    footer .social-icons a:hover {
      color: #ddd;
    }

    .main-content { padding: 20px; }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 20px;
    }

    .product-card {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      text-align: center;
      background-color: #fff;
      position: relative;
    }

    .product-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 8px;
    }

    .product-card img.blurred {
      filter: blur(5px);
      opacity: 0.5;
    }

    .out-of-stock {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: green;
      color: white;
      padding: 10px;
      font-size: 18px;
      font-weight: bold;
      border-radius: 8px;
    }

    .product-name { font-size: 18px; margin-top: 10px; }
    .product-price { font-size: 16px; margin-top: 5px; }
    .product-quantity { font-size: 14px; color: #555; margin-top: 5px; }

    .buy-btn {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 10px;
    }
    .buy-btn:hover {
      background-color: #45a049;
    }

    .buy-btn:disabled {
      background-color: #ccc;
      cursor: not-allowed;
    }

    .cart-count {
      background: red;
      color: white;
      padding: 2px 6px;
      border-radius: 50%;
      font-size: 12px;
      vertical-align: top;
      margin-left: 5px;
    }
  </style>
</head>
<body>

<header>
  <nav class="navbar">
    <div class="nav-logo">
      <a href="index.php">
        <img src="image/qcuai logo.png" alt="logo">
      </a>
      <h2 class="logo-text">
        Quezon City University - <br> Center for Urban Agriculture and Innovation </br>
      </h2>
    </div>
    <ul class="nav-menu">
      <?php if (!isset($_SESSION['email'])): ?>
        <li class="nav-item"><a href="index.php" class="nav-link">Home</a></li>
        <li class="nav-item"><a href="crop.php" class="nav-link">Crops</a></li>
        <li class="nav-item"><a href="video.php" class="nav-link">Videos</a></li>
      <?php endif; ?>

      <li class="nav-item"><a href="shop.php" class="nav-link">Shop</a></li>

      <?php if (isset($_SESSION['email'])): ?>
        <li class="nav-item">
          <a href="cart.php" class="nav-link">
            <i id="cart-icon" class="fas fa-shopping-cart"></i>
            <?php if ($cart_count > 0): ?>
              <span id="cart-count" style="
                background:red;
                color:white;
                padding:2px 6px;
                border-radius:50%;
                font-size:12px;
                position:relative;
                top:-10px;
                left:-5px;
              "><?= $cart_count ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item"><a href="profile.php" class="nav-link">My Profile</a></li>
        <li class="nav-item">
          <a href="#" class="nav-link" onclick="confirmLogout(event)">Log out</a>
        </li>
      <?php else: ?>
        <li class="nav-item"><a href="login.php" class="nav-link">Log in</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>

<main>
  <div class="shop-container">
    <br><br><br><br>
    <h2>🌿 Our Fresh Produce</h2>
    <form method="GET" action="shop.php">
      <select name="category" onchange="this.form.submit()" style="width: 200px; background-color: green; color: white; border: none;">
        <option value="all">All Categories</option>
        <option value="vegetables" <?php if(isset($_GET['category']) && $_GET['category'] == 'vegetables') echo 'selected'; ?>>Vegetables</option>
        <option value="fruits" <?php if(isset($_GET['category']) && $_GET['category'] == 'fruits') echo 'selected'; ?>>Fruits</option>
        <option value="flowers" <?php if(isset($_GET['category']) && $_GET['category'] == 'flowers') echo 'selected'; ?>>Flowers</option>
        <option value="herbs" <?php if(isset($_GET['category']) && $_GET['category'] == 'herbs') echo 'selected'; ?>>Herbs</option>
      </select>
    </form>
<br><br><br>
    <div class="product-grid">
      <?php
      $is_logged_in = isset($_SESSION['email']);
      $category = isset($_GET['category']) ? $_GET['category'] : 'all';

      $sql = ($category == 'all') ? 
          "SELECT * FROM products" : 
          "SELECT * FROM products WHERE category = ?";

      $stmt = $conn->prepare($sql);
      if ($category != 'all') $stmt->bind_param("s", $category);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows == 0) {
        echo '<div class="product-card">';
        echo '<img src="./shop/noproduct.png" alt="No products">';
        echo '<div class="product-name">There are currently no ' . ucfirst($category) . '</div>';
        echo '</div>';
      } else {
        while ($row = $result->fetch_assoc()) {
          $is_out_of_stock = intval($row['quantity']) == 0;
          echo '
          <div class="product-card">
            <img src="' . $row['image'] . '" alt="' . htmlspecialchars($row['name']) . '" class="' . ($is_out_of_stock ? 'blurred' : '') . '">
            ' . ($is_out_of_stock ? '<div class="out-of-stock">Out of Stock</div>' : '') . '
            <div class="product-name">' . htmlspecialchars($row['name']) . '</div>
            <div class="product-price">₱' . htmlspecialchars($row['price']) . '</div>
            <div class="product-quantity">Available: ' . intval($row['quantity']) . '</div>';
          
          if ($is_logged_in) {
            if (!$is_out_of_stock) {
              echo '
                <a href="checkout.php?product_id=' . $row['id'] . '&name=' . urlencode($row['name']) . '&price=' . $row['price'] . '&quantity=' . $row['quantity'] . '">
                  <button class="buy-btn">Buy</button>
                </a>
                <form method="POST" action="add_to_cart.php" style="margin-top: 5px;">
                  <input type="hidden" name="product_id" value="' . $row['id'] . '">
                  <input type="hidden" name="quantity" value="1">
                  <button type="submit" class="buy-btn" style="background-color:#2196F3;">Add to Cart</button>
                </form>';
            } else {
              echo '<button class="buy-btn" style="background-color: #ccc;" disabled>Out of Stock</button>';
            }
          } else {
            echo '<a href="login.php"><button class="buy-btn">Login to Buy</button></a>';
          }

          echo '</div>';
        }        
      }

      $stmt->close();
      $conn->close();
      ?>
    </div>

    <div class="payment">
      <h3>💰 Mode of Payment</h3>
      <ul>
        <li>Cash on Delivery (COD)</li>
        <li>Cash on Site</li>
      </ul>
    </div>

    <div class="milestones">
      <h3>🏆 Milestones & Partnerships</h3>
      <ul>
        <li>Giant Patani initiative</li>
        <li>Strawberry cultivation program</li>
        <li><em>Sagip Osy</em> community support</li>
      </ul>
    </div>
  </div>
</main>

<footer>
  <div>
    <p>&copy; 2025 QCU - Center for Urban Agriculture and Innovation</p>
    <div class="social-icons">
      <a href="#"><i class="fab fa-facebook fa-lg"></i></a>
      <a href="#"><i class="fab fa-instagram fa-lg"></i></a>
      <a href="#"><i class="fab fa-twitter fa-lg"></i></a>
      <a href="#"><i class="fab fa-youtube fa-lg"></i></a>
    </div>
  </div>
</footer>

<script>
  function confirmLogout(event) {
    event.preventDefault();
    Swal.fire({
      title: 'Are you sure you want to log out?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#4CAF50',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, log out'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'logout.php';
      }
    });
  }
</script>

</body>
</html>
