<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'delivery') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['delivery_status'])) {
    $order_id = intval($_POST['order_id']);
    $delivery_status = $_POST['delivery_status'];

    $valid_statuses = ['Pending', 'Out for Delivery', 'Delivered'];
    if (!in_array($delivery_status, $valid_statuses)) {
        $_SESSION['error_message'] = "Invalid delivery status.";
        header("Location: delivery_dashboard.php");
        exit();
    }

    $update_query = "UPDATE orders SET delivery_status = ?";
    if ($delivery_status === 'Out for Delivery') {
        $update_query .= ", ofd_time = NOW()";
    } elseif ($delivery_status === 'Delivered') {
        $update_query .= ", delivered_time = NOW()";
    }
    $update_query .= " WHERE id = ? AND deliverymanId = ?";

    $stmt = $conn->prepare($update_query);
    $deliveryman_id = $_SESSION['user']['id'];
    $stmt->bind_param("sii", $delivery_status, $order_id, $deliveryman_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['success_message'] = "Delivery status updated successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to update delivery status.";
    }

    $stmt->close();
} else {
    $_SESSION['error_message'] = "Invalid request.";
}

$conn->close();

header("Location: delivery_dashboard.php");
exit();
?>
