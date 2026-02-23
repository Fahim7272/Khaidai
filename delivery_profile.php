<?php
session_start();

if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'delivery') {
    $user = $_SESSION['user'];
} else {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

$sql = "SELECT id, name, email, location, profile_picture FROM deliverymen WHERE id = ?";
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

// Fetch total deliveries completed by this rider
$deliveries_sql = "SELECT COUNT(*) AS total_delivered FROM orders WHERE deliverymanId = ? AND delivery_status = 'Delivered'";
$deliveries_stmt = $conn->prepare($deliveries_sql);
$deliveries_stmt->bind_param("i", $user['id']);
$deliveries_stmt->execute();
$total_delivered = $deliveries_stmt->get_result()->fetch_assoc()['total_delivered'];
$deliveries_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider Profile - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .profile-wrapper { 
            max-width: 800px; 
            margin: 60px auto; 
            background: var(--white); 
            border-radius: var(--radius-lg); 
            box-shadow: var(--shadow-soft); 
            overflow: hidden; 
        }
        .profile-header { 
            background: var(--dark-bg); 
            height: 150px; 
            text-align: center; 
            color: var(--white); 
            padding-top: 30px; 
        }
        .profile-header h2 {
            font-weight: 400; 
            letter-spacing: 1px;
        }
        .profile-body { 
            padding: 0 40px 40px; 
            text-align: center; 
        }
        .profile-img-lg { 
            width: 130px; 
            height: 130px; 
            border-radius: 50%; 
            border: 5px solid var(--white); 
            margin-top: -65px; 
            background: var(--light-bg); 
            object-fit: cover; 
            box-shadow: var(--shadow-soft); 
        }
        .profile-name { 
            font-size: 2rem; 
            color: var(--text-main); 
            margin: 15px 0 5px; 
            font-weight: 700; 
        }
        .profile-meta { 
            color: var(--text-muted); 
            font-size: 1.1rem; 
            margin-bottom: 25px; 
        }
        .location-warning {
            color: #e74c3c;
        }
        .stat-box { 
            background: var(--light-bg); 
            padding: 25px; 
            border-radius: var(--radius-md); 
            max-width: 300px; 
            margin: 0 auto 30px; 
        }
        .stat-number { 
            font-size: 2.5rem; 
            color: var(--primary-color); 
            font-weight: 700; 
            margin-bottom: 5px; 
            line-height: 1; 
        }
        .stat-label { 
            color: var(--text-main); 
            font-weight: 500; 
        }
        .profile-actions { 
            display: flex; 
            justify-content: center; 
            gap: 15px; 
        }
        .profile-actions .btn-primary,
        .profile-actions .btn-nav-outline {
            padding: 12px 30px; 
            border-radius: 8px; 
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        .profile-actions .btn-primary {
            background: var(--primary-color);
            color: var(--white);
        }
        .profile-actions .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        .profile-actions .btn-nav-outline {
            border: 2px solid var(--text-muted); 
            color: var(--text-main);
            background: transparent;
        }
        .profile-actions .btn-nav-outline:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
        }
        .file-upload-form { 
            margin-bottom: 30px; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            gap: 10px; 
        }
        .file-upload-form input[type="file"] {
            font-family: 'Poppins', sans-serif;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 250px;
        }
        .upload-btn {
            padding: 8px 20px; 
            border-radius: 5px; 
            cursor: pointer;
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: 500;
            transition: var(--transition);
        }
        .upload-btn:hover {
            background: var(--primary-color);
            color: var(--white);
        }
        .error-message {
            background: #f8d7da; 
            color: #721c24; 
            padding: 10px; 
            border-radius: 8px; 
            margin-bottom: 20px;
        }
        .success-message {
            background: #d4edda; 
            color: #155724; 
            padding: 10px; 
            border-radius: 8px; 
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <section class="section-padding">
        <div class="container">
            <div class="profile-wrapper">
                <div class="profile-header">
                    <h2>Rider Profile</h2>
                </div>
                
                <div class="profile-body">
                    <?php if ($user['profile_picture']): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_picture']); ?>" 
                             alt="Profile" 
                             class="profile-img-lg">
                    <?php else: ?>
                        <img src="images/default-profile-img.jpg" 
                             alt="Default Profile" 
                             class="profile-img-lg">
                    <?php endif; ?>
                    
                    <h1 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h1>
                    <div class="profile-meta">
                        📧 <?php echo htmlspecialchars($user['email']); ?><br>
                        📍 <?php if (!empty($user['location'])): ?>
                                <?php echo htmlspecialchars($user['location']); ?>
                            <?php else: ?>
                                <span class="location-warning">Location not set</span>
                            <?php endif; ?>
                    </div>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="error-message">
                            <?php 
                            echo $_SESSION['error_message']; 
                            unset($_SESSION['error_message']);
                            ?>
                        </div>
                    <?php elseif (isset($_SESSION['success_message'])): ?>
                        <div class="success-message">
                            <?php 
                            // Fixed: was showing error_message in success block
                            echo $_SESSION['success_message']; 
                            unset($_SESSION['success_message']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="stat-box">
                        <div class="stat-number"><?php echo $total_delivered; ?></div>
                        <div class="stat-label">Successful Deliveries</div>
                    </div>

                    <form action="delivery_profile.php" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          class="file-upload-form">
                        <input type="file" 
                               name="profile_picture" 
                               accept="image/*">
                        <button type="submit" class="upload-btn">Upload New Picture</button>
                    </form>

                    <div class="profile-actions">
                        <a href="edit_profile.php" class="btn-primary">Edit Details</a>
                        <a href="change_password.php" class="btn-nav-outline">Change Password</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>