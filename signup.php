<?php
session_start();
include('db_connection.php');

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password != $confirm_password) {
        echo "<script>alert('Passwords do not match. Please try again.'); window.location.href='signup.php';</script>";
        exit();
    }

    $check_sql = "SELECT email FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>alert('Email already exists. Please try again with a different email.'); window.location.href='signup.php';</script>";
        $check_stmt->close();
        $conn->close();
        exit();
    }
    $check_stmt->close();

    $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        $_SESSION['user'] = ['id' => $user_id, 'name' => $name, 'email' => $email, 'role' => 'customer'];
        $_SESSION['user_id'] = $user_id;
        $_SESSION['nav'] = 1;
        header("Location: index.php");
        exit();
    } else {
        echo "<script>alert('Error: Could not register user.'); window.location.href='signup.php';</script>";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .auth-container { display: flex; justify-content: center; align-items: center; min-height: 80vh; background-color: var(--light-bg); padding: 40px 20px; }
        .auth-card { background: var(--white); padding: 40px; border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); width: 100%; max-width: 500px; }
        .auth-card h2 { text-align: center; margin-bottom: 30px; color: var(--text-main); font-size: 2rem; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-muted); }
        .form-control { width: 100%; padding: 12px 15px; border: 1px solid #dcdde1; border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 1rem; transition: var(--transition); }
        .form-control:focus { border-color: var(--primary-color); outline: none; box-shadow: 0 0 0 3px rgba(255, 71, 87, 0.1); }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <section class="auth-container">
        <div class="auth-card">
            <h2>Create an Account</h2>
            <form action="signup.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="john@example.com" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Create a password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                </div>
                <button type="submit" name="submit" class="btn-full" style="border:none; cursor:pointer; font-size:1.1rem; margin-top: 10px;">Sign Up</button>
            </form>
            <p class="text-center" style="margin-top: 20px; color: var(--text-muted);">
                Already have an account? <a href="login.php" style="color: var(--primary-color); font-weight: 600;">Login</a>
            </p>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>