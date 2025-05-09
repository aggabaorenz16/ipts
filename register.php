<?php 
require_once('./db/database.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']); // ✅ FIXED: Added assignment
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    // Validate names
    if (!preg_match("/^[a-zA-Z]+$/", $first_name)) {
        $message = "First name can only contain letters.";
    } elseif (!preg_match("/^[a-zA-Z]*$/", $middle_name)) {
        $message = "Middle name can only contain letters.";
    } elseif (!preg_match("/^[a-zA-Z]+$/", $last_name)) {
        $message = "Last name can only contain letters.";
    } else {
        // ✅ FIXED: Corrected SQL and parameter count
        $stmt = $conn->prepare("INSERT INTO accounts_tbl (first_name, middle_name, last_name, email, address, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $middle_name, $last_name, $email, $address, $password);

        if ($stmt->execute()) {
            $message = "Registration successful! You will be redirected to the login page.";
            header("Refresh: 2; url=login.php");
        } else {
            $message = "Email already exists or an error occurred.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        .image {
            display: grid;
            place-content: center;
            padding: 1rem;
        }
        .image img {
            width: 150px;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            /* height: 100vh; */
        }

        .form-container {
            background: #fff;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 500px;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        
        input[type="text"]:hover,
        input[type="email"]:hover,
        input[type="password"]:hover {
           border: 1px solid green;
        }
        button {
            padding: 10px;
            width: 100%;
            background-color: #007bff;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
        }

        .info {
            color: green;
        }
        a {
            color: green;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="form-container">
   <div class="image">
    <a href="index.php">
        <img src="./image/qcuai logo.png" alt="qcui logo">
    </a>
</div>
    <h2>Register</h2>
    <?php if ($message): ?>
        <p class="info"><?php echo $message; ?></p>
    <?php endif; ?>
    <form action="register.php" method="POST">
        <input type="text" name="first_name" placeholder="First Name" required><br>
        <input type="text" name="middle_name" placeholder="Middle Name (Optional)"><br>
        <input type="text" name="last_name" placeholder="Last Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="text" name="address" placeholder="Address" required><br> <!-- ✅ Added address input -->
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</div>
</body>
</html>
