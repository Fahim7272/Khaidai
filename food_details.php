<?php
session_start();
include('db_connection.php');

if(isset($_GET['id'])) {
    $item_id = intval($_GET['id']);
    $sql_item = "SELECT id, name, price, description, image FROM items WHERE id = $item_id";
    $result_item = $conn->query($sql_item);

    if ($result_item->num_rows > 0) {
        $row = $result_item->fetch_assoc();
        $item_name = htmlspecialchars($row['name']);
        $item_price = htmlspecialchars($row['price']);
        $item_description = htmlspecialchars($row['description']);
        $item_image = $row['image'] ? 'data:image/jpeg;base64,' . base64_encode($row['image']) : 'images/default-food-img.jpg';

        $sql_reviews = "SELECT review_food.id, review_food.rating, review_food.review, review_food.review_date, users.name AS user_name
                        FROM review_food JOIN users ON review_food.user_id = users.id WHERE review_food.item_id = $item_id ORDER BY review_food.review_date DESC";
        $result_reviews = $conn->query($sql_reviews);
    } else {
        header('Location: foods.php');
        exit();
    }
} else {
    header('Location: foods.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'], $_POST['review'])) {
    $user_id = $_SESSION['user']['id'];
    $rating = intval($_POST['rating']);
    $review = $conn->real_escape_string($_POST['review']);
    $sql_insert_review = "INSERT INTO review_food (user_id, item_id, rating, review) VALUES ($user_id, $item_id, $rating, '$review')";
    if ($conn->query($sql_insert_review) === TRUE) {
        header("Location: food_details.php?id=$item_id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $item_name; ?> - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start; margin-bottom: 60px; }
        .details-img-wrapper { border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-soft); }
        .details-img-wrapper img { width: 100%; height: auto; display: block; }
        .details-info h1 { font-size: 2.8rem; margin-bottom: 10px; color: var(--text-main); }
        .details-price { font-size: 2rem; color: var(--primary-color); font-weight: 700; margin-bottom: 20px; }
        .details-desc { font-size: 1.1rem; color: var(--text-muted); line-height: 1.8; margin-bottom: 30px; }
        .action-buttons { display: flex; gap: 15px; }
        .action-buttons a { flex: 1; text-align: center; padding: 15px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: var(--transition); }
        .btn-add-cart { background: var(--dark-bg); color: var(--white); }
        .btn-add-cart:hover { background: #353b48; }
        .btn-order-now { background: var(--primary-color); color: var(--white); }
        .btn-order-now:hover { background: var(--primary-hover); }
        
        .reviews-section { background: var(--white); padding: 40px; border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); }
        .review-card { border-bottom: 1px solid #eee; padding: 20px 0; }
        .review-card:last-child { border-bottom: none; }
        .review-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .reviewer-name { font-weight: 600; color: var(--text-main); }
        .review-rating { color: #f1c40f; font-weight: bold; }
        
        .review-form-wrapper { margin-top: 30px; background: var(--light-bg); padding: 30px; border-radius: var(--radius-md); }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; margin-bottom: 15px; font-family: 'Poppins', sans-serif;}
        
        @media (max-width: 768px) { .details-grid { grid-template-columns: 1fr; } .action-buttons { flex-direction: column; } }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <section class="section-padding">
        <div class="container">
            <div class="details-grid">
                <div class="details-img-wrapper">
                    <img src="<?php echo $item_image; ?>" alt="<?php echo $item_name; ?>">
                </div>
                
                <div class="details-info">
                    <h1><?php echo $item_name; ?></h1>
                    <div class="details-price">৳<?php echo $item_price; ?></div>
                    <p class="details-desc"><?php echo $item_description; ?></p>
                    
                    <div class="action-buttons">
                        <a href="add_to_cart.php?id=<?php echo $item_id; ?>" class="btn-add-cart">Add to Cart</a>
                        <a href="order_now.php?id=<?php echo $item_id; ?>" class="btn-order-now">Order Now</a>
                    </div>
                </div>
            </div>

            <section class="section-padding">
    <div class="container">
        <h3 style="margin-bottom: 30px; border-bottom: 2px solid var(--light-bg); padding-bottom: 10px;">Customer Reviews</h3>
        
        <?php
        // Fetch reviews for this specific item
        $review_sql = "SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.item_id = ? ORDER BY r.id DESC";
        $rev_stmt = $conn->prepare($review_sql);
        $rev_stmt->bind_param("i", $item_id);
        $rev_stmt->execute();
        $reviews = $rev_stmt->get_result();

        if ($reviews->num_rows > 0): 
            while ($rev = $reviews->fetch_assoc()): ?>
                <div style="background: var(--white); padding: 20px; border-radius: var(--radius-md); margin-bottom: 15px; shadow: var(--shadow-soft);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <strong><?php echo htmlspecialchars($rev['name']); ?></strong>
                        <span style="color: #f1c40f;">
                            <?php echo str_repeat('★', $rev['rating']) . str_repeat('☆', 5 - $rev['rating']); ?>
                        </span>
                    </div>
                    <p style="color: var(--text-muted); margin: 0; font-style: italic;">"<?php echo htmlspecialchars($rev['comment']); ?>"</p>
                    <small style="color: #27ae60; font-weight: 600; display: block; margin-top: 5px;">✓ Verified Customer</small>
                </div>
            <?php endwhile;
        else: ?>
            <p style="color: var(--text-muted); text-align: center; padding: 20px;">No reviews yet for this dish. Be the first to order and share your thoughts!</p>
        <?php endif; ?>
    </div>
</section>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>