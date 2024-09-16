<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$current_password = "";
$new_password = "";
$confirm_new_password = "";
$error_message = "";
$success_message = "";

if (isset($_POST['submit'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Fetch the current password from the database
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stored_password = $row['password'];

    if ($current_password === $stored_password) {
        if ($new_password === $confirm_new_password) {
            // Update the new password in the database
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
        $error_message = "Current password is incorrect.";
    }
    $stmt->close();
}

$conn-> close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/edit_profile.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <section class="change-password">
        <div class="container">
            <h2>Change Password</h2>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="change_password.php" method="POST">
                <div class="input-container">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="input-container">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="input-container">
                    <label for="confirm_new_password">Confirm New Password:</label>
                    <input type="password" id="confirm_new_password" name="confirm_new_password" required>
                </div>
                <input type="submit" name="submit" value="Change Password" class="btn">
            </form>
        </div>
    </section>

    <?php include 'footer.php'; ?>

</body>
</html>
