<?php
session_start();
include('db_connection.php');

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the credentials match the admin credentials
    if ($email === 'admin@gmail.com' && $password === '1234') {
        $_SESSION['admin'] = [
            'id' => 1, // Assuming admin ID is 1
            'name' => 'Admin',
            'email' => $email
        ];
        $_SESSION['nav'] = 0;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        // Verify credentials against the deliverymen table
        $sql = "SELECT id, name, email, password FROM deliverymen WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if ($password === $row['password']) {
                $_SESSION['user'] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'role' => 'delivery'
                ];
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['nav'] = 2;
                header("Location: delivery_dashboard.php");
                exit();
            } else {
                $error_message = "Invalid password.";
            }
        } else {
            // Verify credentials against the users table
            $sql = "SELECT id, name, email, password FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                if ($password === $row['password']) {
                    $_SESSION['user'] = [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'role' => 'user'
                    ];
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['nav'] = 1;
                    header("Location: profile.php");
                    exit();
                } else {
                    $error_message = "Invalid password.";
                }
            } else {
                $error_message = "User not found.";
            }
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login&signup.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <section class="login">
        <div class="container text-center">
            <form action="login.php" method="post" class="login-form">
                <fieldset>
                    <legend>Login</legend>
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <div class="input-container">
                        <label for="email">Email:</label>
                        <input type="text" id="email" name="email" class="input-responsive" required>
                    </div>
                    <div class="input-container">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" class="input-responsive" required>
                    </div>
                    <input type="submit" name="submit" value="Login" class="btn btn-primary">
                </fieldset>
            </form>
            <p>Don't have an account? <a href="signup.php">Signup</a></p>
        </div>
    </section>
    <section class="social">
        <div class="container text-center">
            <ul>
                <li><a href="#"><img src="https://img.icons8.com/fluent/50/000000/facebook-new.png"/></a></li>
                <li><a href="#"><img src="https://img.icons8.com/fluent/48/000000/instagram-new.png"/></a></li>
                <li><a href="#"><img src="https://img.icons8.com/fluent/48/000000/twitter.png"/></a></li>
            </ul>
        </div>
    </section>
    <section class="footer">
        <div class="container text-center">
            <p>All rights reserved - KhaiDai</p>
        </div>
    </section>
</body>
</html>
