<?php
session_start();
include('db_connection.php');

$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

$category_sql = "SELECT * FROM category WHERE id = $category_id";
$category_result = $conn->query($category_sql);
$category = $category_result->fetch_assoc();

$item_sql = "SELECT * FROM items WHERE category_id = $category_id";
$item_result = $conn->query($item_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['category_name']); ?> - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    
    <section class="hero-section" style="height: 30vh; min-height: 250px;">
        <div class="hero-overlay"></div>
        <div class="container hero-content text-center">
            <h1 class="hero-title" style="font-size: 2.5rem;">Category: <?php echo htmlspecialchars($category['category_name']); ?></h1>
        </div>
    </section>
    
    <section class="section-padding">
        <div class="container">
            <div class="modern-grid food-grid">
                <?php if ($item_result && $item_result->num_rows > 0): ?>
                    <?php while($row = $item_result->fetch_assoc()): ?>
                        <div class="food-card">
                            <div class="food-card-img">
                                <?php if ($row['image']): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                                <?php else: ?>
                                    <img src="images/default-food-img.jpg" alt="Default Image">
                                <?php endif; ?>
                                <span class="price-tag">৳ <?php echo htmlspecialchars($row['price']); ?></span>
                            </div>
                            <div class="food-card-content">
                                <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                                <p class="food-desc">
                                    <?php 
                                        $desc = htmlspecialchars($row['description'] ?? '');
                                        echo strlen($desc) > 60 ? substr($desc, 0, 60) . '...' : $desc; 
                                    ?>
                                </p>
                                <a href="food_details.php?id=<?php echo $row['id']; ?>" class="btn-full">See Details</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center" style="grid-column: 1 / -1; padding: 40px; background: var(--white); border-radius: var(--radius-md);">
                        <p>No items found in this category.</p>
                        <a href="categories.php" style="color: var(--primary-color); font-weight: 600;">&larr; Back to Categories</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    
    <?php include 'footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>