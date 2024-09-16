<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'delivery') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        $success_message = "Order status updated successfully.";
    } else {
        $error_message = "Failed to update order status. Please try again.";
    }
    $stmt->close();
}

$conn->close();
header("Location: delivery_dashboard.php");
exit();
?>
