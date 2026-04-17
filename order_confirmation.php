<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - KhaiDai</title>
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
    
    <section class="section-padding" style="min-height: 70vh; display: flex; align-items: center;">
        <div class="container">
            <div class="confirmation-card">
                <img src="https://img.icons8.com/color/96/000000/ok--v2.png" alt="Success" class="icon-success">
                <h2>Cart Checkout Successful!</h2>
                <p>Your order has been placed successfully and your cart has been cleared. Our delivery team will process it shortly and contact you when it's on the way.</p>
                
                <a href="my_orders.php" class="btn-primary" style="padding: 12px 30px; border-radius: 8px; margin-right: 10px;">Track Your Order</a>
                <a href="index.php" class="btn-nav-outline" style="padding: 12px 30px; border-radius: 8px; text-decoration: none; color: var(--text-main); border: 2px solid #ccc;">Return Home</a>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>