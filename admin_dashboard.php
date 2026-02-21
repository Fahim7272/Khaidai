<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$food_count_sql = "SELECT COUNT(*) AS total_food FROM items";
$user_count_sql = "SELECT COUNT(*) AS total_users FROM users";
$order_count_sql = "SELECT COUNT(*) AS total_orders FROM orders";

$total_food = $conn->query($food_count_sql)->fetch_assoc()['total_food'];
$total_users = $conn->query($user_count_sql)->fetch_assoc()['total_users'];
$total_orders = $conn->query($order_count_sql)->fetch_assoc()['total_orders'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .admin-header { 
            background: var(--dark-bg); 
            padding: 40px 0; 
            color: var(--white); 
            text-align: center; 
        }
        .admin-header h1 { 
            font-size: 2.5rem; 
            margin-bottom: 10px; 
        }
        .dashboard-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 30px; 
            margin-top: -40px; 
        }
        .stat-card { 
            background: var(--white); 
            padding: 40px 30px; 
            border-radius: var(--radius-lg); 
            box-shadow: var(--shadow-soft); 
            text-align: center; 
            transition: var(--transition); 
            border-top: 5px solid var(--primary-color); 
        }
        .stat-card:hover { 
            transform: translateY(-5px); 
            box-shadow: var(--shadow-hover); 
        }
        .stat-card:nth-child(2) { 
            border-top-color: #3498db; 
        }
        .stat-card:nth-child(3) { 
            border-top-color: #2ecc71; 
        }
        .stat-title { 
            font-size: 1.2rem; 
            color: var(--text-muted); 
            font-weight: 600; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            margin-bottom: 15px; 
        }
        .stat-number { 
            font-size: 3.5rem; 
            color: var(--text-main); 
            font-weight: 700; 
            line-height: 1; 
        }
        .admin-actions {
            text-align: center;
            margin-top: 50px;
        }
        .admin-actions .btn-primary,
        .admin-actions .btn-nav-outline {
            padding: 15px 30px;
            border-radius: 8px;
            display: inline-block;
            text-decoration: none;
            font-weight: 500;
        }
        .admin-actions .btn-primary {
            background: var(--primary-color);
            color: var(--white);
            border: none;
        }
        .admin-actions .btn-nav-outline {
            border: 2px solid var(--primary-color);
            color: var(--text-main);
            margin-left: 15px;
            background: transparent;
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <div class="admin-header">
        <div class="container">
            <h1>Admin Control Panel</h1>
            <p>Welcome back! Here is what is happening with your restaurant today.</p>
        </div>
    </div>

    <section class="section-padding">
        <div class="container">
            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-title">Menu Items</div>
                    <div class="stat-number"><?php echo $total_food; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Registered Users</div>
                    <div class="stat-number"><?php echo $total_users; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Total Orders</div>
                    <div class="stat-number"><?php echo $total_orders; ?></div>
                </div>
            </div>
            
            <div class="admin-actions">
                <a href="manage_food.php" class="btn-primary">Manage Food Menu</a>
                <a href="manage_orders.php" class="btn-nav-outline">View Recent Orders</a>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>