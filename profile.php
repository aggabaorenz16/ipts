<?php
session_start();
require_once('./db/database.php');

if (!isset($_SESSION['email'])) {
  header('Location: login.php');
  exit();
}

$email = $_SESSION['email'];

// Fetch user info
$user_sql = "SELECT * FROM accounts_tbl WHERE email = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("s", $email);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

// Get user_id
$user_id = $user['id'];  // Assuming the user's ID is stored in the 'id' column of accounts_tbl

// Fetch user purchases directly from orders_tbl
$purchase_sql = "SELECT product_name, quantity, total_price, order_date 
                 FROM orders_tbl 
                 WHERE user_id = ? 
                 ORDER BY order_date DESC";
$purchase_stmt = $conn->prepare($purchase_sql);
$purchase_stmt->bind_param("i", $user_id);  // Bind user_id as an integer
$purchase_stmt->execute();
$purchase_result = $purchase_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile - QCU Urban Agriculture</title>
  <link rel="stylesheet" href="./css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    footer {
      background-color: #4CAF50;
      color: white;
      padding: 1rem;
      text-align: center;
      height: 90px;
    }

    footer .social-icons a {
      color: white;
      font-size: 24px;
      text-decoration: none;
    }

    footer .social-icons a:hover {
      color: #ddd;
    }

    .profile-container {
      max-width: 800px;
      margin: auto;
      padding: 1rem;
    }

    .user-info {
      background-color: #f5f5f5;
      padding: 20px;
      border-radius: 10px;
    }

    .user-info p {
      display: inline-block;
      margin-right: 15px;
    }

    .user-info .edit {
      cursor: pointer;
      color: #007bff;
      font-size: 16px;
      margin-left: 10px;
    }

    .user-info .edit:hover {
      color: #0056b3;
    }

    .user-info input {
      padding: 5px;
      font-size: 14px;
      border: 1px solid #ddd;
      border-radius: 5px;
      display: none;
    }

    .purchase-history {
      margin-top: 30px;
    }

    .purchase-history table {
      width: 100%;
      border-collapse: collapse;
    }

    .purchase-history th, .purchase-history td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
    }

    .purchase-history th {
      background-color: #4CAF50;
      color: white;
    }

    .no-purchases {
      text-align: center;
      margin-top: 20px;
      color: #888;
    }

    .delete-btn {
      margin-top: 20px;
      background-color: #dc3545;
      color: white;
      padding: 10px;
      border: none;
      cursor: pointer;
      border-radius: 5px;
    }

    .delete-btn:hover {
      background-color: #c82333;
    }

    #save-changes {
      background-color: #28a745;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 5px;
      cursor: pointer;
      display: none;
    }

    #save-changes:hover {
      background-color: #218838;
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
      <li class="nav-item"><a href="shop.php" class="nav-link">Shop</a></li>
      <li class="nav-item"><a href="profile.php" class="nav-link">Profile</a></li>
      <?php if (isset($_SESSION['email'])): ?>
        <li class="nav-item"><a href="logout.php" class="nav-link">Log out</a></li>
      <?php else: ?>
        <li class="nav-item"><a href="login.php" class="nav-link">Log in</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>

<div class="profile-container">
  <h2>👤 My Profile</h2>
  <div class="user-info">
    <p><strong>Name:</strong> 
        <span id="user-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></span>
        <input type="text" id="edit-name" value="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>" />
        <span class="edit" onclick="editInfo('name')">✏️</span>
    </p>
    <p><strong>Address:</strong> 
        <span id="user-address"><?= htmlspecialchars($user['address']) ?></span>
        <input type="text" id="edit-address" value="<?= htmlspecialchars($user['address']) ?>" />
        <span class="edit" onclick="editInfo('address')">✏️</span>
    </p>
    <p><strong>Email:</strong> 
        <span id="user-email"><?= htmlspecialchars($user['email']) ?></span>
        <input type="text" id="edit-email" value="<?= htmlspecialchars($user['email']) ?>" />
        <span class="edit" onclick="editInfo('email')">✏️</span>
    </p>
    <button id="save-changes" onclick="saveChanges()">Save Changes</button>
  </div>

  <div class="purchase-history">
    <h3>🛒 Purchase History</h3>
    <?php if ($purchase_result->num_rows > 0): ?>
      <table>
        <tr>
          <th>Product</th>
          <th>Quantity</th>
          <th>Total Price</th>
          <th>Date</th>
        </tr>
        <?php while ($row = $purchase_result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td><?= intval($row['quantity']) ?></td>
            <td>₱<?= number_format($row['total_price'], 2) ?></td>
            <td><?= htmlspecialchars(date("F j, Y", strtotime($row['order_date']))) ?></td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p class="no-purchases">You haven't made any purchases yet.</p>
    <?php endif; ?>
  </div>

  <!-- Delete Account Button -->
  <button class="delete-btn" onclick="confirmDelete()">Delete Account</button>
</div>

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
  // Function to confirm account deletion with SweetAlert
  function confirmDelete() {
    Swal.fire({
      title: 'Are you sure?',
      text: "Once deleted, you will not be able to recover your account!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'Cancel',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        // Redirect to delete account PHP script (handle server-side deletion)
        window.location.href = 'delete_account.php'; 
      }
    });
  }

  // Function to show input fields for editing
  function editInfo(field) {
    // Hide the span text and show the input field for that field
    document.getElementById('user-' + field).style.display = 'none';
    document.getElementById('edit-' + field).style.display = 'inline';
    document.getElementById('save-changes').style.display = 'inline'; // Show Save button
  }

  // Function to save the changes to the server
  function saveChanges() {
    // Get the values from the input fields
    var name = document.getElementById('edit-name').value;
    var address = document.getElementById('edit-address').value;
    var email = document.getElementById('edit-email').value;

    // Use AJAX to send the updated data to a PHP script without reloading the page
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_profile.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Update the user interface with the new values
            document.getElementById('user-name').innerText = name;
            document.getElementById('user-address').innerText = address;
            document.getElementById('user-email').innerText = email;

            // Hide the input fields again
            document.getElementById('edit-name').style.display = 'none';
            document.getElementById('edit-address').style.display = 'none';
            document.getElementById('edit-email').style.display = 'none';
            document.getElementById('save-changes').style.display = 'none'; // Hide Save button
        }
    };
    xhr.send('name=' + encodeURIComponent(name) + '&address=' + encodeURIComponent(address) + '&email=' + encodeURIComponent(email));
  }
</script>

</body>
</html>
