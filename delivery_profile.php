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
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deliveryman Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/profile.css">
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
                    
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
                        <input type="file" name="profile_picture" accept="image/*">
                        <button type="submit" class="btn btn-primary">Update Profile Picture</button>
                    </form>
                    <?php
                    
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
            <div class="profile-actions">
                <a href="edit_delivery_profile.php" class="btn btn-primary">Edit Profile</a>
                <a href="change_password.php" class="btn btn-secondary">Change Password</a>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
