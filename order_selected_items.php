<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');
$user_id = $_SESSION['user']['id'];
$success_message = $error_message = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['selected_items']) && !empty($_POST['selected_items'])) {
        $selected_items = array_map('intval', $_POST['selected_items']); 
        $sql_update_items = "UPDATE cart_items SET ordered = 1 WHERE user_id = ? AND id IN (". implode(',', $selected_items) .")";
        $stmt = $conn->prepare($sql_update_items);
        $stmt->bind_param("i",  $user_id);

        if ($stmt->execute()) {
            $success_message = "Your order has been placed successfully! Our delivery team will contact you shortly.";
        } else {
            $error_message = "Error placing order: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error_message = "No items were selected for checkout.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .confirmation-card { max-width: 600px; margin: 80px auto; background: var(--white); padding: 50px 30px; border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); text-align: center; }
        .icon-success { width: 80px; height: 80px; margin-bottom: 20px; }
        .confirmation-card h2 { color: var(--text-main); margin-bottom: 15px; font-size: 2rem; }
        .confirmation-card p { color: var(--text-muted); font-size: 1.1rem; margin-bottom: 30px; line-height: 1.6; }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="confirmation-card">
            <?php if (isset($success_message)): ?>
                <img src="https://img.icons8.com/color/96/000000/ok--v2.png" alt="Success" class="icon-success">
                <h2>Order Confirmed!</h2>
                <p><?php echo $success_message; ?></p>
                <a href="my_orders.php" class="btn-primary" style="padding: 12px 30px; border-radius: 8px;">Track Your Order</a>
                <br><br>
                <a href="foods.php" style="color: var(--primary-color); font-weight: 500;">&larr; Continue Shopping</a>
            <?php else: ?>
                <img src="https://img.icons8.com/color/96/000000/cancel--v1.png" alt="Error" class="icon-success">
                <h2>Order Failed</h2>
                <p><?php echo $error_message ?? "Something went wrong."; ?></p>
                <a href="cart.php" class="btn-primary" style="padding: 12px 30px; border-radius: 8px;">Return to Cart</a>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>