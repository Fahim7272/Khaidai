<?php
session_start();
include('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items from the database
$sql = "SELECT c.item_id, c.quantity, i.price 
        FROM cart_items c 
        JOIN items i ON c.item_id = i.id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Insert each cart item into the orders table
while ($row = $result->fetch_assoc()) {
    $item_id = $row['item_id'];
    $quantity = $row['quantity'];
    $price = $row['price'];
    $total_price = $quantity * $price;
    $payment_method = 'Cash on Delivery'; // You can change this as needed
    $order_date = date('Y-m-d H:i:s'); // Current date and time

    $insert_order_sql = "INSERT INTO orders (user_id, item_id, quantity, total_price, payment_method, order_date) 
                         VALUES (?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_order_sql);
    $insert_stmt->bind_param("iiidss", $user_id, $item_id, $quantity, $total_price, $payment_method, $order_date);
    $insert_stmt->execute();
    $insert_stmt->close();
}

// Clear the cart items for the user
$clear_cart_sql = "DELETE FROM cart_items WHERE user_id = ?";
$clear_cart_stmt = $conn->prepare($clear_cart_sql);
$clear_cart_stmt->bind_param("i", $user_id);
$clear_cart_stmt->execute();
$clear_cart_stmt->close();

$conn->close();

// Redirect to my_orders.php
header('Location: my_orders.php');
exit();
?>
