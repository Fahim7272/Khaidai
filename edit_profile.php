<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
                $success_message = "Profile updated successfully!";
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['email'] = $email;
                $_SESSION['user']['location'] = $location; 

                $error_message = "Error updating profile: " . $conn->error;
            }

            $stmt->close();
        }
    }

    if (isset($_POST['update_picture'])) {
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
            $sql = "UPDATE users SET profile_picture=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("bi", $profile_picture, $user['id']);

            if ($stmt->execute()) {
                $success_message = "Profile picture updated successfully!";
                $_SESSION['user']['profile_picture'] = $profile_picture; 
            } else {
                $error_message = "Error updating profile picture: " . $conn->error;
            }

            $stmt->close();
        } else {
            $error_message = "Please select a valid image file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/edit_profile.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <section class="edit-profile">
        <div class="container">
            <h2>Edit Profile</h2>

            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="edit_profile.php" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?php echo isset($user['location']) ? htmlspecialchars($user['location']) : ''; ?>">

                <input type="submit" name="update_profile" value="Save Changes">
            </form>

            
        </div>
    </section>

    <section class="social">
        <div class="container text-center">
            <ul>
                <li><a href="#"><img src="https://img.icons8.com/fluent/50/000000/facebook-new.png" alt="Facebook"></a></li>
                <li><a href="#"><img src="https://img.icons8.com/fluent/48/000000/instagram-new.png" alt="Instagram"></a></li>
                <li><a href="#"><img src="https://img.icons8.com/fluent/48/000000/twitter.png" alt="Twitter"></a></li>
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
