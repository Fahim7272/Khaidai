?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// Define variables and initialize with empty values
$name = $email = $current_password = $new_password = "";
$name_err = $email_err = $current_password_err = $new_password_err = "";
$success_message = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and process profile update
    if (isset($_POST['update_profile'])) {
        // Validate name
        if (empty(trim($_POST["name"]))) {
            $name_err = "Please enter a name.";
        } else {
            $name = trim($_POST["name"]);
        }

        // Validate email
        if (empty(trim($_POST["email"]))) {
            $email_err = "Please enter an email.";
        } else {
            $email = trim($_POST["email"]);
        }

        // Check input errors before updating in database
        if (empty($name_err) && empty($email_err)) {
            $sql = "UPDATE admins SET name = ?, email = ? WHERE id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssi", $name, $email, $_SESSION['admin']['id']);
                if ($stmt->execute()) {
                    $success_message = "Profile updated successfully.";
                    $_SESSION['admin']['name'] = $name;
                    $_SESSION['admin']['email'] = $email;
                } else {
                    echo "Something went wrong. Please try again later.";
                }
                $stmt->close();
            }
        }
    }

    // Validate and process password change
    if (isset($_POST['change_password'])) {
        // Validate current password
        if (empty(trim($_POST["current_password"]))) {
            $current_password_err = "Please enter your current password.";
        } else {
            $current_password = trim($_POST["current_password"]);
        }

        // Validate new password
        if (empty(trim($_POST["new_password"]))) {
            $new_password_err = "Please enter a new password.";
        } else {
            $new_password = trim($_POST["new_password"]);
        }

        // Check current password and update new password in database
        if (empty($current_password_err) && empty($new_password_err)) {
            $sql = "SELECT password FROM admins WHERE id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $_SESSION['admin']['id']);
                if ($stmt->execute()) {
                    $stmt->store_result();
                    if ($stmt->num_rows == 1) {
                        $stmt->bind_result($hashed_password);
                        if ($stmt->fetch()) {
                            if ($current_password === $hashed_password) {
                                $sql = "UPDATE admins SET password = ? WHERE id = ?";
                                if ($stmt = $conn->prepare($sql)) {
                                    $stmt->bind_param("si", $new_password, $_SESSION['admin']['id']);
                                    if ($stmt->execute()) {
                                        $success_message = "Password changed successfully.";
                                    } else {
                                        echo "Something went wrong. Please try again later.";
                                    }
                                }
                            } else {
                                $current_password_err = "The current password you entered was not valid.";
                            }
                        }
                    }
                } else {
                    echo "Something went wrong. Please try again later.";
                }
                $stmt->close();
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="main-content">
        <div class="wrapper">
            <h1>Admin Settings</h1>
            <br><br>

            <?php 
            if(!empty($success_message)){
                echo '<div class="success">' . $success_message . '</div>';
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <fieldset>
                    <legend>Update Profile</legend>
                    <div class="input-container">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" class="input-responsive" value="<?php echo htmlspecialchars($_SESSION['admin']['name']); ?>" required>
                        <span class="error"><?php echo $name_err; ?></span>
                    </div>
                    <div class="input-container">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" class="input-responsive" value="<?php echo htmlspecialchars($_SESSION['admin']['email']); ?>" required>
                        <span class="error"><?php echo $email_err; ?></span>
                    </div>
                    <input type="submit" name="update_profile" value="Update Profile" class="btn btn-primary">
                </fieldset>
            </form>

            <br><br>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <fieldset>
                    <legend>Change Password</legend>
                    <div class="input-container">
                        <label for="current_password">Current Password:</label>
                        <input type="password" id="current_password" name="current_password" class="input-responsive" required>
                        <span class="error"><?php echo $current_password_err; ?></span>
                    </div>
                    <div class="input-container">
                        <label for="new_password">New Password:</label>
                        <input type="password" id="new_password" name="new_password" class="input-responsive" required>
                        <span class="error"><?php echo $new_password_err; ?></span>
                    </div>
                    <input type="submit" name="change_password" value="Change Password" class="btn btn-primary">
                </fieldset>
            </form>

        </div>
    </div>
    <section class="footer">
        <div class="container text-center">
            <p>All rights reserved - KhaiDai</p>
        </div>
    </section>
</body>
</html>
