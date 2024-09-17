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
    <title>KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/card.css">
    <style>
        .text-red {
            color: rgb(236, 89, 15);
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <section class="food-search text-center">
        <div class="container">
            <form action="search.php" method="POST">
                <input type="search" name="search" placeholder="Search for Food.." required>
                <input type="submit" name="submit" value="Search" class="btn btn-primary">
            </form>
        </div>
    </section>
    <section class="food-menu">
        <div class="container">
            <h2 class="text-center">Food Menu</h2>
            <p class="text-red">Note: Update your current location before order food.</p>
            
            <div class="food-menu-grid">
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <div class="food-menu-box">
                            <div class="food-menu-img">
                                <?php if ($item['image']): ?>
                                    <img src="data:image/jpeg;base64,<?php echo base64_encode($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-responsive img-curve">
                                <?php else: ?>
                                    <img src="images/default-food-img.jpg" alt="Default Image" class="img-responsive img-curve">
                                <?php endif; ?>
                            </div>
                            <div class="food-menu-desc">
                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p class="food-price">৳ <?php echo htmlspecialchars($item['price']); ?></p>
                                <a href="food_details.php?id=<?php echo $item['id']; ?>" class="btn btn-primary">See Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No items found.</p>
                <?php endif; ?>
            </div>

            <div class="clearfix"></div>
        </div>
    </section>
    <?php include 'footer.php'; ?>
    
</body>
</html>
