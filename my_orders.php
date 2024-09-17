<?php
session_start();
include('db_connection.php');

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's orders with item details from the database
$sql = "SELECT o.*, i.name AS item_name, i.price AS item_price, i.image AS item_image
        FROM orders o
        INNER JOIN items i ON o.item_id = i.id
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC";
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
    <title>My Orders</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Include your CSS file -->
    <style>
        /* Example CSS for styling */
       
        .order-item {
            border: 1px solid #ccc;
            margin-bottom: 20px;
            padding: 10px;
            display: flex;
        }
        .order-item img {
            width: 150px; /* Adjust size as needed */
            height: auto;
            margin-right: 20px;
        }
        .order-item-content {
            flex: 1;
        }
        .order-item h3 {
            margin-bottom: 10px;
        }
        .order-item p {
            margin-bottom: 5px;
        }
        .order-item ul {
            list-style-type: none;
            padding-left: 0;
        }
        .delivery-status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
        }
        .status-pending {
            background-color: #ff3333; /* Red for pending */
        }
        .status-out-for-delivery {
            background-color: #ffcc00; /* Yellow for out for delivery */
        }
        .status-delivered {
            background-color: #00cc66; /* Green for delivered */
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?> <!-- Include your navigation bar -->
    
    <section class="food-search text-center">
        <div class="container">
            
        <h3 class="text-white">My Orders</h3>

        </div>
    </section>

    <div class="container">
        

        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="order-item">
                    <?php if ($row['item_image']): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['item_image']); ?>" alt="<?php echo htmlspecialchars($row['item_name']); ?>">
                    <?php else: ?>
                        <img src="images/default-food-img.jpg" alt="Default Image">
                    <?php endif; ?>
                    <div class="order-item-content">
                        <h3><?php echo htmlspecialchars($row['item_name']); ?></h3>
                        <p>Order Date: <?php echo $row['order_date']; ?></p>
                        <ul>
                            <li>Quantity: <?php echo $row['quantity']; ?></li>
                            <li>Item Price: $<?php echo $row['item_price']; ?></li>
                            <li>Total Price: $<?php echo $row['total_price']; ?></li>
                            <li>Payment Method: <?php echo $row['payment_method']; ?></li>
                            <li>
                                Delivery Status:
                                <span class="delivery-status <?php
                                    if ($row['delivery_status'] === 'Pending') {
                                        echo 'status-pending';
                                    } elseif ($row['delivery_status'] === 'Out for Delivery') {
                                        echo 'status-out-for-delivery';
                                    } elseif ($row['delivery_status'] === 'Delivered') {
                                        echo 'status-delivered';
                                    }
                                ?>">
                                    <?php echo $row['delivery_status']; ?>
                                </span>
                                <?php if ($row['delivery_status'] === 'Out for Delivery' && $row['ofd_time']): ?>
                                    <br>OFD Time: <?php echo $row['ofd_time']; ?>
                                <?php elseif ($row['delivery_status'] === 'Delivered' && $row['delivered_time']): ?>
                                    <br>Delivered Time: <?php echo $row['delivered_time']; ?>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?> <!-- Include your footer -->

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
