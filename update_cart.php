<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST['quantity'] as $item_id => $quantity) {
        $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND item_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $quantity, $_SESSION['user_id'], $item_id);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();
header('Location: cart.php');
exit();
?>
