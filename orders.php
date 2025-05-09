<?php 
session_start();
require_once('./db/database.php');

// Optional: Restrict access to admins only
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Handle Delete action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM orders_tbl WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        header("Location: orders.php");
        exit;
    } else {
        echo "Error deleting order.";
    }
}

// Fetch orders joined with account names
$query = "SELECT 
            o.id, 
            a.first_name, a.middle_name, a.last_name, 
            a.address AS user_address,
            o.address AS order_address,
            o.message, o.payment_method, o.quantity,
            o.total_price, o.product_name, o.product_id, o.order_date
          FROM orders_tbl o
          JOIN accounts_tbl a ON o.user_id = a.id";

$result = $conn->query($query); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
         #logoutLink {
            background-color: red;
            border: none;
        }
        body {
            font-family: Arial, sans-serif;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #4CAF50;
            color: white;
            padding-top: 20px;
            position: fixed;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 15px;
            font-size: 18px;
            border-bottom: 1px solid #fff;
        }

        .sidebar a:hover {
            background-color: #45a049;
        }

        .sidebar i {
            margin-right: 10px;
        }

        .content {
            margin-left: 260px;
            padding: 40px;
            width: 100%;
            /* background-color: #f4f4f4; */
        }

        .container {
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #4CAF50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 14px;
            text-align: center;
        }

        .edit-btn {
            background-color: #007BFF;
        }

        .delete-btn {
            background-color: #DC3545;
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
<div class="sidebar">
        <a href="admin_dashboard.php"><i class="fas fa-home"></i>Home</a>
        <a href="orders.php"><i class="fas fa-box"></i>Orders</a>
        <a href="add-product.php"><i class="fas fa-plus-circle"></i>Add Product</a>
        <a href="add-crops.php"><i class="fas fa-plus-circle"></i>Add Crops</a>
        <a href="view-product.php"><i class="fas fa-eye"></i>View Product</a>
        <a href="view-crops.php"><i class="fas fa-eye"></i>View Crops</a>
        <a href="#" id="logoutLink"><i class="fas fa-sign-out-alt"></i>Log Out</a>
    </div>
    <div class="content">
        <div class="container">
            <h2>Manage Orders</h2>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th>
                            <th>User Address</th>
                            <th>Order Address</th>
                            <th>Message</th>
                            <th>Payment Method</th>
                            <th>Quantity</th>
                            <th>Product Name</th>
                            <th>Total Price</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['middle_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['user_address']); ?></td>
                            <td><?php echo htmlspecialchars($row['order_address']); ?></td>
                            <td><?php echo htmlspecialchars($row['message']); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['total_price']); ?></td>
                            <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                            <td class="action-buttons">
                                <a href="edit-order.php?id=<?php echo $row['id']; ?>" class="btn edit-btn">Edit</a>
                                <a href="?delete_id=<?php echo $row['id']; ?>" class="btn delete-btn" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No orders found.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById('logoutLink').addEventListener('click', function(e) {
            e.preventDefault();  // Prevent the default link action

            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to log out?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, log out!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';  // Redirect to logout page
                }
            });
        });
    </script>
</body>
</html>
