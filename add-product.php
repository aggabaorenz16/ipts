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

// Handle form submission from add-product.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $quantity = (int)$_POST['quantity'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $file_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed)) {
            if ($_FILES['image']['size'] < 5000000) { // File size limit 5MB
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    // Insert into database
                    $stmt = $conn->prepare("INSERT INTO products (name, price, image, category, quantity) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssi", $name, $price, $target_file, $category, $quantity);

                    if ($stmt->execute()) {
                        $success = "Product added successfully!";
                    } else {
                        $error = "Database insert failed.";
                    }

                    $stmt->close();
                } else {
                    $error = "Failed to upload image.";
                }
            } else {
                $error = "Image file is too large. Maximum size is 5MB.";
            }
        } else {
            $error = "Invalid image file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        }
    } else {
        $error = "Image upload error.";
    }
}

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
    <title>Admin Dashboard - Upload Product</title>
    <style>
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
            padding: 30px;
            max-width: 500px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }

        h2 {
            text-align: center;
            color: #4CAF50;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input, select {
            margin-bottom: 15px;
            padding: 10px;
            font-size: 16px;
        }

        button {
            background: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            font-size: 16px;
            cursor: pointer;
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
    <br>
    <br>
    <br>

    <!-- Success and Error Messages -->
    <?php if ($success): ?>
        <div class="message success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <!-- Product Upload Form -->
    <div class="container">
        <h2>Add New Product</h2>

        <form action="add-product.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Product Name" required>
            <input type="text" name="price" placeholder="Price (e.g., 100 per kilo)" required>
            <select name="category" required>
                <option value="">-- Select Category --</option>
                <option value="vegetables">Vegetables</option>
                <option value="fruits">Fruits</option>
                <option value="flowers">Flowers</option>
                <option value="herbs">Herbs</option>
            </select>
            <input type="number" name="quantity" placeholder="Quantity Available" required min="1">
            <input type="file" name="image" accept="image/*" required>
            <button type="submit">Upload Product</button>
        </form>
    </div>
</div>
</body>
</html>
