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

// Fetch products to display
$stmt = $conn->prepare("SELECT id, name, price, category, quantity, image FROM products");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $error = "No products found.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Products</title>
    <style>
        /* Your existing styles */
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

        .content {
            margin-left: 260px;
            padding: 40px;
            width: 100%;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        .button {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
        }

        .button:hover {
            background-color: #45a049;
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
            overflow: auto;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            margin: 15% auto;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

    </style>
</head>
<body>

    <div class="sidebar">
        <a href="admin_dashboard.php">Home</a>
        <a href="orders.php">Orders</a>
        <a href="add-product.php">Add Product</a>
        <a href="view-product.php">View Product</a>
        <a href="logout.php">Log Out</a>
    </div>

    <div class="content">
        <h2>View Products</h2>

        <?php if ($success): ?>
            <div class="message success"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= $product['name'] ?></td>
                        <td><?= $product['price'] ?></td>
                        <td><?= $product['category'] ?></td>
                        <td><?= $product['quantity'] ?></td>
                        <td>
                            <button class="button edit-btn" data-id="<?= $product['id'] ?>">Edit</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Product</h2>
            <form id="editForm" method="POST" enctype="multipart/form-data" action="update_product.php">
                <input type="hidden" name="id" id="productId">
                <label for="name">Product Name</label>
                <input type="text" name="name" id="name" required>

                <label for="price">Price</label>
                <input type="number" name="price" id="price" step="0.01" required>

                <label for="category">Category</label>
                <select name="category" id="category" required>
                    <option value="Category1">Category1</option>
                    <option value="Category2">Category2</option>
                    <option value="Category3">Category3</option>
                </select>

                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" id="quantity" required>

                <label for="image">Product Image</label>
                <input type="file" name="image" id="image">

                <button type="submit" class="button">Update Product</button>
            </form>
        </div>
    </div>

    <script>
        // Get modal element
        var modal = document.getElementById("editModal");

        // Get close button
        var closeBtn = document.getElementsByClassName("close")[0];

        // Get all edit buttons
        var editBtns = document.querySelectorAll(".edit-btn");

        // Open the modal and load product data
        editBtns.forEach(function(btn) {
            btn.addEventListener("click", function() {
                var productId = btn.getAttribute("data-id");

                // Use AJAX to fetch product details by ID
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "get_product.php?id=" + productId, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var product = JSON.parse(xhr.responseText);

                        // Populate the modal fields
                        document.getElementById("productId").value = product.id;
                        document.getElementById("name").value = product.name;
                        document.getElementById("price").value = product.price;
                        document.getElementById("category").value = product.category;
                        document.getElementById("quantity").value = product.quantity;
                    }
                };
                xhr.send();

                // Show the modal
                modal.style.display = "block";
            });
        });

        // Close the modal
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }

        // Close the modal if clicked outside of it
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>
</html>
