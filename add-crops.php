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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $description = $_POST['description'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/"; // Ensure you have a folder called 'uploads' with write permissions
        $file_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Insert into the database
                $stmt = $conn->prepare("INSERT INTO crops (name, description, image) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $description, $target_file);

                if ($stmt->execute()) {
                    $success = "Crop added successfully!";
                } else {
                    $error = "Database insert failed.";
                }

                $stmt->close();
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid image file type.";
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
    <meta charset="UTF-8">
    <title>Admin Dashboard - Upload Product</title>

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.js"></script>

    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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
          /*   background-color: #f4f4f4; */
        }

        .container {
            /* background: #fff; */
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

        input, select, textarea {
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

<!-- Admin Sidebar -->
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
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Uploaded Successfully',
                text: 'Uploaded Successfully.',
                confirmButtonColor: '#4CAF50'
            });
        </script>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <!-- Crop Upload Form -->
    <div class="container">
        <h2>Add New Crop</h2>
        <form action="add-crops.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Crop Name" required>
            <textarea name="description" placeholder="About the Crop" required></textarea>
            <input type="file" name="image" accept="image/*" required>
            <button type="submit">Upload Crop</button>
        </form>
    </div>
</div>

</body>
</html>
