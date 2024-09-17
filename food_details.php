<?php
session_start();
include('db_connection.php');

// Check if id parameter is provided in the URL
if(isset($_GET['id'])) {
    $item_id = $_GET['id'];

    // Fetch item details from the database
    $sql_item = "SELECT id, name, price, description, image FROM items WHERE id = $item_id";
    $result_item = $conn->query($sql_item);

    if ($result_item->num_rows > 0) {
        $row = $result_item->fetch_assoc();
        $item_name = htmlspecialchars($row['name']);
        $item_price = htmlspecialchars($row['price']);
        $item_description = htmlspecialchars($row['description']);
        $item_image = $row['image'] ? 'data:image/jpeg;base64,' . base64_encode($row['image']) : 'images/default-food-img.jpg'; // Use a default image if none is provided

        // Fetch reviews for this item with user names and review dates
        $sql_reviews = "SELECT review_food.id, review_food.rating, review_food.review, review_food.review_date, users.name AS user_name
                        FROM review_food
                        JOIN users ON review_food.user_id = users.id
                        WHERE review_food.item_id = $item_id";
        $result_reviews = $conn->query($sql_reviews);
    } else {
        // No item found with the given id
        $item_name = 'Item Not Found';
        $item_price = '';
        $item_description = 'Sorry, the requested item could not be found.';
        $item_image = 'images/default-food-img.jpg'; // Default image for not found item
    }
} else {
    // Redirect if id parameter is not provided
    header('Location: foods.php');
    exit();
}

// Handle form submission for giving reviews
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'], $_POST['review'])) {
    $user_id = $_SESSION['user']['id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    // Insert review into database
    $sql_insert_review = "INSERT INTO review_food (user_id, item_id, rating, review) VALUES ($user_id, $item_id, $rating, '$review')";
    if ($conn->query($sql_insert_review) === TRUE) {
        $_SESSION['success_message'] = "Review added successfully.";
        // Redirect to refresh page after handling the update
        header("Location: food_details.php?id=$item_id");
        exit();
    } else {
        $_SESSION['error_message'] = "Error adding review: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Details - <?php echo $item_name; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/food-details.css"> <!-- Additional CSS for food details page -->
    <style>
        /* Add custom styles here */
       
        .food-details {
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            border-radius: 5px;
        }
        .food-details-img {
            text-align: center;
        }
        .food-details-img img {
            max-width: 40%;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .food-details-desc {
            margin-top: 20px;
        }
        .food-details-desc h2 {
            font-size: 2.5rem;
            color: #333;
        }
        .food-price {
            font-size: 1.5rem;
            color: #f39c12;
        }
        .food-description {
            font-size: 1.1rem;
            color: #666;
            margin-top: 10px;
        }
        .buttons {
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #f39c12;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #e67e22;
        }
        .reviews {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .reviews h3 {
            font-size: 1.8rem;
            color: #333;
        }
        .review-item {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }
        .review-item p {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 5px;
        }
        .review-item strong {
            color: #333;
            font-weight: bold;
        }
        .review-item .review-date {
            font-size: 0.9rem;
            color: #999;
        }
        .review-form {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .review-form h3 {
            font-size: 1.8rem;
            color: #333;
        }
        .review-form form {
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 10px;
        }
        .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            font-size: 1.1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group textarea {
            height: 100px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <section class="food-search text-center">
        <div class="container">
            
        <h3><?php echo $item_name; ?></h3>

        </div>
    </section>

    <section class="food-details">
        <div class="container">
            <div class="food-details-img">
                <img src="<?php echo $item_image; ?>" alt="<?php echo $item_name; ?>" class="img-responsive img-details">
            </div>
            <div class="food-details-desc">
                <h2><?php echo $item_name; ?></h2>
                <p class="food-price">৳<?php echo $item_price; ?></p>
                <p class="food-description"><?php echo $item_description; ?></p>
                
                <!-- Add to Cart and Order Now buttons -->
                <div class="buttons">
                    <a href="add_to_cart.php?id=<?php echo $item_id; ?>" class="btn">Add to Cart</a>
                    <a href="order_now.php?id=<?php echo $item_id; ?>" class="btn">Order Now</a>
                </div>

                <!-- Form for giving review -->
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="review-form">
                        <h3>Give Review:</h3>
                        <form action="food_details.php?id=<?php echo $item_id; ?>" method="POST">
                            <div class="form-group">
                                <label for="rating">Rating:</label>
                                <select name="rating" id="rating" required>
                                    <option value="">Select Rating</option>
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Very Good</option>
                                    <option value="3">3 - Good</option>
                                    <option value="2">2 - Fair</option>
                                    <option value="1">1 - Poor</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="review">Review:</label>
                                <textarea name="review" id="review" placeholder="Write your review here..." required></textarea>
                            </div>
                            <button type="submit" class="btn">Submit Review</button>
                        </form>
                    </div>
                <?php else: ?>
                    <p><a href="login.php">Login</a> to leave a review.</p>
                <?php endif; ?>

                <!-- Display reviews -->
                <div class="reviews">
                    <h3>Reviews:</h3>
                    <?php if ($result_reviews->num_rows > 0): ?>
                        <?php while ($review = $result_reviews->fetch_assoc()): ?>
                            <div class="review-item">
                                <p><strong><?php echo htmlspecialchars($review['user_name']); ?>:</strong> <?php echo $review['rating']; ?>/5</p>
                                <p>Review: <?php echo htmlspecialchars($review['review']); ?></p>
                                <p class="review-date">Reviewed on: <?php echo htmlspecialchars($review['review_date']); ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No reviews yet.</p>
                    <?php endif; ?>
                </div>

                
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>

<?php
$conn->close();
?>
