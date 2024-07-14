<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
} else {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// Fetch user details from the database
$sql = "SELECT id, name, email, location, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Update session user with fetched details (optional)
    $_SESSION['user'] = $row;
    $user = $row; // Update $user with fetched details
} else {
    $_SESSION['error_message'] = "User details not found.";
    header("Location: login.html"); // Redirect if user details not found
    exit();
}
$stmt->close();

// Fetch total number of reviews given by the user
$reviews_sql = "SELECT COUNT(*) AS total_reviews FROM review_food WHERE user_id = ?";
$reviews_stmt = $conn->prepare($reviews_sql);
$reviews_stmt->bind_param("i", $user['id']);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();
$total_reviews = $reviews_result->fetch_assoc()['total_reviews'];
$reviews_stmt->close();

// Fetch total number of food items ordered by the user
$orders_sql = "SELECT COUNT(*) AS total_orders FROM orders WHERE user_id = ?";
$orders_stmt = $conn->prepare($orders_sql);
$orders_stmt->bind_param("i", $user['id']);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();
$total_orders = $orders_result->fetch_assoc()['total_orders'];
$orders_stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/profile.css">
    <style>
        .cards {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 200px;
        }
        .card h3 {
            margin-bottom: 10px;
        }
        .card p {
            font-size: 24px;
            color: #333;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <section class="food-search text-center">
        <div class="container">
            <h3 class="text-white"><?php echo htmlspecialchars($user['name']); ?></h3>
        </div>
    </section>

    <section class="profile">
        <div class="container">
            <div class="profile-info">
                <div class="profile-img">
                    <?php if (!empty($user['profile_picture'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_picture']); ?>" alt="Profile Picture">
                    <?php else: ?>
                        <img src="images/default-profile-img.jpg" alt="Profile Picture">
                    <?php endif; ?>
                </div>
                <div class="profile-details">
                    <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                    <?php if (!empty($user['location'])): ?>
                        <p>Location: <?php echo htmlspecialchars($user['location']); ?></p>
                    <?php endif; ?>
                    <?php
                    // Display error or success messages if any
                    if (isset($_SESSION['error_message'])) {
                        echo '<p class="error-message">' . $_SESSION['error_message'] . '</p>';
                        unset($_SESSION['error_message']);
                    } elseif (isset($_SESSION['success_message'])) {
                        echo '<p class="success-message">' . $_SESSION['success_message'] . '</p>';
                        unset($_SESSION['success_message']);
                    }
                    ?>
                </div>
            </div>
            <div class="cards">
                <div class="card">
                    <h3>Total Reviews Given</h3>
                    <p><?php echo $total_reviews; ?></p>
                </div>
                <div class="card">
                    <h3>Total Food Ordered</h3>
                    <p><?php echo $total_orders; ?></p>
                </div>
            </div>
            <div class="cards">
                <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
                <a href="change_password.php" class="btn btn-primary">Change Password</a>
            </div>

            
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
