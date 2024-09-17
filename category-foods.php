<?php
session_start();
include('db_connection.php');


$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;


$category_sql = "SELECT * FROM category WHERE id = $category_id";
$category_result = $conn->query($category_sql);
$category = $category_result->fetch_assoc();


$item_sql = "SELECT * FROM items WHERE category_id = $category_id";
$item_result = $conn->query($item_sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['category_name']); ?> - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/card.css">
</head>
<body>
    
    <?php include 'navbar.php'; ?>
    
    <section class="food-search text-center">
        <div class="container">
            <form action="search.php" method="POST">
                <input type="search" name="search" placeholder="Search for Food or Category.." required>
                <input type="submit" name="submit" value="Search" class="btn btn-primary">
            </form>
        </div>
    </section>
    
    <section class="categories">
        <div class="container">
            <h2 class="text-center"><?php echo htmlspecialchars($category['category_name']); ?></h2>
            <?php if ($category['image']): ?>
                <div class="category-img">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($category['image']); ?>" alt="<?php echo htmlspecialchars($category['category_name']); ?>" class="img-responsive img-curve">
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <section class="food-menu">
        <div class="container">
            <h2 class="text-center">Food Menu</h2>
            
            <div class="food-menu-grid">
                <?php if ($item_result->num_rows > 0): ?>
                    <?php while($row = $item_result->fetch_assoc()): ?>
                        <div class="food-menu-box">
                            <div class="food-menu-img">
                                <?php if ($row['image']): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="img-responsive img-curve">
                                <?php else: ?>
                                    <img src="images/default-food-img.jpg" alt="Default Image" class="img-responsive img-curve">
                                <?php endif; ?>
                            </div>
                            <div class="food-menu-desc">
                                <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                                <p class="food-price">৳ <?php echo htmlspecialchars($row['price']); ?></p>
                                <a href="food_details.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">See Details</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No items found in this category.</p>
                <?php endif; ?>
            </div>

            <div class="clearfix"></div>
        </div>
    </section>
    
    <?php include 'footer.php'; ?>
    
</body>
</html>
