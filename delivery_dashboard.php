<?php
session_start();
include('db_connection.php');


if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'delivery') {
    header("Location: login.php");
    exit();
}

$delivery_id = $_SESSION['user']['id'];


$sql = "SELECT orders.id, users.name as user_name, items.name as item_name, items.image as item_image, orders.quantity, orders.total_price, orders.payment_method, orders.order_date, orders.delivery_status
        FROM orders
        JOIN users ON orders.user_id = users.id
        JOIN items ON orders.item_id = items.id
        WHERE orders.deliverymanId = ?
        ORDER BY orders.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $delivery_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .dashboard {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .dashboard h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .order-table th, .order-table td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: left;
        }
        .order-table th {
            background-color: #f8f8f8;
            font-weight: bold;
        }
        .order-table td img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .order-actions select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            background-color: #fff;
        }
        .order-actions input[type="submit"] {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        .order-actions input[type="submit"]:hover {
            background-color: #45a049;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            text-align: center;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        .update-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .update-button:hover {
            background-color: #45a049;
        }
        .update-button:focus {
            outline: none;
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.6);
        }
        .delivered-status {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <section class="dashboard">
            <h2>Delivery Dashboard</h2>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>

            <div class="orders">
                <h3>Assigned Orders</h3>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User Name</th>
                            <th>Item Name</th>
                            <th>Item Image</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Payment Method</th>
                            <th>Order Date</th>
                            <th>Delivery Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['item_name']); ?></td>
                                <td>
                                    <?php if ($order['item_image']): ?>
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($order['item_image']); ?>" alt="<?php echo htmlspecialchars($order['item_name']); ?>">
                                    <?php else: ?>
                                        <img src="images/default-food-img.jpg" alt="Default Image">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $order['quantity']; ?></td>
                                <td>৳<?php echo $order['total_price']; ?></td>
                                <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                <td>
                                    <?php if ($order['delivery_status'] === 'Delivered'): ?>
                                        <span class="delivered-status"><?php echo $order['delivery_status']; ?></span>
                                    <?php else: ?>
                                        <form action="update_delivery_status.php" method="POST">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="delivery_status" required>
                                                <option value="Pending" <?php echo $order['delivery_status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="Out for Delivery" <?php echo $order['delivery_status'] === 'Out for Delivery' ? 'selected' : ''; ?>>Out for Delivery</option>
                                                <option value="Delivered" <?php echo $order['delivery_status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            </select>
                                            <input type="submit" value="Update" class="update-button">
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <?php include 'footer.php'; ?> <!-- Include your footer -->

</body>
</html>
