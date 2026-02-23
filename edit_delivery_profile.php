<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'delivery') {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$success_message = $error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $location = $_POST['location']; 

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Invalid email format";
        } else {
            // Updating the deliverymen table specifically
            $sql = "UPDATE deliverymen SET name=?, email=?, location=? WHERE id=?";
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Rider Profile - KhaiDai</title>
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
            transition: var(--transition); 
        }
        .form-control:focus { 
            border-color: var(--primary-color); 
            outline: none; 
            box-shadow: 0 0 0 3px rgba(255, 71, 87, 0.1);
        }
        .success-message { 
            background: #d4edda; 
            color: #155724; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            text-align: center;
            font-weight: 500;
        }
        .error-message { 
            background: #f8d7da; 
            color: #721c24; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            text-align: center;
            font-weight: 500;
        }
        .btn-save {
            border: none; 
            cursor: pointer; 
            font-size: 1.1rem; 
            margin-top: 10px;
            width: 100%;
            padding: 15px 30px;
            background: var(--primary-color);
            color: var(--white);
            border-radius: 8px;
            font-weight: 600;
            transition: var(--transition);
        }
        .btn-save:hover {
            background: var(--primary-dark);
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
            <h2>Edit Rider Details</h2>
            
            <?php if ($success_message): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="edit_delivery_profile.php" method="POST">
                <div class="form-group">
                    <label>Full Name:</label>
                    <input type="text" 
                           name="name" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($user['name']); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label>Email Address:</label>
                    <input type="email" 
                           name="email" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label>Assigned Delivery Zone / Address:</label>
                    <input type="text" 
                           name="location" 
                           class="form-control" 
                           placeholder="e.g. Mirpur Area" 
                           value="<?php echo isset($user['location']) ? htmlspecialchars($user['location']) : ''; ?>">
                </div>

                <button type="submit" name="update_profile" class="btn-save">Save Changes</button>
            </form>
            
            <div class="back-link">
                <a href="delivery_profile.php">&larr; Back to Profile</a>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>