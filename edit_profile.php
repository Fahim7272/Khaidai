<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$success_message = $error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle Text Details Update
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $location = $_POST['location']; 

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Invalid email format";
        } else {
            $sql = "UPDATE users SET name=?, email=?, location=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $name, $email, $location, $user['id']);

            if ($stmt->execute()) {
                $success_message = "Profile details updated successfully!";
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['email'] = $email;
                $_SESSION['user']['location'] = $location; 
            } else {
                $error_message = "Error updating profile: " . $conn->error;
            }
            $stmt->close();
        }
    }

    // Handle Profile Picture Update
    if (isset($_POST['update_picture'])) {
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
            $sql = "UPDATE users SET profile_picture=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("bi", $profile_picture, $user['id']);
            $stmt->send_long_data(0, $profile_picture);
            
            if ($stmt->execute()) {
                $success_message = "Profile picture updated successfully!";
            } else {
                $error_message = "Failed to update picture.";
            }
            $stmt->close();
        } else {
            $error_message = "Please select a valid image.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .edit-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 30px; max-width: 1000px; margin: 0 auto; align-items: start; }
        .form-card { background: var(--white); padding: 40px; border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); }
        .form-card h3 { margin-bottom: 25px; color: var(--text-main); border-bottom: 2px solid var(--light-bg); padding-bottom: 15px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-muted); }
        .form-control { width: 100%; padding: 12px 15px; border: 1px solid #dcdde1; border-radius: 8px; font-family: 'Poppins', sans-serif; transition: var(--transition); }
        .form-control:focus { border-color: var(--primary-color); outline: none; }
        @media (max-width: 768px) { .edit-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <section class="hero-section" style="height: 25vh; min-height: 200px;">
        <div class="hero-overlay"></div>
        <div class="container hero-content text-center">
            <h1 class="hero-title" style="font-size: 2.2rem;">Edit Your Profile</h1>
        </div>
    </section>

    <section class="section-padding">
        <div class="container">
            <?php if ($success_message): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; max-width: 1000px; margin: 0 auto 30px;"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; max-width: 1000px; margin: 0 auto 30px;"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="edit-grid">
                <div class="form-card text-center">
                    <h3>Profile Picture</h3>
                    <img src="images/default-profile-img.jpg" alt="Avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; margin-bottom: 20px; border: 3px solid var(--light-bg);">
                    <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <input type="file" name="profile_picture" class="form-control" accept="image/*" required>
                        </div>
                        <button type="submit" name="update_picture" class="btn-nav-outline" style="width: 100%; padding: 10px; border-radius: 8px; cursor: pointer;">Upload Picture</button>
                    </form>
                </div>

                <div class="form-card">
                    <h3>Personal Details</h3>
                    <form action="edit_profile.php" method="POST">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Delivery Address</label>
                            <input type="text" name="location" class="form-control" placeholder="e.g. Banani, Road 11" value="<?php echo isset($user['location']) ? htmlspecialchars($user['location']) : ''; ?>">
                        </div>
                        <button type="submit" name="update_profile" class="btn-primary" style="width: 100%; padding: 12px; border-radius: 8px; border: none; cursor: pointer; font-size: 1.1rem; margin-top: 10px;">Save Changes</button>
                    </form>
                </div>
            </div>
            
            <div class="text-center" style="margin-top: 30px;">
                <a href="profile.php" style="color: var(--text-muted); font-weight: 500;">&larr; Back to Profile Dashboard</a>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>