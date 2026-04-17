<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

$name = $email = $current_password = $new_password = "";
$error_message = $success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_profile'])) {
        $name = trim($_POST["name"]);
        $email = trim($_POST["email"]);

        if (empty($name) || empty($email)) {
            $error_message = "Name and Email are required.";
        } else {
            $sql = "UPDATE admins SET name = ?, email = ? WHERE id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssi", $name, $email, $_SESSION['admin']['id']);
                if ($stmt->execute()) {
                    $success_message = "Profile updated successfully.";
                    $_SESSION['admin']['name'] = $name;
                    $_SESSION['admin']['email'] = $email;
                }
                $stmt->close();
            }
        }
    }

    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];

        $sql = "SELECT password FROM admins WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $_SESSION['admin']['id']);
            $stmt->execute();
            $stmt->bind_result($db_password);
            $stmt->fetch();
            $stmt->close();

            if ($current_password === $db_password) {
                $update_sql = "UPDATE admins SET password = ? WHERE id = ?";
                if ($update_stmt = $conn->prepare($update_sql)) {
                    $update_stmt->bind_param("si", $new_password, $_SESSION['admin']['id']);
                    if ($update_stmt->execute()) {
                        $success_message = "Password changed successfully.";
                    }
                    $update_stmt->close();
                }
            } else {
                $error_message = "Incorrect current password.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .settings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; max-width: 1000px; margin: 0 auto; align-items: start; }
        .form-card { background: var(--white); padding: 40px; border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); }
        .form-card h3 { margin-bottom: 25px; color: var(--text-main); border-bottom: 2px solid var(--light-bg); padding-bottom: 15px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-muted); }
        .form-control { width: 100%; padding: 12px; border: 1px solid #dcdde1; border-radius: 8px; font-family: 'Poppins', sans-serif; transition: var(--transition); }
        .form-control:focus { border-color: var(--primary-color); outline: none; }
        @media (max-width: 768px) { .settings-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <section class="hero-section" style="height: 25vh; min-height: 200px;">
        <div class="hero-overlay"></div>
        <div class="container hero-content text-center">
            <h1 class="hero-title" style="font-size: 2.2rem;">Account Settings</h1>
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

            <div class="settings-grid">
                <div class="form-card">
                    <h3>Update Profile</h3>
                    <form action="settings.php" method="POST">
                        <div class="form-group">
                            <label>Admin Name</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($_SESSION['admin']['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Admin Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['admin']['email']); ?>" required>
                        </div>
                        <button type="submit" name="update_profile" class="btn-primary" style="width: 100%; padding: 12px; border-radius: 8px; border: none; cursor: pointer; font-size: 1.1rem; margin-top: 10px;">Save Changes</button>
                    </form>
                </div>

                <div class="form-card">
                    <h3>Security</h3>
                    <form action="settings.php" method="POST">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <button type="submit" name="change_password" class="btn-nav-solid" style="background: var(--dark-bg); width: 100%; padding: 12px; border-radius: 8px; border: none; cursor: pointer; font-size: 1.1rem; margin-top: 10px;">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>