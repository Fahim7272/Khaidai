<?php
session_start();
include('db_connection.php');


$category_sql = "SELECT * FROM category";
$category_result = $conn->query($category_sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Categories - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/category.css">
    <style>
        
        
    </style>
</head>
<body>
    
    <?php include 'navbar.php'; ?>
    <section class="food-search text-center">
        <div class="container">
            <h3>Manage Food</h3>
            <p>This administration page allows you to efficiently oversee and manage the restaurant's menu, including adding, updating, and removing food items.</p>
        </div>
    </section>
    
    <section class="categories">
        <div class="container">
            <h2 class="text-center">All Categories</h2>

            <?php if ($category_result->num_rows > 0): ?>
                <div class="categories-grid">
                    <?php while($category_row = $category_result->fetch_assoc()): ?>
                        <a href="category-foods.php?category_id=<?php echo $category_row['id']; ?>">
                            <div class="category-card">
                                <?php if ($category_row['image']): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($category_row['image']); ?>" alt="<?php echo htmlspecialchars($category_row['category_name']); ?>">
                                <?php else: ?>
                                    <img src="images/default-category-img.jpg" alt="Default Image">
                                <?php endif; ?>
                                <h3><?php echo htmlspecialchars($category_row['category_name']); ?></h3>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No categories found.</p>
            <?php endif; ?>

            <div class="text-center">
                <a href="index.php">Back to Home</a>
            </div>
        </div>
    </section>
    
    <?php include 'footer.php'; ?>
    
</body>
</html>
