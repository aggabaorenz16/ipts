<?php
session_start();
require_once('./db/database.php');

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['email'];

$sql = "SELECT * FROM cart_tbl WHERE user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <link rel="stylesheet" href="./css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .cart-container {
            padding: 30px;
            max-width: 900px;
            margin: auto;
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
            padding: 15px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-delete {
            background-color: #f44336;
            color: white;
        }
        .btn-checkout {
            background-color: #4CAF50;
            color: white;
        }
        .quantity-btn {
            padding: 6px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="cart-container">
    <h2>🛒 Your Cart</h2>

    <?php if ($result->num_rows > 0): ?>
        <form action="update_cart_quantity.php" method="POST">
            <input type="hidden" name="total_items" value="<?= $result->num_rows ?>">
            <table>
                <tr>
                    <th>Product</th>
                    <th>Price (₱)</th>
                    <th>Quantity</th>
                    <th>Subtotal (₱)</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php $subtotal = $row['price'] * $row['quantity']; $total_price += $subtotal; ?>
                    <tr>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td><?= number_format($row['price'], 2) ?></td>
                        <td>
                            <form action="update_cart_quantity.php" method="POST" style="display:inline;">
                                <input type="hidden" name="cart_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="quantity-btn" name="action" value="decrease">-</button>
                                <input type="text" name="quantity" value="<?= $row['quantity'] ?>" readonly style="width: 40px; text-align: center;">
                                <button type="submit" class="quantity-btn" name="action" value="increase">+</button>
                            </form>
                        </td>
                        <td><?= number_format($subtotal, 2) ?></td>
                        <td>
                            <form action="delete_from_cart.php" method="POST" class="delete-form" style="display:inline;">
                                <input type="hidden" name="cart_id" value="<?= $row['id'] ?>">
                                <button type="button" class="btn btn-delete swal-delete">Remove</button>
                            </form>

                            <!-- Checkout this item form -->
                            <form action="cart_checkout_single.php" method="POST" class="checkout-form" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                                <input type="hidden" name="product_name" value="<?= htmlspecialchars($row['product_name']) ?>">
                                <input type="hidden" name="product_price" value="<?= $row['price'] ?>">
                                <input type="hidden" name="quantity" value="<?= $row['quantity'] ?>">
                                <button type="submit" class="btn btn-checkout">Checkout this item</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td colspan="2"><strong>₱<?= number_format($total_price, 2) ?></strong></td>
                </tr>
            </table>
        </form>
    <?php else: ?>
        <p>Your cart is empty.</p>
        <a href="shop.php">← Back to Shop</a>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.swal-delete').forEach(button => {
    button.addEventListener('click', function () {
        const form = this.closest('.delete-form');
        Swal.fire({
            title: 'Remove item?',
            text: "Are you sure you want to remove this product from your cart?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f44336',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
