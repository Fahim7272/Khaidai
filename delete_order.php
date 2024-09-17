<?php
session_start();


if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: manage_orders.php");
    exit();
} else {
    header("Location: manage_orders.php");
    exit();
}
?>
