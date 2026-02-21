<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$sql_user = "SELECT * FROM users WHERE id = $user_id";
$user = $conn->query($sql_user)->fetch_assoc();

// Fetch cart items
$sql = "SELECT c.item_id, c.quantity, i.name, i.price, i.image 
        FROM cart_items c JOIN items i ON c.item_id = i.id WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$total_cart_price = 0;
foreach ($cart_items as $item) {
    $total_cart_price += ($item['price'] * $item['quantity']);
}

// Redirect if cart is empty
if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart Checkout - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .checkout-grid { 
            display: grid; 
            grid-template-columns: 1fr 1.5fr; 
            gap: 40px; 
            max-width: 1100px; 
            margin: 0 auto; 
            align-items: start; 
        }
        .summary-card, 
        .payment-card { 
            background: var(--white); 
            padding: 30px; 
            border-radius: var(--radius-lg); 
            box-shadow: var(--shadow-soft); 
        }
        .summary-card h3, 
        .payment-card h3 { 
            margin-bottom: 20px; 
            color: var(--text-main); 
            font-size: 1.5rem; 
            border-bottom: 2px solid var(--light-bg); 
            padding-bottom: 15px; 
        }
        .cart-checkout-item { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 15px; 
            font-weight: 500; 
        }
        .cart-checkout-item small {
            color: var(--text-muted);
        }
        .payment-option { 
            display: block; 
            border: 2px solid var(--light-bg); 
            padding: 15px 20px; 
            border-radius: 8px; 
            margin-bottom: 15px; 
            cursor: pointer; 
            transition: var(--transition); 
            font-weight: 500; 
        }
        .payment-option:hover { 
            border-color: var(--primary-color); 
            background: rgba(255, 71, 87, 0.05); 
        }
        .payment-option input[type="radio"] { 
            margin-right: 10px; 
            transform: scale(1.2); 
            accent-color: var(--primary-color); 
        }
        .form-control { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ccc; 
            border-radius: 8px; 
            font-family: 'Poppins', sans-serif; 
            margin-bottom: 15px; 
        }
        .form-row {
            display: flex; 
            gap: 15px;
        }
        .form-row .form-group {
            flex: 1;
        }
        .form-label {
            font-weight: 500; 
            display: block; 
            margin-bottom: 8px;
        }
        .total-row {
            display: flex; 
            justify-content: space-between; 
            margin-top: 20px; 
            font-size: 1.2rem; 
            font-weight: 700; 
            border-top: 2px dashed var(--light-bg); 
            padding-top: 20px;
        }
        .total-amount {
            color: var(--primary-color);
        }
        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }
        .checkout-section {
            min-height: 70vh;
        }
        .btn-confirm {
            border: none; 
            cursor: pointer; 
            font-size: 1.1rem; 
            margin-top: 20px; 
            padding: 15px;
            width: 100%;
            background: var(--primary-color);
            color: var(--white);
            border-radius: 8px;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-confirm:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        @media (max-width: 768px) { 
            .checkout-grid { 
                grid-template-columns: 1fr; 
            }
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <section class="section-padding checkout-section">
        <div class="container">
            <h2 class="section-title">Complete Your Order</h2>

            <div class="checkout-grid">
                <div class="summary-card">
                    <h3>Cart Summary</h3>
                    
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-checkout-item">
                            <span>
                                <?php echo htmlspecialchars($item['name']); ?> 
                                <small>(x<?php echo $item['quantity']; ?>)</small>
                            </span>
                            <span>৳ <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="total-row">
                        <span>Total to Pay:</span>
                        <span class="total-amount">৳ <?php echo number_format($total_cart_price, 2); ?></span>
                    </div>
                </div>

                <div class="payment-card">
                    <h3>Delivery Details & Payment</h3>
                    <form action="place_order.php" method="POST">
                        <label class="form-label">Street Address</label>
                        <input type="text" 
                               name="address" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($user['location']); ?>" 
                               required 
                               placeholder="House, Road, Area">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">City</label>
                                <input type="text" 
                                       name="city" 
                                       class="form-control" 
                                       required 
                                       placeholder="e.g. Dhaka">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Postal Code</label>
                                <input type="text" 
                                       name="postal_code" 
                                       class="form-control" 
                                       required 
                                       placeholder="e.g. 1212">
                            </div>
                        </div>

                        <label class="form-label">Select Payment Method</label>
                        <label class="payment-option">
                            <input type="radio" 
                                   name="payment_method" 
                                   value="Cash on Delivery" 
                                   checked> 
                            Cash on Delivery
                        </label>
                        <label class="payment-option">
                            <input type="radio" 
                                   name="payment_method" 
                                   value="bKash"> 
                            bKash
                        </label>
                        <label class="payment-option">
                            <input type="radio" 
                                   name="payment_method" 
                                   value="Nagad"> 
                            Nagad
                        </label>

                        <button type="submit" class="btn-confirm">Confirm & Place Order</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?> 
</body>
</html>