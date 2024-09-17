<?php
session_start();
include('db_connection.php');

// Check if item id is provided and is numeric
if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $item_id = $_GET['id'];

    // Check if user is logged in
    if(isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Check if item already exists in cart for the user
        $sql_check = "SELECT * FROM cart_items WHERE user_id = $user_id AND item_id = $item_id";
        $result_check = $conn->query($sql_check);

        if ($result_check) {
            if ($result_check->num_rows > 0) {
                // Item already exists in cart, update quantity
                $sql_update = "UPDATE cart_items SET quantity = quantity + 1 WHERE user_id = $user_id AND item_id = $item_id";
                if ($conn->query($sql_update) === TRUE) {
                    // Redirect to food_details.php after updating item
                    header("Location: cart.php?id=$item_id");
                    exit();
                } else {
                    // Handle update failure
                    echo "Error updating record: " . $conn->error;
                }
            } else {
                // Item does not exist in cart, insert new item
                $sql_insert = "INSERT INTO cart_items (user_id, item_id, quantity)
                               VALUES ($user_id, $item_id, 1)";
                if ($conn->query($sql_insert) === TRUE) {
                    // Redirect to food_details.php after adding item
                    header("Location: cart.php?id=$item_id");
                    exit();
                } else {
                    // Handle insert failure
                    echo "Error inserting record: " . $conn->error;
                }
            }
        } else {
            // Query execution failed
            echo "Error executing query: " . $conn->error;
        }
    } else {
        // User is not logged in, redirect to login page
        header('Location: login.php');
        exit();
    }
} else {
    // Redirect to an error page or handle invalid id scenario
    header('Location: food.php'); // Redirect to main menu or error page
    exit();
}

$conn->close();
?>
