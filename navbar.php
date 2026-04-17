<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/navbar.css">

<nav class="navbar modern-navbar">
    <div class="navcontainer">
        <div class="logo">
            <a href="index.php" title="Home">
                <span class="brand-name">Khai<span class="highlight">Dai</span></span>
            </a>
        </div>
        <div class="menu">
            <ul class="nav-links">
                <?php if (isset($_SESSION['nav']) && $_SESSION['nav'] == 0): ?>
                    <li><a href="admin_dashboard.php">Dashboard</a></li>
                    <li><a href="manage_food.php">Menu</a></li>
                    <li><a href="manage_users.php">Users</a></li>
                    <li><a href="manage_orders.php">Orders</a></li>
                    <li><a href="logout.php" class="btn-nav-outline">Logout</a></li>
                <?php elseif (isset($_SESSION['nav']) && $_SESSION['nav'] == 1): ?>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="foods.php">Menu</a></li>
                    <li><a href="my_orders.php">Orders</a></li>
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php" class="btn-nav-outline">Logout</a></li>
                <?php elseif (isset($_SESSION['nav']) && $_SESSION['nav'] == 2): ?>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="delivery_dashboard.php">Dashboard</a></li>
                    <li><a href="logout.php" class="btn-nav-outline">Logout</a></li>
                <?php else: ?>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="foods.php">Menu</a></li>
                    <li><a href="login.php" class="btn-nav-ghost">Login</a></li>
                    <li><a href="signup.php" class="btn-nav-solid">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</nav>