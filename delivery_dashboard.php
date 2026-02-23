<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'delivery') {
    header("Location: login.php");
    exit();
}

$delivery_id = $_SESSION['user']['id'];

$sql = "SELECT orders.id, users.name as user_name, users.location as user_address, items.name as item_name, items.image as item_image, orders.quantity, orders.total_price, orders.payment_method, orders.order_date, orders.delivery_status
        FROM orders JOIN users ON orders.user_id = users.id JOIN items ON orders.item_id = items.id
        WHERE orders.deliverymanId = ? ORDER BY orders.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $delivery_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Dashboard - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .table-container { 
            background: var(--white); 
            padding: 30px; 
            border-radius: var(--radius-lg); 
            box-shadow: var(--shadow-soft); 
            overflow-x: auto; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            min-width: 800px; 
        }
        th { 
            background: var(--dark-bg); 
            color: var(--white); 
            padding: 15px; 
            text-align: left; 
        }
        td { 
            padding: 15px; 
            border-bottom: 1px solid #eee; 
            vertical-align: middle; 
        }
        .select-status { 
            padding: 8px; 
            border-radius: 5px; 
            border: 1px solid #ccc; 
            font-family: 'Poppins', sans-serif; 
            outline: none; 
        }
        .btn-update { 
            background: var(--primary-color); 
            color: white; 
            border: none; 
            padding: 8px 15px; 
            border-radius: 5px; 
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }
        .btn-update:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        .delivery-hero {
            height: 250px; 
            min-height: 250px;
        }
        .delivery-hero p {
            color: #eccc68;
        }
        .success-message {
            background: #d4edda; 
            color: #155724; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            text-align: center;
            font-weight: 500;
        }
        .order-id {
            font-weight: 600;
        }
        .customer-info small {
            color: var(--text-muted);
            display: block;
        }
        .payment-info {
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        .delivered-badge {
            background: #d4edda; 
            color: #155724; 
            padding: 5px 10px; 
            border-radius: 20px; 
            font-weight: 600;
            display: inline-block;
        }
        .status-form {
            display: flex; 
            gap: 10px;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <div class="hero-section delivery-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content text-center">
            <h1 class="hero-title" style="font-size: 2.5rem;">Delivery Dashboard</h1>
            <p>Manage your assigned tasks and update delivery statuses.</p>
        </div>
    </div>

    <section class="section-padding">
        <div class="container">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success-message">
                    <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']); 
                    ?>
                </div>
            <?php endif; ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer & Address</th>
                            <th>Item</th>
                            <th>Total Amount</th>
                            <th>Status Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($orders)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No active deliveries assigned to you.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>
                                        <span class="order-id">#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></span>
                                    </td>
                                    <td class="customer-info">
                                        <strong><?php echo htmlspecialchars($order['user_name']); ?></strong>
                                        <small><?php echo htmlspecialchars($order['user_address']); ?></small>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($order['item_name']); ?> 
                                        (x<?php echo $order['quantity']; ?>)
                                    </td>
                                    <td>
                                        ৳ <?php echo number_format($order['total_price'], 2); ?> 
                                        <br>
                                        <span class="payment-info">(<?php echo $order['payment_method']; ?>)</span>
                                    </td>
                                    <td>
                                        <?php if ($order['delivery_status'] === 'Delivered'): ?>
                                            <span class="delivered-badge">Delivered ✓</span>
                                        <?php else: ?>
                                            <form action="update_delivery_status.php" 
                                                  method="POST" 
                                                  class="status-form">
                                                <input type="hidden" 
                                                       name="order_id" 
                                                       value="<?php echo $order['id']; ?>">
                                                <select name="delivery_status" 
                                                        class="select-status" 
                                                        required>
                                                    <option value="Pending" 
                                                        <?php echo $order['delivery_status'] === 'Pending' ? 'selected' : ''; ?>>
                                                        Pending
                                                    </option>
                                                    <option value="Out for Delivery" 
                                                        <?php echo $order['delivery_status'] === 'Out for Delivery' ? 'selected' : ''; ?>>
                                                        Out for Delivery
                                                    </option>
                                                    <option value="Delivered" 
                                                        <?php echo $order['delivery_status'] === 'Delivered' ? 'selected' : ''; ?>>
                                                        Delivered
                                                    </option>
                                                </select>
                                                <button type="submit" class="btn-update">Update</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>