<?php
session_start();
include('db_connection.php');

$search = isset($_POST['search']) ? $conn->real_escape_string($_POST['search']) : '';

$sql = "SELECT * FROM items WHERE name LIKE '%$search%' OR description LIKE '%$search%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    
    <section class="hero-section" style="height: 30vh; min-height: 250px;">
        <div class="hero-overlay"></div>
        <div class="container hero-content text-center">
            <h1 class="hero-title" style="font-size: 2.5rem;">Foods on Your Search <span style="color: var(--primary-color);">"<?php echo htmlspecialchars($search); ?>"</span></h1>
        </div>
    </section>

    <section class="section-padding">
        <div class="container">
            <div class="modern-grid food-grid">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
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
                                <a href="food_details.php?id=<?php echo $row['id']; ?>" class="btn-full">Order Now</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center" style="grid-column: 1 / -1; padding: 40px; background: var(--white); border-radius: var(--radius-md);">
                        <p style="font-size: 1.2rem; color: var(--text-muted);">No food found matching your search. Please try another keyword.</p>
                        <br>
                        <a href="foods.php" class="btn-primary" style="padding: 12px 30px; border-radius: 8px;">View Full Menu</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>