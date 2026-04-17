<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include('db_connection.php');

$sql_orders = "SELECT orders.id, users.name AS user_name, orders.delivery_status, orders.order_date, orders.total_price, orders.deliverymanId, deliverymen.name AS deliveryman_name, orders.payment_method
               FROM orders
               JOIN users ON orders.user_id = users.id
               LEFT JOIN deliverymen ON orders.deliverymanId = deliverymen.id
               ORDER BY orders.order_date DESC";
$result_orders = $conn->query($sql_orders);

$sql_deliverymen = "SELECT id, name FROM deliverymen";
$result_deliverymen = $conn->query($sql_deliverymen);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .table-container { background: var(--white); padding: 30px; border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        th { background: var(--dark-bg); color: var(--white); padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        .select-delivery { padding: 6px; border-radius: 5px; border: 1px solid #ccc; font-family: 'Poppins', sans-serif; outline: none; }
        .btn-assign { background: var(--dark-bg); color: white; border: none; padding: 7px 12px; border-radius: 5px; cursor: pointer; transition: var(--transition); }
        .btn-assign:hover { background: var(--primary-color); }
        .btn-delete { background: #e74c3c; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 0.9rem; transition: var(--transition); }
        
        .status-badge { display: inline-block; padding: 5px 12px; border-radius: 50px; font-weight: 600; font-size: 0.85rem; }
        .status-pending { background-color: #ffeaa7; color: #d35400; }
        .status-ofd { background-color: #74b9ff; color: #0984e3; }
        .status-delivered { background-color: #55efc4; color: #00b894; }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <section class="hero-section" style="height: 25vh; min-height: 200px;">
        <div class="hero-overlay"></div>
        <div class="container hero-content text-center">
            <h1 class="hero-title" style="font-size: 2.2rem;">Manage Orders</h1>
        </div>
    </section>

    <section class="section-padding">
        <div class="container">
            <?php if (isset($_SESSION['message'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total (৳)</th>
                            <th>Status</th>
                            <th>Deliveryman Assignment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_orders->num_rows > 0): ?>
                            <?php while($row = $result_orders->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></strong><br><small style="color:var(--text-muted);"><?php echo date('M d, g:i A', strtotime($row['order_date'])); ?></small></td>
                                    <td><?php echo htmlspecialchars($row['user_name']); ?><br><small>(<?php echo $row['payment_method']; ?>)</small></td>
                                    <td><strong>৳<?php echo number_format($row['total_price'], 2); ?></strong></td>
                                    <td>
                                        <?php 
                                            $s_class = 'status-pending';
                                            if ($row['delivery_status'] == 'Out for Delivery') $s_class = 'status-ofd';
                                            if ($row['delivery_status'] == 'Delivered') $s_class = 'status-delivered';
                                        ?>
                                        <span class="status-badge <?php echo $s_class; ?>"><?php echo htmlspecialchars($row['delivery_status']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($row['delivery_status'] != 'Delivered'): ?>
                                            <form action="assign_deliveryman.php" method="POST" style="display: flex; gap: 8px;">
                                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                                <select name="deliveryman_id" class="select-delivery" required>
                                                    <option value="">Select Rider</option>
                                                    <?php 
                                                    $result_deliverymen->data_seek(0);
                                                    while ($dm = $result_deliverymen->fetch_assoc()): ?>
                                                        <option value="<?php echo $dm['id']; ?>" <?php echo ($row['deliverymanId'] == $dm['id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($dm['name']); ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                                <button type="submit" class="btn-assign">Assign</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color: var(--text-muted); font-weight: 500;">Delivered by: <?php echo htmlspecialchars($row['deliveryman_name']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="edit_order.php?id=<?php echo $row['id']; ?>" class="btn-assign" style="background:#3498db; text-decoration:none;">Edit</a>
                                        <a href="delete_order.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Delete this order completely?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">No orders found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>