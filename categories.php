<?php
session_start();
include('db_connection.php');

$category_sql = "SELECT * FROM category";
$category_result = $conn->query($category_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Categories - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    
    <section class="hero-section" style="height: 30vh; min-height: 250px;">
        <div class="hero-overlay"></div>
        <div class="container hero-content text-center">
            <h1 class="hero-title" style="font-size: 2.5rem;">Explore By Category</h1>
            <p style="color: #eccc68;">Find exactly what you're craving.</p>
        </div>
    </section>
    
    <section class="categories-section section-padding">
        <div class="container">
            <?php if ($category_result->num_rows > 0): ?>
                <div class="modern-grid category-grid">
                    <?php while($category_row = $category_result->fetch_assoc()): ?>
                        <a href="category-foods.php?category_id=<?php echo $category_row['id']; ?>" class="category-card">
                            <div class="category-img-wrapper">
                                <?php if ($category_row['image']): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($category_row['image']); ?>" alt="<?php echo htmlspecialchars($category_row['category_name']); ?>">
                                <?php else: ?>
                                    <img src="images/default-food-img.jpg" alt="Default Category">
                                <?php endif; ?>
                            </div>
                            <div class="category-title-overlay">
                                <h3><?php echo htmlspecialchars($category_row['category_name']); ?></h3>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center" style="padding: 40px; background: var(--white); border-radius: var(--radius-md);">
                    <p>No categories found.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <?php include 'footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>