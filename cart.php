<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT c.item_id, c.quantity, i.name, i.price, i.image, c.created_at 
        FROM cart_items c JOIN items i ON c.item_id = i.id WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total_cart_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .cart-container { 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 40px 20px; 
        }
        .cart-item-modern { 
            display: flex; 
            align-items: center; 
            background: var(--white); 
            border-radius: var(--radius-md); 
            box-shadow: var(--shadow-soft); 
            margin-bottom: 20px; 
            padding: 15px; 
            transition: var(--transition); 
        }
        .cart-item-modern:hover { 
            box-shadow: var(--shadow-hover); 
            transform: translateY(-3px); 
        }
        .cart-img { 
            width: 120px; 
            height: 120px; 
            border-radius: 8px; 
            object-fit: cover; 
            margin-right: 20px; 
        }
        .cart-details { 
            flex: 1; 
        }
        .cart-details h4 { 
            font-size: 1.4rem; 
            margin: 0 0 5px 0; 
            color: var(--text-main); 
        }
        .cart-price { 
            font-size: 1.2rem; 
            color: var(--primary-color); 
            font-weight: 700; 
            margin-bottom: 5px; 
        }
        .cart-meta { 
            color: var(--text-muted); 
            font-size: 0.95rem; 
        }
        .cart-actions { 
            display: flex; 
            flex-direction: column; 
            align-items: flex-end; 
            justify-content: space-between; 
            height: 100px; 
        }
        .cart-actions .item-total {
            font-weight: 600; 
            font-size: 1.1rem;
        }
        .btn-remove { 
            color: #e74c3c; 
            text-decoration: none; 
            font-weight: 600; 
            font-size: 0.9rem; 
            transition: var(--transition); 
        }
        .btn-remove:hover { 
            color: #c0392b; 
            text-decoration: underline; 
        }
        .cart-summary { 
            background: var(--white); 
            padding: 30px; 
            border-radius: var(--radius-md); 
            box-shadow: var(--shadow-soft); 
            margin-top: 30px; 
            text-align: right; 
        }
        .cart-summary h3 { 
            font-size: 1.5rem; 
            margin-bottom: 20px; 
            color: var(--text-main); 
        }
        .cart-summary .highlight {
            color: var(--primary-color);
        }
        .btn-full { 
            display: inline-block; 
            width: auto; 
            padding: 15px 40px; 
            font-size: 1.1rem;
            background: var(--primary-color);
            color: var(--white);
            text-decoration: none;
            border-radius: var(--radius-md);
            font-weight: 600;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }
        .btn-full:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        .btn-full.small {
            padding: 12px 30px;
        }
        .empty-cart {
            text-align: center;
            padding: 50px;
            background: var(--white);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-soft);
        }
        .empty-cart img {
            margin-bottom: 20px;
        }
        .empty-cart h3 {
            color: var(--text-main);
            margin-bottom: 15px;
        }
        .empty-cart p {
            color: var(--text-muted);
            margin-bottom: 25px;
        }
        .section-heading {
            text-align: center;
            margin-bottom: 40px;
        }
        .cart-section {
            min-height: 70vh;
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <section class="section-padding cart-section">
        <div class="cart-container">
            <h2 class="section-heading">Your Shopping Cart</h2>
            
            <?php if ($result->num_rows > 0): ?>
                <div class="cart-items-wrapper">
                    <?php while($row = $result->fetch_assoc()): ?>
                        <?php 
                        $item_total = $row['price'] * $row['quantity']; 
                        $total_cart_price += $item_total; 
                        ?>
                        <div class="cart-item-modern">
                            <?php if ($row['image']): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($row['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($row['name']); ?>" 
                                     class="cart-img">
                            <?php else: ?>
                                <img src="images/default-food-img.jpg" 
                                     alt="Default Image" 
                                     class="cart-img">
                            <?php endif; ?>
                            
                            <div class="cart-details">
                                <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                                <div class="cart-price">৳ <?php echo htmlspecialchars($row['price']); ?></div>
                                <div class="cart-meta">
                                    Quantity: <strong><?php echo htmlspecialchars($row['quantity']); ?></strong><br>
                                    <small>Added: <?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                                </div>
                            </div>
                            
                            <div class="cart-actions">
                                <div class="item-total">৳ <?php echo number_format($item_total, 2); ?></div>
                                <a href="remove_from_cart.php?id=<?php echo $row['item_id']; ?>" 
                                   class="btn-remove">Remove Item</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="cart-summary">
                    <h3>Subtotal: <span class="highlight">৳ <?php echo number_format($total_cart_price, 2); ?></span></h3>
                    <a href="checkout.php" class="btn-full">Proceed to Checkout</a>
                </div>
            <?php else: ?>
                <div class="empty-cart">
                    <img src="https://img.icons8.com/fluency/96/000000/shopping-cart.png" alt="Empty Cart">
                    <h3>Your cart is currently empty</h3>
                    <p>Looks like you haven't added any delicious food yet.</p>
                    <a href="foods.php" class="btn-full small">Browse Menu</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>