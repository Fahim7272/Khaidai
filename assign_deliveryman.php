<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $deliveryman_id = $_POST['deliveryman_id'];

    // Update the deliverymanId in the orders table
    $sql = "UPDATE orders SET deliverymanId = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $deliveryman_id, $order_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Deliveryman assigned successfully.";
    } else {
        $_SESSION['message'] = "Failed to assign deliveryman. Please try again.";
    }

    $stmt->close();
    $conn->close();

    // Redirect back to manage_orders.php
    header("Location: manage_orders.php");
    exit();
} else {
    // Redirect back to manage_orders.php if accessed directly
    header("Location: manage_orders.php");
    exit();
}
?>
