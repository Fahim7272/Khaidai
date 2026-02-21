<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$error_message = "";
$success_message = "";

if (isset($_POST['submit'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stored_password = $row['password'];

    if ($current_password === $stored_password) {
        if ($new_password === $confirm_new_password) {
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_password, $user_id);
            if ($stmt->execute()) {
                $success_message = "Password changed successfully.";
            } else {
                $error_message = "Failed to change password. Please try again.";
            }
        } else {
            $error_message = "New passwords do not match.";
        }
    } else {
        $error_message = "Incorrect current password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .form-container { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 70vh; 
            padding: 40px 20px; 
        }
        .form-card { 
            background: var(--white); 
            padding: 40px; 
            border-radius: var(--radius-lg); 
            box-shadow: var(--shadow-soft); 
            width: 100%; 
            max-width: 500px; 
        }
        .form-card h2 { 
            text-align: center; 
            margin-bottom: 30px; 
            color: var(--text-main); 
        }
        .form-group { 
            margin-bottom: 20px; 
        }
        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 500; 
            color: var(--text-muted); 
        }
        .form-control { 
            width: 100%; 
            padding: 12px 15px; 
            border: 1px solid #dcdde1; 
            border-radius: 8px; 
            font-family: 'Poppins', sans-serif; 
            font-size: 1rem; 
            transition: var(--transition); 
        }
        .form-control:focus { 
            border-color: var(--primary-color); 
            outline: none; 
            box-shadow: 0 0 0 3px rgba(255, 71, 87, 0.1); 
        }
        .alert-toast { 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            font-weight: 500; 
            text-align: center; 
        }
        .alert-success { 
            background: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb; 
        }
        .alert-error { 
            background: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb; 
        }
        .btn-update {
            border: none; 
            cursor: pointer; 
            font-size: 1.1rem; 
            margin-top: 10px; 
            background: var(--dark-bg);
            width: 100%;
            padding: 15px 30px;
            border-radius: 8px;
            color: var(--white);
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-update:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: var(--text-muted);
            font-weight: 500;
            text-decoration: none;
            transition: var(--transition);
        }
        .back-link a:hover {
            color: var(--primary-color);
        }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <section class="form-container">
        <div class="form-card">
            <h2>Secure Your Account</h2>

            <?php if ($success_message): ?>
                <div class="alert-toast alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert-toast alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="change_password.php" method="POST">
                <div class="form-group">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_new_password">Confirm New Password:</label>
                    <input type="password" id="confirm_new_password" name="confirm_new_password" class="form-control" required>
                </div>
                
                <button type="submit" name="submit" class="btn-update">Update Password</button>
            </form>
            
            <div class="back-link">
                <a href="profile.php">&larr; Back to Profile</a>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>