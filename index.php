<?php
session_start();
include('db_connection.php');

$category_sql = "SELECT * FROM category LIMIT 3";
$category_result = $conn->query($category_sql);

$item_sql = "SELECT * FROM items LIMIT 6";
$item_result = $conn->query($item_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KhaiDai - Fresh Food Delivery</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css"> </head>
<body>
    
    <?php include 'navbar.php'; ?>
    
    <header class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container hero-content text-center">
            <h1 class="hero-title">Hungry? We've Got You Covered.</h1>
            <p class="hero-subtitle">Discover the best food and drinks in your area.</p>
            
            <form action="search.php" method="POST" class="hero-search-form">
                <div class="search-wrapper">
                    <input type="search" name="search" placeholder="Search for your favorite dish..." required class="search-input">
                    <button type="submit" name="submit" class="btn btn-primary search-btn">Find Food</button>
                </div>
            </form>
        </div>
    </header>

    <section class="categories-section section-padding">
        <div class="container">
            <h2 class="section-heading text-center">Craving Something Specific?</h2>
            
            <div class="modern-grid category-grid">
                <?php if ($category_result && $category_result->num_rows > 0): ?>
                    <?php while($category_row = $category_result->fetch_assoc()): ?>
                        <a href="category-foods.php?category_id=<?php echo $category_row['id']; ?>" class="category-card">
                            <div class="category-img-wrapper">
                                <?php if ($category_row['image']): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($category_row['image']); ?>" alt="<?php echo htmlspecialchars($category_row['category_name']); ?>">
                                <?php else: ?>
                                    <img src="images/default-category-img.jpg" alt="Default Category">
                                <?php endif; ?>
                            </div>
                            <div class="category-title-overlay">
                                <h3><?php echo htmlspecialchars($category_row['category_name']); ?></h3>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center">No categories found.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="featured-section section-padding bg-light">
        <div class="container">
            <div class="section-header flex-between">
                <h2 class="section-heading">Popular Right Now</h2>
                <a href="foods.php" class="view-all-link">View Full Menu &rarr;</a>
            </div>

            <div class="modern-grid food-grid">
                <?php if ($item_result && $item_result->num_rows > 0): ?>
                    <?php while($item_row = $item_result->fetch_assoc()): ?>
                        <div class="food-card">
                            <div class="food-card-img">
                                <?php if ($item_row['image']): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($item_row['image']); ?>" alt="<?php echo htmlspecialchars($item_row['name']); ?>">
                                <?php else: ?>
                                    <img src="images/default-food-img.jpg" alt="Default Image">
                                <?php endif; ?>
                                <span class="price-tag">৳ <?php echo htmlspecialchars($item_row['price']); ?></span>
                            </div>
                            <div class="food-card-content">
                                <h4><?php echo htmlspecialchars($item_row['name']); ?></h4>
                                <p class="food-desc">
                                    <?php 
                                        $desc = htmlspecialchars($item_row['description'] ?? '');
                                        echo strlen($desc) > 60 ? substr($desc, 0, 60) . '...' : $desc; 
                                    ?>
                                </p>
                                <a href="food_details.php?id=<?php echo $item_row['id']; ?>" class="btn btn-primary btn-full">Order Now</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center">No featured items available.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

</body>
</html>
<?php $conn->close(); ?>