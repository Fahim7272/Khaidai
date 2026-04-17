<?php
session_start();

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
} else {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

$sql = "SELECT id, name, email, location, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['user'] = $row;
    $user = $row; 
} else {
    $_SESSION['error_message'] = "User details not found.";
    header("Location: login.php"); 
    exit();
}
$stmt->close();

$reviews_sql = "SELECT COUNT(*) AS total_reviews FROM review_food WHERE user_id = ?";
$reviews_stmt = $conn->prepare($reviews_sql);
$reviews_stmt->bind_param("i", $user['id']);
$reviews_stmt->execute();
$total_reviews = $reviews_stmt->get_result()->fetch_assoc()['total_reviews'];
$reviews_stmt->close();

$orders_sql = "SELECT COUNT(*) AS total_orders FROM orders WHERE user_id = ?";
$orders_stmt = $conn->prepare($orders_sql);
$orders_stmt->bind_param("i", $user['id']);
$orders_stmt->execute();
$total_orders = $orders_stmt->get_result()->fetch_assoc()['total_orders'];
$orders_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .profile-wrapper { max-width: 800px; margin: 60px auto; background: var(--white); border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); overflow: hidden; }
        .profile-header { background: var(--dark-bg); height: 150px; text-align: center; color: var(--white); padding-top: 30px; }
        .profile-body { padding: 0 40px 40px; text-align: center; }
        .profile-img-lg { width: 130px; height: 130px; border-radius: 50%; border: 5px solid var(--white); margin-top: -65px; background: var(--light-bg); object-fit: cover; box-shadow: var(--shadow-soft); }
        .profile-name { font-size: 2rem; color: var(--text-main); margin: 15px 0 5px; font-weight: 700; }
        .profile-meta { color: var(--text-muted); font-size: 1.1rem; margin-bottom: 25px; }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .stat-box { background: var(--light-bg); padding: 25px; border-radius: var(--radius-md); transition: var(--transition); }
        .stat-box:hover { transform: translateY(-3px); box-shadow: var(--shadow-soft); }
        .stat-number { font-size: 2.5rem; color: var(--primary-color); font-weight: 700; margin-bottom: 5px; line-height: 1; }
        .stat-label { color: var(--text-main); font-weight: 500; }
        .profile-actions { display: flex; justify-content: center; gap: 15px; }
        .alert-toast { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        @media (max-width: 600px) { .stats-grid { grid-template-columns: 1fr; } .profile-actions { flex-direction: column; } }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <section class="section-padding">
        <div class="container">
            <div class="profile-wrapper">
                <div class="profile-header">
                    <h2 style="font-weight: 400; letter-spacing: 1px;">Welcome Back!</h2>
                </div>
                
                <div class="profile-body">
                    <?php if ($user['profile_picture']): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_picture']); ?>" alt="Profile" class="profile-img-lg">
                    <?php else: ?>
                        <img src="images/default-profile-img.jpg" alt="Default Profile" class="profile-img-lg">
                    <?php endif; ?>
                    
                    <h1 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h1>
                    <div class="profile-meta">
                        📧 <?php echo htmlspecialchars($user['email']); ?><br>
                        📍 <?php echo !empty($user['location']) ? htmlspecialchars($user['location']) : '<span style="color:#e74c3c;">Location not set</span>'; ?>
                    </div>

                    <?php
                        if (isset($_SESSION['error_message'])) {
                            echo '<div class="alert-toast alert-error">' . $_SESSION['error_message'] . '</div>';
                            unset($_SESSION['error_message']);
                        } elseif (isset($_SESSION['success_message'])) {
                            echo '<div class="alert-toast alert-success">' . $_SESSION['success_message'] . '</div>';
                            unset($_SESSION['success_message']);
                        }
                    ?>

                    <div class="stats-grid">
                        <div class="stat-box">
                            <div class="stat-number"><?php echo $total_orders; ?></div>
                            <div class="stat-label">Total Orders</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number"><?php echo $total_reviews; ?></div>
                            <div class="stat-label">Reviews Given</div>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <a href="edit_profile.php" class="btn-primary" style="padding: 12px 30px; border-radius: 8px; text-decoration: none;">Edit Profile</a>
                        <a href="change_password.php" class="btn-nav-outline" style="padding: 12px 30px; border-radius: 8px; border: 2px solid var(--text-muted); color: var(--text-main); text-decoration: none;">Change Password</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>