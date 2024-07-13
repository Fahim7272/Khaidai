<?php
session_start();
include('db_connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if address details are provided
if (isset($_POST['address']) && isset($_POST['city']) && isset($_POST['postal_code'])) {
    $address = $_POST['address'];
    $city = $_POST['city'];
    $postal_code = $_POST['postal_code'];

    // Insert order into the orders table
    $sql_order = "INSERT INTO orders (customer_id, address, city, postal_code, status)
                  VALUES ($user_id, '$address', '$city', '$postal_code', 'Pending')";
    if ($conn->query($sql_order) === TRUE) {
        $order_id = $conn->insert_id;

        // Fetch cart items
        $sql_cart = "SELECT item_id, quantity, total_price FROM cart_items WHERE user_id = $user_id";
        $result_cart = $conn->query($sql_cart);

        if ($result_cart->num_rows > 0) {
            while ($row_cart = $result_cart->fetch_assoc()) {
                $item_id = $row_cart['item_id'];
                $quantity = $row_cart['quantity'];
                $total_price = $row_cart['total_price'];

                // Insert order items into the order_items table
                $sql_order_item = "INSERT INTO order_items (order_id, item_id, quantity, total_price)
                                   VALUES ($order_id, $item_id, $quantity, $total_price)";
                $conn->query($sql_order_item);
            }

            // Clear cart after placing order
            $sql_clear_cart = "DELETE FROM cart_items WHERE user_id = $user_id";
            $conn->query($sql_clear_cart);

            // Redirect to order confirmation page
            header('Location: order_confirmation.php');
            exit();
        } else {
            echo "No items in cart.";
        }
    } else {
        echo "Error placing order: " . $conn->error;
    }
} else {
    echo "Please fill in all required fields.";
}

$conn->close();
?>
