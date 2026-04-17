<?php
session_start();
include('db_connection.php');
$error_message = "";

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email === 'admin@gmail.com' && $password === '1234') {
        $_SESSION['admin'] = ['id' => 1, 'name' => 'Admin', 'email' => $email];
        $_SESSION['nav'] = 0;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $sql = "SELECT id, name, email, password FROM deliverymen WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if ($password === $row['password']) {
                $_SESSION['user'] = ['id' => $row['id'], 'name' => $row['name'], 'email' => $row['email'], 'role' => 'delivery'];
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['nav'] = 2;
                header("Location: delivery_dashboard.php");
                exit();
            } else { $error_message = "Invalid password."; }
        } else {
            $sql = "SELECT id, name, email, password FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                if ($password === $row['password']) {
                    $_SESSION['user'] = ['id' => $row['id'], 'name' => $row['name'], 'email' => $row['email'], 'role' => 'customer'];
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['nav'] = 1;
                    header("Location: index.php");
                    exit();
                } else { $error_message = "Invalid password."; }
            } else { $error_message = "User not found."; }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .auth-container { display: flex; justify-content: center; align-items: center; min-height: 80vh; background-color: var(--light-bg); }
        .auth-card { background: var(--white); padding: 40px; border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); width: 100%; max-width: 450px; }
        .auth-card h2 { text-align: center; margin-bottom: 30px; color: var(--text-main); font-size: 2rem; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-muted); }
        .form-control { width: 100%; padding: 12px 15px; border: 1px solid #dcdde1; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 1rem; transition: var(--transition); }
        .form-control:focus { border-color: var(--primary-color); outline: none; box-shadow: 0 0 0 3px rgba(255, 71, 87, 0.1); }
        .error-alert { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <section class="auth-container">
        <div class="auth-card">
            <h2>Welcome Back</h2>
            <?php if ($error_message): ?>
                <div class="error-alert"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
                <button type="submit" name="submit" class="btn-full" style="border:none; cursor:pointer; font-size:1.1rem; margin-top: 10px;">Login</button>
            </form>
            <p class="text-center" style="margin-top: 20px; color: var(--text-muted);">
                Don't have an account? <a href="signup.php" style="color: var(--primary-color); font-weight: 600;">Sign Up</a>
            </p>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>