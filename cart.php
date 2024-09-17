<?php
session_start();
include('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items from the database
$sql = "SELECT c.item_id, c.quantity, i.name, i.price, i.image, c.created_at 
        FROM cart_items c 
        JOIN items i ON c.item_id = i.id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cart.css">
    <style>
        .cart {
            padding: 20px 0;
        }

        

        .cart h2 {
            font-size: 2em;
            margin-bottom: 20px;
            text-align: center;
        }

        .cart-items {
            display: flex;
            flex-direction: column;
        }

        .cart-item {
            display: flex;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
        }

        .cart-item-img img {
            width: 150px;
            height: auto;
            border-right: 1px solid #ddd;
        }

        .cart-item-desc {
            padding: 10px;
            flex: 1;
        }

        .cart-item-desc h4 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .cart-item-desc p {
            margin: 5px 0;
        }

        .cart-item-price {
            color: #e67e22;
            font-size: 1.2em;
        }

        .cart-item-quantity {
            font-size: 1em;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 10px;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            color: #fff;
            background-color: #e67e22;
        }

        .btn-primary {
            background-color: #3498db;
        }

        .btn-danger {
            background-color: #e74c3c;
        }

        .btn-primary:hover,
        .btn-danger:hover {
            background-color: #d35400;
        }
    </style> <!-- Additional CSS for cart page -->
</head>
<body>

    <?php include 'navbar.php'; ?>

    <section class="food-search text-center">
        <div class="container">
            
        <h3 class="text-white">My Cart</h3>

        </div>
    </section>
    
    <section class="cart">
        <div class="container">
            
            
            <?php if ($result->num_rows > 0): ?>
                <div class="cart-items">
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="cart-item">
                            <div class="cart-item-img">
                                <?php if ($row['image']): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="img-responsive img-curve">
                                <?php else: ?>
                                    <img src="images/default-food-img.jpg" alt="Default Image" class="img-responsive img-curve">
                                <?php endif; ?>
                            </div>
                            <div class="cart-item-desc">
                                <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                                <p class="cart-item-price">৳<?php echo htmlspecialchars($row['price']); ?></p>
                                <p class="cart-item-quantity">Quantity: <?php echo htmlspecialchars($row['quantity']); ?></p>
                                <p class="cart-item-date">Added on: <?php echo htmlspecialchars($row['created_at']); ?></p>
                                <br>
                                <a href="remove_from_cart.php?id=<?php echo $row['item_id']; ?>" class="btn btn-danger">Remove</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
            <?php else: ?>
                <p class="text-center">Your cart is empty.</p>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>

