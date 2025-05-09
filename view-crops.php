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

// Handle crop update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $edit_id = (int)$_POST['edit_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image = $_POST['image'];

    $stmt = $conn->prepare("UPDATE crops SET name = ?, description = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $description, $image, $edit_id);

    if ($stmt->execute()) {
        $success = "Crop updated successfully!";
    } else {
        $error = "Failed to update crop.";
    }

    $stmt->close();
}

// Handle crop delete
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM crops WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $success = "Crop deleted successfully!";
    } else {
        $error = "Failed to delete crop.";
    }

    $stmt->close();
}


// Fetch crops
$sql = "SELECT id, name, description, image FROM crops";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - View Crops</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
         #logoutLink {
                background-color: red;
                border: none;
            }
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; display: flex; }
        .sidebar {
            width: 250px; height: 100vh; background-color: #4CAF50; color: white; padding-top: 20px; position: fixed;
        }
        .sidebar a {
            display: block; color: white; text-decoration: none; padding: 15px; font-size: 18px; border-bottom: 1px solid #fff;
        }
        .sidebar a:hover { background-color: #45a049; }
        .sidebar i { margin-right: 10px; }
        .content {
            margin-left: 260px; padding: 40px; width: 100%;
        }
        .container { width: 100%; }
        h2 { text-align: center; color: green; }
        table {
            width: 100%; border-collapse: collapse; margin-top: 20px;
        }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        img { width: 50px; height: auto; }
        .action-buttons button, .action-buttons a {
            display: inline-flex; align-items: center; color: white; padding: 8px 15px;
            font-size: 16px; cursor: pointer; border: none; border-radius: 5px; text-decoration: none; margin-right: 10px;
        }
        .edit-button { background-color: #4CAF50; }
        .edit-button:hover { background-color: #45a049; }
        .delete-button { background-color: #d9534f; }
        .delete-button:hover { background-color: #c9302c; }
        .modal {
            display: none; position: fixed; z-index: 1; left: 0; top: 0;
            width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fff; margin: 5% auto; padding: 20px; width: 50%; border-radius: 8px;
        }
        .close { float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        input[type="text"], textarea, input[type="submit"] {
            width: 100%; padding: 10px; margin: 10px 0; font-size: 16px;
        }
        input[type="submit"] {
            background-color: #4CAF50; color: white; border: none;
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
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Log Out</a>
</div>

<div class="content">
    <h2>Manage Crops</h2>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Description</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['description'] ?></td>
                    <td><img src="<?= $row['image'] ?>" alt="<?= $row['name'] ?>"></td>
                    <td class="action-buttons">
                        <button onclick="openModal(<?= $row['id'] ?>, '<?= addslashes($row['name']) ?>', '<?= addslashes($row['description']) ?>', '<?= $row['image'] ?>')" class="edit-button">
                            <i class="fas fa-pencil-alt"></i> Edit
                        </button>
                        <a href="view-crops.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this crop?')" class="delete-button">
    <i class="fas fa-times"></i> Delete
</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No crops found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Edit Crop</h2>
            <form method="POST" action="view-crops.php">
                <input type="hidden" name="edit_id" id="edit_id">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required>

                <label for="description">Description:</label>
                <textarea name="description" id="description" required></textarea>

                <label for="image">Image URL:</label>
                <input type="text" name="image" id="image" required>

                <input type="submit" value="Save Changes">
            </form>
        </div>
    </div>
</div>

<script>
    function openModal(id, name, description, image) {
        document.getElementById('edit_id').value = id;
        document.getElementById('name').value = name;
        document.getElementById('description').value = description;
        document.getElementById('image').value = image;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target === document.getElementById('editModal')) {
            closeModal();
        }
    }
</script>

<!-- SweetAlert for success/error -->
<?php if ($success): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '<?= $success ?>',
        timer: 2000,
        showConfirmButton: false
    });
</script>
<?php elseif ($error): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= $error ?>',
        timer: 2000,
        showConfirmButton: false
    });
</script>
<?php endif; ?>

</body>
</html>
