<?php
session_start();
include('db_connection.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$admin = $_SESSION['admin'];

// Fetch total counts from the database
$food_count_sql = "SELECT COUNT(*) AS total_food FROM items";
$user_count_sql = "SELECT COUNT(*) AS total_users FROM users";
$order_count_sql = "SELECT COUNT(*) AS total_orders FROM orders";

$food_count_result = $conn->query($food_count_sql);
$user_count_result = $conn->query($user_count_sql);
$order_count_result = $conn->query($order_count_sql);

$total_food = $food_count_result->fetch_assoc()['total_food'];
$total_users = $user_count_result->fetch_assoc()['total_users'];
$total_orders = $order_count_result->fetch_assoc()['total_orders'];

// Calculate order increase
$previous_total_orders = isset($_SESSION['previous_total_orders']) ? $_SESSION['previous_total_orders'] : 0;
$order_increase = $total_orders - $previous_total_orders;

// Update session with the current total orders
$_SESSION['previous_total_orders'] = $total_orders;

// Calculate user increase
$previous_total_users = isset($_SESSION['previous_total_users']) ? $_SESSION['previous_total_users'] : 0;
$user_increase = $total_users - $previous_total_users;

// Update session with the current total users
$_SESSION['previous_total_users'] = $total_users;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        /* Add CSS styling here if necessary */
        .admin-dashboard {
            padding: 20px;
        }
        .content {
            text-align: center;
        }
        .dashboard-cards {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            width: 250px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .card h3 {
            margin-bottom: 10px;
        }
        .card p {
            font-size: 1.5em;
            margin: 0;
        }
        .card .increase {
            color: green;
            font-size: 0.75em;
        }
        .card .decrease {
            color: red;
            font-size: 0.75em;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <section class="food-search text-center">
        <div class="container">
            <h1>Welcome, <?php echo htmlspecialchars($admin['name']); ?>!</h1>
            <p>From here, you can manage the website content, users, orders, and more.</p>
        </div>
    </section>
    <section class="admin-content">
        <div class="content">
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Total Food Items</h3>
                    <p><?php echo $total_food; ?></p>
                </div>
                <div class="card">
                    <h3>Total Users</h3>
                    <p><?php echo $total_users; ?></p>
                    <?php if ($user_increase > 0): ?>
                        <p class="increase">(+<?php echo $user_increase; ?> since last visit)</p>
                    <?php elseif ($user_increase < 0): ?>
                        <p class="decrease">(<?php echo $user_increase; ?> since last visit)</p>
                    <?php else: ?>
                        <p class="decrease">(No change since last visit)</p>
                    <?php endif; ?>
                </div>
                <div class="card">
                    <h3>Total Orders</h3>
                    <p><?php echo $total_orders; ?></p>
                    <?php if ($order_increase > 0): ?>
                        <p class="increase">(+<?php echo $order_increase; ?> since last visit)</p>
                    <?php elseif ($order_increase < 0): ?>
                        <p class="decrease">(<?php echo $order_increase; ?> since last visit)</p>
                    <?php else: ?>
                        <p class="decrease">(No change since last visit)</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php include 'footer.php'; ?>
</body>
</html>
