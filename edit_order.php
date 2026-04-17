<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
include('db_connection.php');

if (isset($_POST['submit'])) {
    $id = $_POST['id']; $status = $_POST['status'];
    $sql = "UPDATE orders SET delivery_status = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql); $stmt->bind_param("si", $status, $id);
    $stmt->execute(); $stmt->close(); $conn->close();
    header("Location: manage_orders.php"); exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT id, delivery_status FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql); $stmt->bind_param("i", $id); $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc(); $stmt->close();
} else { header("Location: manage_orders.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Edit Order Status</title>
    <link rel="stylesheet" href="css/style.css"><link rel="stylesheet" href="css/modern.css">
    <style>
        .form-container{
            display:flex;
            justify-content:center;
            align-items:center;
            min-height:60vh;
        } 
        .form-card{
            background:var(--white);
            padding:40px;
            border-radius:var(--radius-lg);
            box-shadow:var(--shadow-soft);
            width:100%;max-width:500px;
            
        } 
        .form-control{
            width:100%;
            padding:12px;
            border:1px solid #ccc;
            border-radius:8px;
            margin-bottom:20px;
            font-family:'Poppins';
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    <section class="form-container section-padding">
        <div class="form-card">
            <h2 class="text-center" style="margin-bottom: 30px;">Update Order #<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></h2>
            <form action="edit_order.php" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($order['id']); ?>">
                <label style="font-weight: 500;">Delivery Status:</label>
                <select name="status" class="form-control">
                    <option value="Pending" <?php echo ($order['delivery_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Out for Delivery" <?php echo ($order['delivery_status'] == 'Out for Delivery') ? 'selected' : ''; ?>>Out for Delivery</option>
                    <option value="Delivered" <?php echo ($order['delivery_status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                </select>
                <button type="submit" name="submit" class="btn-primary" style="width: 100%; padding: 15px; border-radius: 8px; border:none; cursor:pointer;">Update Status</button>
            </form>
            <div class="text-center" style="margin-top: 20px;"><a href="manage_orders.php" style="color: var(--text-muted);">&larr; Back to Orders</a></div>
        </div>
    </section>
    <?php include 'footer.php'; ?>
</body>
</html>