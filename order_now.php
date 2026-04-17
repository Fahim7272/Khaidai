<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$sql_user = "SELECT * FROM users WHERE id = $user_id";
$result_user = $conn->query($sql_user);

if ($result_user->num_rows == 1) {
    $user = $result_user->fetch_assoc();
} else {
    header('Location: foods.php');
    exit();
}

$item_id = isset($_GET['id']) ? intval($_GET['id']) : null;
$item_details = [];
$error_message = '';

if ($item_id) {
    $sql_item = "SELECT * FROM items WHERE id = $item_id";
    $result_item = $conn->query($sql_item);
    if ($result_item->num_rows == 1) {
        $item_details = $result_item->fetch_assoc();
    } else {
        $error_message = "Item not found.";
    }
} else {
    $error_message = "Item ID not provided.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['quantity'])) {
    $quantity = intval($_POST['quantity']);
    $payment_method = $_POST['payment_method'] ?? 'Cash on Delivery';
    $total_price = $item_details['price'] * $quantity;
    $address = $user['location']; // Utilizing the user's saved location
    
    // Insert direct order
    $sql_order = "INSERT INTO orders (user_id, item_id, quantity, total_price, payment_method, address, status, delivery_status)
                  VALUES ($user_id, $item_id, $quantity, $total_price, '$payment_method', '$address', 'Pending', 'Pending')";
    
    if ($conn->query($sql_order) === TRUE) {
        header("Location: my_orders.php");
        exit();
    } else {
        $error_message = "Failed to place order: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .checkout-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 40px; max-width: 1000px; margin: 0 auto; align-items: start; }
        .summary-card, .payment-card { background: var(--white); padding: 30px; border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); }
        .summary-card h3, .payment-card h3 { margin-bottom: 20px; color: var(--text-main); font-size: 1.5rem; border-bottom: 2px solid var(--light-bg); padding-bottom: 15px; }
        .item-preview { display: flex; gap: 15px; margin-bottom: 20px; align-items: center; }
        .item-preview img { width: 80px; height: 80px; border-radius: 8px; object-fit: cover; }
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
            <?php if ($error_message): ?>
                <div class="text-center" style="color: red; padding: 20px;"><?php echo $error_message; ?></div>
            <?php elseif (!empty($item_details)): ?>
                
                <h2 class="text-center" style="margin-bottom: 40px;">Secure Checkout</h2>

                <div class="checkout-grid">
                    <div class="summary-card">
                        <h3>Order Summary</h3>
                        <div class="item-preview">
                            <?php if ($item_details['image']): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($item_details['image']); ?>" alt="Food Image">
                            <?php else: ?>
                                <img src="images/default-food-img.jpg" alt="Default Food">
                            <?php endif; ?>
                            <div>
                                <h4 style="margin: 0 0 5px 0;"><?php echo htmlspecialchars($item_details['name']); ?></h4>
                                <div style="color: var(--primary-color); font-weight: 700;">৳ <span id="base-price"><?php echo htmlspecialchars($item_details['price']); ?></span></div>
                            </div>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; margin-top: 20px; font-size: 1.2rem; font-weight: 700; border-top: 2px dashed var(--light-bg); padding-top: 20px;">
                            <span>Total to Pay:</span>
                            <span style="color: var(--primary-color);">৳ <span id="total-display"><?php echo htmlspecialchars($item_details['price']); ?></span></span>
                        </div>
                    </div>

                    <div class="payment-card">
                        <h3>Delivery Details & Payment</h3>
                        <form action="order_now.php?id=<?php echo $item_id; ?>" method="POST">
                            <label style="font-weight: 500; display: block; margin-bottom: 8px;">Delivery Address</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['location']); ?>" disabled style="background: #f1f2f6;">
                            <small style="color: var(--text-muted); display: block; margin-bottom: 20px;">* Address is pulled from your profile. Please update your profile if needed.</small>

                            <label style="font-weight: 500; display: block; margin-bottom: 8px;">Quantity</label>
                            <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="20" required onchange="updateTotal()">

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

                            <button type="submit" class="btn-full" style="border:none; cursor:pointer; font-size:1.1rem; margin-top: 20px; padding: 15px;">Confirm Order</button>
                        </form>
                    </div>
                </div>

            <?php endif; ?>
        </div>
    </section>

    <?php include 'footer.php'; ?> 

    <script>
        function updateTotal() {
            const quantity = document.getElementById('quantity').value;
            const basePrice = parseFloat(document.getElementById('base-price').innerText);
            const totalDisplay = document.getElementById('total-display');
            totalDisplay.innerText = (basePrice * quantity).toFixed(2);
        }
    </script>
</body>
</html>