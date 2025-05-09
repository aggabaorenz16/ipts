<?php
session_start();
require_once('./db/database.php');

// Optional: Restrict access to admins only
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$success = "";
$error = "";

// Handle product update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $edit_id = (int)$_POST['edit_id'];
    $name = $_POST['name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];
    $image = $_POST['image'];

    $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, quantity = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sssdi", $name, $category, $quantity, $image, $edit_id);

    if ($stmt->execute()) {
        $success = "Product updated successfully!";
    } else {
        $error = "Failed to update product.";
    }

    $stmt->close();
}

// Handle product delete
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    // Check if product is referenced in orders_tbl
    $check = $conn->prepare("SELECT COUNT(*) as count FROM orders_tbl WHERE product_id = ?");
    $check->bind_param("i", $delete_id);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();
    $check->close();

    if ($count > 0) {
        $error = "Cannot delete product. It is still referenced in existing orders.";
    } else {
        // Set any references in cancelled_orders to NULL (optional safety)
        $stmt = $conn->prepare("UPDATE cancelled_orders SET order_id = NULL WHERE order_id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();

        // Now safely delete the product
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $delete_id);

        if ($stmt->execute()) {
            $success = "Product deleted successfully!";
        } else {
            $error = "Failed to delete product.";
        }

        $stmt->close();
    }
}

// Fetch products
$sql = "SELECT id, name, image, category, quantity FROM products";
$result = $conn->query($sql);
$conn->close();
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.js"></script>
        <!-- FontAwesome CDN for icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <meta charset="UTF-8">
        <title>Admin Dashboard - View Products</title>
        <style>
            /* Your existing styles here */
            #logoutLink {
                background-color: red;
                border: none;
            }
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
            }

            .container {
                width: 100%;
            }

            h2 {
                text-align: center;
                color: green;
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

            .action-buttons button, .action-buttons a {
                display: inline-flex;
                align-items: center;
                color: white;
                padding: 8px 15px;
                font-size: 16px;
                cursor: pointer;
                border: none;
                border-radius: 5px;
                transition: all 0.3s ease;
                text-decoration: none;
            }

            .edit-button {
                background-color: #4CAF50;
            }

            .edit-button:hover {
                background-color: #45a049;
            }

            .edit-button:active {
                background-color: #388e3c;
            }

            .delete-button {
                background-color: #d9534f;
                color: white;
            }

            .delete-button:hover {
                background-color: #c9302c;
            }

            .delete-button:active {
                background-color: #ac2925;
            }

            .action-buttons i {
                margin-right: 5px;
            }

            .action-buttons button:hover,
            .action-buttons a:hover {
                transform: scale(1.05);
            }

            .action-buttons button:active,
            .action-buttons a:active {
                transform: scale(1);
            }

            .action-buttons button, .action-buttons a {
                margin-right: 10px;
            }

            .message {
                text-align: center;
                padding: 10px;
                margin-bottom: 15px;
            }

            .success {
                background-color: #d4edda;
                color: #155724;
            }

            .error {
                background-color: #f8d7da;
                color: #721c24;
            }

            .modal {
                display: none;
                position: fixed;
                z-index: 1;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.4);
                padding-top: 60px;
            }

            .modal-content {
                background-color: #fefefe;
                margin: 5% auto;
                padding: 20px;
                width: 50%;
                border: 1px solid #888;
                border-radius: 8px;
            }

            .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }

            .close:hover, .close:focus {
                color: black;
                cursor: pointer;
            }

            input[type="text"], input[type="number"], input[type="submit"] {
                width: 100%;
                padding: 10px;
                margin: 10px 0;
                border: 1px solid #ccc;
                border-radius: 4px;
                font-size: 16px;
            }

            input[type="submit"] {
                background-color: #4CAF50;
                color: white;
                cursor: pointer;
            }

            input[type="submit"]:hover {
                background-color: #45a049;
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
            <a href="logout.php" id="logoutLink"><i class="fas fa-sign-out-alt"></i>Log Out</a>
        </div>

        <div class="content">
            <h2>Manage Products</h2>
            
            <?php if ($success): ?>
                <div class="message success"><?= $success ?></div>
            <?php elseif ($error): ?>
                <div class="message error"><?= $error ?></div>
            <?php endif; ?>

            <!-- Product Table -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['name'] ?></td>
                                <td><img src="<?= $row['image'] ?>" alt="<?= $row['name'] ?>" width="50"></td>
                                <td><?= $row['category'] ?></td>
                                <td><?= $row['quantity'] ?></td>
                                <td class="action-buttons">
                                    <!-- Edit Button -->
                                    <button onclick="openModal(<?= $row['id'] ?>, '<?= $row['name'] ?>', '<?= $row['category'] ?>', '<?= $row['quantity'] ?>', '<?= $row['image'] ?>')" class="action-buttons edit-button">
                                        <i class="fas fa-pencil-alt"></i> Edit
                                    </button>

                                    <!-- Delete Button -->
                                    <a href="view-product.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?')" class="action-buttons delete-button">
                                        <i class="fas fa-times"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>

        <!-- Modal for Editing Product -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Edit Product</h2>
                <form method="POST">
                    <input type="hidden" name="edit_id" id="edit_id">
                    <input type="text" name="name" id="name" required placeholder="Product Name">
                    <input type="text" name="category" id="category" required placeholder="Category">
                    <input type="number" name="quantity" id="quantity" required placeholder="Quantity">
                    <input type="text" name="image" id="image" required placeholder="Image URL">
                    <input type="submit" value="Update Product">
                </form>
            </div>
        </div>

        <script>
            // Open the modal to edit product
            function openModal(id, name, category, quantity, image) {
                document.getElementById("editModal").style.display = "block";
                document.getElementById("edit_id").value = id;
                document.getElementById("name").value = name;
                document.getElementById("category").value = category;
                document.getElementById("quantity").value = quantity;
                document.getElementById("image").value = image;
            }

            // Close the modal
            function closeModal() {
                document.getElementById("editModal").style.display = "none";
            }
        </script>
    </body>
    </html>
