<?php
session_start();
include('db_connection.php');

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password != $confirm_password) {
        echo "<script>alert('Passwords do not match. Please try again.'); window.location.href='signup.html';</script>";
        exit();
    }

    // Check if email already exists
    $check_sql = "SELECT email FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>alert('Email already exists. Please try again with a different email.'); window.location.href='signup.html';</script>";
        $check_stmt->close();
        $conn->close();
        exit();
    }

    $check_stmt->close();

    // Insert user into the database
    $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        $_SESSION['user'] = [
            'id' => $user_id,
            'name' => $name,
            'email' => $email
        ];

        header("Location: profile.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
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
    <title>Signup</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login&signup.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <section class="signup">
        <div class="container text-center">
            <div class="signup-form">
                <h2>Signup</h2>
                <form action="signup.php" method="post">
                    <div class="input-container">
                        <label for="name">Full Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="input-container">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="input-container">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="input-container">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <input type="submit" name="submit" value="Signup" class="btn btn-primary">
                </form>
                <p>Already have an account? <a href="login.html">Login</a></p>
            </div>
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
