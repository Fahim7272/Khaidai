<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

function getItemDetails($conn, $item_id) {
    $sql = "SELECT id, name, price, image FROM items WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();
    return $item;
}

$user = $_SESSION['user'];
$user_id = $user['id'];

if (isset($_SESSION['selected_items']) && !empty($_SESSION['selected_items'])) {
    $selected_items = $_SESSION['selected_items'];

    $order_items = [];
    $total_checkout_price = 0;
    
    foreach ($selected_items as $item_id => $quantity) {
        $item = getItemDetails($conn, $item_id);
        if ($item) {
            $item['quantity'] = $quantity;
            $item['total_price'] = $item['price'] * $quantity; 
            $order_items[] = $item;
            $total_checkout_price += $item['total_price'];
        }
    }
    // We do NOT unset selected_items yet, so they can process the payment!
} else {
    header("Location: cart.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Order - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .checkout-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 40px; max-width: 1100px; margin: 0 auto; align-items: start; }
        .summary-card, .payment-card { background: var(--white); padding: 30px; border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); }
        .summary-card h3, .payment-card h3 { margin-bottom: 20px; color: var(--text-main); font-size: 1.5rem; border-bottom: 2px solid var(--light-bg); padding-bottom: 15px; }
        .cart-checkout-item { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; font-weight: 500; }
        .cart-checkout-item img { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; }
        .payment-option { display: block; border: 2px solid var(--light-bg); padding: 15px 20px; border-radius: 8px; margin-bottom: 15px; cursor: pointer; transition: var(--transition); font-weight: 500; }
        .payment-option:hover { border-color: var(--primary-color); background: rgba(255, 71, 87, 0.05); }
        .payment-option input[type="radio"] { margin-right: 10px; transform: scale(1.2); accent-color: var(--primary-color); }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; font-family: 'Poppins', sans-serif; margin-bottom: 15px; }
        @media (max-width: 768px) { .checkout-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <section class="section-padding" style="min-height: 70vh;">
        <div class="container">
            <h2 class="text-center" style="margin-bottom: 40px;">Finalize Your Order</h2>

            <div class="checkout-grid">
                <div class="summary-card">
                    <h3>Receipt Summary</h3>
                    
                    <?php foreach ($order_items as $item): ?>
                        <div class="cart-checkout-item">
                            <?php if ($item['image']): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($item['image']); ?>" alt="Food">
                            <?php else: ?>
                                <img src="images/default-food-img.jpg" alt="Default">
                            <?php endif; ?>
                            <div style="flex: 1;">
                                <div style="margin: 0;"><?php echo htmlspecialchars($item['name']); ?></div>
                                <small style="color:var(--text-muted);">Qty: <?php echo $item['quantity']; ?></small>
                            </div>
                            <div style="font-weight: 700;">৳ <?php echo number_format($item['total_price'], 2); ?></div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div style="display: flex; justify-content: space-between; margin-top: 20px; font-size: 1.2rem; font-weight: 700; border-top: 2px dashed var(--light-bg); padding-top: 20px;">
                        <span>Total to Pay:</span>
                        <span style="color: var(--primary-color);">৳ <?php echo number_format($total_checkout_price, 2); ?></span>
                    </div>
                </div>

                <div class="payment-card">
                    <h3>Delivery & Payment</h3>
                    <form action="order_selected_items.php" method="POST">
                        <?php foreach ($order_items as $item): ?>
                            <input type="hidden" name="selected_items[]" value="<?php echo $item['id']; ?>">
                        <?php endforeach; ?>

                        <label style="font-weight: 500; display: block; margin-bottom: 8px;">Delivery Address</label>
                        <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" required placeholder="Your delivery location">
                        
                        <label style="font-weight: 500; display: block; margin-bottom: 10px; margin-top: 10px;">Select Payment Method</label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="Cash on Delivery" checked> Cash on Delivery
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="bKash"> bKash
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="Nagad"> Nagad
                        </label>

                        <button type="submit" class="btn-full" style="border:none; cursor:pointer; font-size:1.1rem; margin-top: 20px; padding: 15px; background: var(--primary-color); color: var(--white); border-radius: 8px;">Place Final Order</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?> 
</body>
</html>
<?php $conn->close(); ?>