<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

include('db_connection.php');

$user_id = $_SESSION['user']['id'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['selected_items']) && !empty($_POST['selected_items'])) {
        
        $selected_items = $_POST['selected_items'];;
        $selected_items = array_map('intval', $selected_items); 

        
        $sql_update_items = "UPDATE cart_items SET ordered = 1 WHERE user_id = ? AND id IN (". implode(',', $selected_items) .")";
        $stmt_update_items = $conn->prepare($sql_update_items);
        $stmt_update_items->bind_param("i",  $user_id);

        
        if ($stmt_update_items->execute()) {
            
            $success_message = "Selected items ordered successfully!";
        } else {
            
            $error_message = "Error ordering items: " . $conn->error;
        }

       
        $stmt_update_items->close();
    } else {
       
        $error_message = "No items selected to order";
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="css/style.css">
    
</head>
<body>
    <?php include 'navbar.php' ;; ?>
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
    <?php include 'footer.php' ; ?>
</body>
</html>
