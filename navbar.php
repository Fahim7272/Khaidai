<head>
<link rel="stylesheet" href="css/navbar.css">
</head>
<nav class="navbar">
    <div class="navcontainer">
        <div class="logo">
            <a href="#" title="Logo">
                <img src="images/logo.jpg" alt="Restaurant Logo" class="logo">
            </a>
        </div>
        <div class="menu">
            <ul>
                <?php if (isset($_SESSION['nav']) && $_SESSION['nav'] == 0): ?>
                    
                    <li><a href="admin_dashboard.php">Dashboard</a></li>
                    <li><a href="manage_food.php">Food Items</a></li>
                    <li><a href="manage_users.php">Users</a></li>
                    <li><a href="manage_orders.php">Orders</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php elseif (isset($_SESSION['nav']) && $_SESSION['nav'] == 1): ?>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="foods.php">Foods</a></li>
                    <li><a href="my_orders.php">Orders</a></li>
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php elseif (isset($_SESSION['nav']) && $_SESSION['nav'] == 2): ?>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="delivery_dashboard.php">Dashboard</a></li>
                    
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="index.php">Home</a></li>
                    
                    <li><a href="foods.php">Foods</a></li>
                    
                    <li><a href="login.php">Login</a></li>
                    <li><a href="signup.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</nav>
