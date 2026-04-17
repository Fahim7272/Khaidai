<?php
// upload_images.php
include('db_connection.php');

echo "<h2>Starting Image Upload...</h2>";

/**
 * Helper function to safely update images in the database
 */
function updateImage($conn, $table, $columnToMatch, $matchValue, $imagePath) {
    if (file_exists($imagePath)) {
        $imageData = file_get_contents($imagePath);
        
        $sql = "UPDATE $table SET image = ? WHERE $columnToMatch = ?";
        $stmt = $conn->prepare($sql);
        
        // 'b' for blob, 's' for string
        $stmt->bind_param("bs", $imageData, $matchValue);
        $stmt->send_long_data(0, $imageData); // Send the image data
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Successfully uploaded <strong>$imagePath</strong> for $matchValue.</p>";
        } else {
            echo "<p style='color: red;'>Database error updating $matchValue.</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: orange;'>File not found: <strong>$imagePath</strong> (Make sure the name matches perfectly)</p>";
    }
}

// ==========================================
// 1. UPDATE CATEGORIES
// ==========================================
echo "<h3>Updating Categories</h3>";
updateImage($conn, 'category', 'category_name', 'Burgers', 'images/burger.jpg');
updateImage($conn, 'category', 'category_name', 'Pizzas', 'images/pizza.jpg');
updateImage($conn, 'category', 'category_name', 'Momos', 'images/momo.jpg');
// Add drinks if you have a drinks image! Example:
// updateImage($conn, 'category', 'category_name', 'Drinks', 'images/drinks.jpg');


// ==========================================
// 2. UPDATE FOOD ITEMS
// ==========================================
echo "<h3>Updating Food Items</h3>";
// Burgers
updateImage($conn, 'items', 'name', 'Smoky BBQ Burger', 'images/burger.jpg');
updateImage($conn, 'items', 'name', 'Crispy Chicken Burger', 'images/burger.jpg');

// Pizzas
updateImage($conn, 'items', 'name', 'Chicken Hawaiian Pizza', 'images/pizza.jpg');
updateImage($conn, 'items', 'name', 'Spicy Beef Pizza', 'images/pizza.jpg');

// Momos
updateImage($conn, 'items', 'name', 'Chicken Steam Momo', 'images/momo.jpg');
updateImage($conn, 'items', 'name', 'Fried Momo', 'images/momo.jpg');


echo "<h3>Finished!</h3>";
echo "<p><a href='index.php'>Go back to Homepage</a> and check out your images!</p>";

$conn->close();
?>