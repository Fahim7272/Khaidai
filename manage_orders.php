<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// Fetch orders from the database
$sql_orders = "SELECT orders.id, users.name AS user_name, orders.delivery_status, orders.order_date, orders.total_price, orders.deliverymanId, deliverymen.name AS deliveryman_name, orders.ofd_time, orders.delivered_time, orders.payment_method
               FROM orders
               JOIN users ON orders.user_id = users.id
               LEFT JOIN deliverymen ON orders.deliverymanId = deliverymen.id
               ORDER BY orders.order_date DESC";


$result_orders = $conn->query($sql_orders);

// Fetch deliverymen for dropdown
$sql_deliverymen = "SELECT id, name FROM deliverymen";
$result_deliverymen = $conn->query($sql_deliverymen);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .status-pending {
            color: orange;
        }
        .status-out-for-delivery {
            color: blue;
        }
        .status-delivered {
            color: green;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <section class="food-search text-center">
        <div class="container">
        <h1>Manage Orders</h1>
        <p>This administration page for overseeing and updating customer orders,<br>ensuring efficient tracking and accurate status management.</p>
        </div>
    </section>
    <section class="content">
        <div class="wrapper">
            <br>

            <?php if ($result_orders->num_rows > 0): ?>
            <table class="tbl-full">
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Delivery Status</th>
                    <th>Deliveryman</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result_orders->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                        <td>৳<?php echo htmlspecialchars($row['total_price']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                        <td>
                            <?php
                            $status_class = '';
                            if ($row['delivery_status'] == 'Pending') {
                                $status_class = 'status-pending';
                            } elseif ($row['delivery_status'] == 'Out for Delivery') {
                                $status_class = 'status-out-for-delivery';
                            } elseif ($row['delivery_status'] == 'Delivered') {
                                $status_class = 'status-delivered';
                            }
                            ?>
                            <span class="<?php echo $status_class; ?>">
                                <?php
                                echo htmlspecialchars($row['delivery_status']);
                                if ($row['delivery_status'] == 'Out for Delivery') {
                                    echo '<br><small>' . htmlspecialchars($row['ofd_time']) . '</small>';
                                } elseif ($row['delivery_status'] == 'Delivered') {
                                    echo '<br><small>' . htmlspecialchars($row['delivered_time']) . '</small>';
                                }
                                ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['delivery_status'] == 'Pending'): ?>
                                <form action="assign_deliveryman.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <select name="deliveryman_id">
                                        <option value="">Select Deliveryman</option>
                                        <?php
                                        // Reset deliverymen result pointer
                                        $result_deliverymen->data_seek(0);
                                        while ($deliveryman = $result_deliverymen->fetch_assoc()): ?>
                                            <option value="<?php echo $deliveryman['id']; ?>" <?php echo ($row['deliverymanId'] == $deliveryman['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($deliveryman['name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                    <button type="submit" class="btn-primary">Assign</button>
                                </form>
                            <?php else: ?>
                                <?php echo htmlspecialchars($row['deliveryman_name']); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="delete_order.php?id=<?php echo $row['id']; ?>" class="btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <?php else: ?>
                <p>No orders found.</p>
            <?php endif; ?>
        </div>
    </section>
    <section class="footer">
        <div class="container text-center">
            <p>All rights reserved - KhaiDai</p>
        </div>
    </section>
</body>
</html>
