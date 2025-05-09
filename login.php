<?php
session_start();
require_once('./db/database.php');

// Handle login logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_email = $_POST['email'];
    $user_password = $_POST['password'];

    // Check if email exists in accounts table
    $stmt = $conn->prepare("SELECT password, role FROM accounts_tbl WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hashed_password, $role);
        $stmt->fetch();

        // Verify password
        if (password_verify($user_password, $hashed_password)) {
            $_SESSION['email'] = $user_email;
            $_SESSION['role'] = $role;

            if ($role === 'admin') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: shop.php');
            }
            exit();
        } else {
            $error = "Incorrect password. Please try again.";
        }
    } else {
        $error = "Email not found. Please try again or register.";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - QCU Urban Agriculture</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 24px;
            color: green;
        }

        form input[type="email"],
        form input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        a {
            text-decoration: none;
            color: green;
        }
        form input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: green;
            border: none;
            border-radius: 6px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form input[type="submit"]:hover {
            background-color:rgba(0, 128, 0, 0.81);
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 16px;
        }
        .image {
            display: grid;
            place-content: center;
            padding: 1rem;
        }
        .image img {
            width: 150px;
        }
    
    </style>
</head>
<body>
    <div class="login-container">
    <div class="image">
        <img src="./image/qcuai logo.png" alt="qcui logo">
        </div>
        <h2>Login your account</h2>
        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>

        <form method="POST" action="login.php">
            <label for="Email">Email Address</label>
            <input type="email" name="email" id="Email" placeholder="Enter your email" required>
            <label for="Password">Password</label>
            <input type="password" name="password" id="Password" required placeholder="Enter your password">
            <a href="register.php">Register</a><br><br>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
