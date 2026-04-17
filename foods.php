<?php
session_start();
include('db_connection.php');
include('ItemRepository.php');

$itemRepository = new ItemRepository($conn);
$items = $itemRepository->getItems();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full Menu - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css"> </head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    
    <section class="hero-section" style="height: 40vh; min-height: 300px;">
        <div class="hero-overlay"></div>
        <div class="container hero-content text-center">
            <h2 class="hero-title" style="font-size: 2.5rem;">Our Full Menu</h2>
            <form action="search.php" method="POST" class="hero-search-form" style="margin-top: 20px;">
                <div class="search-wrapper">
                    <input type="search" name="search" placeholder="Search for Food.." required class="search-input">
                    <button type="submit" name="submit" class="btn-primary search-btn">Search</button>
                </div>
            </form>
        </div>
    </section>

    <section class="section-padding">
        <div class="container">
            <p class="text-center" style="color: var(--primary-color); margin-bottom: 30px; font-weight: 500;">
                Note: Please ensure your location is updated in your profile before ordering.
            </p>
            
            <div class="modern-grid food-grid">
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <div class="food-card">
                            <div class="food-card-img">
                                <?php if ($item['image']): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <?php else: ?>
                                    <img src="images/default-food-img.jpg" alt="Default Image">
                                <?php endif; ?>
                                <span class="price-tag">৳ <?php echo htmlspecialchars($item['price']); ?></span>
                            </div>
                            <div class="food-card-content">
                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p class="food-desc">
                                    <?php 
                                        $desc = htmlspecialchars($item['description'] ?? '');
                                        echo strlen($desc) > 60 ? substr($desc, 0, 60) . '...' : $desc; 
                                    ?>
                                </p>
                                <a href="food_details.php?id=<?php echo $item['id']; ?>" class="btn-full">See Details & Order</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center" style="grid-column: 1 / -1;">No items found in the menu.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>