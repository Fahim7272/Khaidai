<?php
session_start();

// Check if user is logged in (session exists)
if (!isset($_SESSION['user'])) {
    // Redirect to login if session doesn't exist
    header("Location: login.html");
    exit();
}

include('db_connection.php');

// Retrieve user ID from session
$user_id = $_SESSION['user']['id'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if selected_items array is set and not empty
    if (isset($_POST['selected_items']) && !empty($_POST['selected_items'])) {
        // Sanitize and validate selected item IDs (you may need additional validation as per your application logic)
        $selected_items = $_POST['selected_items'];
        $selected_items = array_map('intval', $selected_items); // Convert each element to integer for security

        // Prepare SQL statement to update ordered status of selected items
        $sql_update_items = "UPDATE cart_items SET ordered = 1 WHERE user_id = ? AND id IN (". implode(',', $selected_items) .")";
        $stmt_update_items = $conn->prepare($sql_update_items);
        $stmt_update_items->bind_param("i", $user_id);

        // Execute update query
        if ($stmt_update_items->execute()) {
            // Orders successfully updated
            $success_message = "Selected items ordered successfully!";
        } else {
            // Error updating orders
            $error_message = "Error ordering items: " . $conn->error;
        }

        // Close statement
        $stmt_update_items->close();
    } else {
        // No items selected
        $error_message = "No items selected to order.";
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Add any additional stylesheets or meta tags as needed -->
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h2>Order Confirmation</h2>
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <p><a href="cart.php">&laquo; Back to Cart</a></p>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
