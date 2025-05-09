<?php
session_start();
require_once('./db/database.php');

// Restrict access to admins only
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'];

// Fetch first name from accounts_tbl
$first_name = '';
$sql = "SELECT first_name FROM accounts_tbl WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($first_name);
$stmt->fetch();
$stmt->close();

// Fetch total orders and cancelled orders count
$sql_orders = "SELECT COUNT(*) FROM orders_tbl";
$sql_cancelled = "SELECT COUNT(*) FROM cancelled_orders";

$stmt_orders = $conn->prepare($sql_orders);
$stmt_orders->execute();
$stmt_orders->bind_result($total_orders);
$stmt_orders->fetch();
$stmt_orders->close();

$stmt_cancelled = $conn->prepare($sql_cancelled);
$stmt_cancelled->execute();
$stmt_cancelled->bind_result($total_cancelled);
$stmt_cancelled->fetch();
$stmt_cancelled->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Include Chart.js -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
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
            overflow: auto; /* Adds a scrollbar only when content overflows */
        }

        .container {
            padding: 30px;
            margin: auto;
            border-radius: 10px;
        }

        .greeting {
            font-size: 20px;
            margin-bottom: 20px;
        }

        .dashboard-links {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .dashboard-links a {
            flex: 1 1 200px;
            text-align: center;
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-decoration: none;
            font-size: 18px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: background-color 0.3s;
        }

        .dashboard-links a:hover {
            background-color: #45a049;
        }

        #logoutLink {
            background-color: red;
            border: none;
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
        <div class="greeting">👋 Welcome Back, <?php echo htmlspecialchars($first_name); ?>!</div>
        <div class="dashboard-links">
            <a href="admin_dashboard.php"><i class="fas fa-home"></i><br>Home</a>
            <a href="orders.php"><i class="fas fa-box"></i><br>Orders</a>
            <a href="add-product.php"><i class="fas fa-plus-circle"></i><br>Add Product</a>
            <a href="add-crops.php"><i class="fas fa-plus-circle"></i><br>Add Crops</a>
        </div>

        <!-- Display Order Stats -->
        <h3>Order and Cancelled Statistics</h3>
        <canvas id="orderChart"></canvas>

        <script>
            // Data for the chart (from PHP)
            var totalOrders = <?php echo $total_orders; ?>;
            var totalCancelled = <?php echo $total_cancelled; ?>;

            // Create the chart
            var ctx = document.getElementById('orderChart').getContext('2d');
            var orderChart = new Chart(ctx, {
                type: 'bar',  // Bar chart
                data: {
                    labels: ['Total Orders', 'Cancelled Orders'],  // X-axis labels
                    datasets: [{
                        label: 'Number of Orders',
                        data: [totalOrders, totalCancelled],  // Data points
                        backgroundColor: ['#4CAF50', '#FF6347'],  // Green for orders, Red for cancelled
                        borderColor: ['#388E3C', '#FF4500'],  // Border color
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            enabled: true,
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                        }
                    }
                }
            });
        </script>
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
