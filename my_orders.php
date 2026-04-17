<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Using LEFT JOIN to prevent crashes if item_id is missing, and selecting safely
$sql = "SELECT o.*, i.name AS item_name, i.price AS item_price, i.image AS item_image
        FROM orders o 
        LEFT JOIN items i ON o.item_id = i.id
        WHERE o.user_id = ? 
        ORDER BY o.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .orders-container { max-width: 900px; margin: 0 auto; padding: 40px 20px; }
        .order-card { background: var(--white); border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); margin-bottom: 30px; overflow: hidden; }
        .order-header { background: var(--dark-bg); color: var(--white); padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; }
        .order-header h3 { margin: 0; font-size: 1.2rem; font-weight: 500; color: #fff !important; }
        .order-header span { color: #a4b0be; font-size: 0.95rem; }
        .order-body { padding: 25px; display: flex; align-items: center; gap: 25px; }
        .order-img { width: 100px; height: 100px; border-radius: 8px; object-fit: cover; background: var(--light-bg); }
        .order-details { flex: 1; }
        .order-details h4 { font-size: 1.3rem; margin: 0 0 8px 0; color: var(--text-main); }
        .order-meta { color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; }
        .order-status-wrapper { text-align: right; min-width: 150px; }
        
        /* Status Badges */
        .status-badge { display: inline-block; padding: 6px 15px; border-radius: 50px; font-weight: 600; font-size: 0.9rem; }
        .status-pending { background-color: #ffeaa7; color: #d35400; }
        .status-ofd { background-color: #74b9ff; color: #0984e3; }
        .status-delivered { background-color: #55efc4; color: #00b894; }
        
        @media (max-width: 768px) { .order-body { flex-direction: column; text-align: center; } .order-status-wrapper { text-align: center; margin-top: 15px; } }
    </style>
</head>
<div id="reviewModal" style="display:none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 15px; width: 90%; max-width: 400px; position: relative;">
        <h3 id="modalFoodName">Rate Food</h3>
        <form action="submit_review.php" method="POST">
            <input type="hidden" name="item_id" id="modalItemId">
            
            <label style="display:block; margin: 15px 0 5px;">Rating (1-5):</label>
            <select name="rating" class="form-control" style="width: 100%;" required>
                <option value="5">5 Stars - Excellent</option>
                <option value="4">4 Stars - Good</option>
                <option value="3">3 Stars - Average</option>
                <option value="2">2 Stars - Poor</option>
                <option value="1">1 Star - Terrible</option>
            </select>

            <label style="display:block; margin: 15px 0 5px;">Your Comment:</label>
            <textarea name="comment" class="form-control" style="width: 100%;" rows="3" placeholder="Tell us how it tasted!" required></textarea>

            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 20px; border: none; padding: 12px; cursor: pointer;">Submit Review</button>
            <button type="button" onclick="closeModal()" style="width: 100%; background: none; border: none; color: var(--text-muted); margin-top: 10px; cursor: pointer;">Cancel</button>
        </form>
    </div>
</div>

<script>
function openReviewModal(itemId, itemName) {
    document.getElementById('reviewModal').style.display = 'flex';
    document.getElementById('modalItemId').value = itemId;
    document.getElementById('modalFoodName').innerText = "Review " + itemName;
}
function closeModal() {
    document.getElementById('reviewModal').style.display = 'none';
}
</script>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <section class="section-padding" style="min-height: 70vh;">
        <div class="orders-container">
            <h2 class="section-heading text-center" style="margin-bottom: 40px;">Order History</h2>
            
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <h3>Order #<?php echo str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></h3>
                            <span>
                                <?php 
                                    // Safely handle the date depending on your database column name
                                    $date_val = isset($row['order_date']) ? $row['order_date'] : (isset($row['created_at']) ? $row['created_at'] : 'Recently');
                                    if ($date_val !== 'Recently') {
                                        echo date('F d, Y - h:i A', strtotime($date_val));
                                    } else {
                                        echo 'Recent Order';
                                    }
                                ?>
                            </span>
                        </div>
                        <div class="order-body">
                            <?php if (!empty($row['item_image'])): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($row['item_image']); ?>" alt="Food Image" class="order-img">
                            <?php else: ?>
                                <img src="images/default-food-img.jpg" alt="Default Food" class="order-img">
                            <?php endif; ?>
                            
                            <div class="order-details">
                                <h4><?php echo !empty($row['item_name']) ? htmlspecialchars($row['item_name']) : 'Custom/Cart Order'; ?></h4>
                                <div class="order-meta">
                                    Quantity: <strong><?php echo isset($row['quantity']) ? htmlspecialchars($row['quantity']) : '1'; ?></strong><br>
                                    Total Amount: <strong style="color: var(--primary-color);">৳ <?php echo isset($row['total_price']) ? number_format($row['total_price'], 2) : '0.00'; ?></strong><br>
                                    Payment: <?php echo isset($row['payment_method']) ? htmlspecialchars($row['payment_method']) : 'Standard'; ?>
                                </div>
                            </div>
                            
                            <div class="order-status-wrapper">
                                <?php 
                                    $current_status = isset($row['delivery_status']) ? $row['delivery_status'] : 'Pending';
                                    $status_class = 'status-pending';
                                    if ($current_status === 'Out for Delivery') $status_class = 'status-ofd';
                                    if ($current_status === 'Delivered') $status_class = 'status-delivered';
                                ?>
                                <span class="status-badge <?php echo $status_class; ?>" style="margin-bottom: 10px;">
                                    <?php echo htmlspecialchars($current_status); ?>
                                </span>

                                <?php if ($current_status === 'Delivered'): ?>
                                    <button onclick="openReviewModal(<?php echo $row['item_id']; ?>, '<?php echo htmlspecialchars($row['item_name']); ?>')" 
                                            class="btn-nav-outline" 
                                            style="display: block; width: 100%; padding: 5px; font-size: 0.8rem; border-color: var(--primary-color); color: var(--primary-color); cursor: pointer;">
                                        Rate Food
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center" style="padding: 50px; background: var(--white); border-radius: var(--radius-md); box-shadow: var(--shadow-soft);">
                    <img src="https://img.icons8.com/fluency/96/000000/purchase-order.png" alt="No Orders" style="margin-bottom: 20px;">
                    <h3 style="color: var(--text-main); margin-bottom: 15px;">No orders yet</h3>
                    <p style="color: var(--text-muted); margin-bottom: 25px;">You haven't placed any orders. Discover our menu and treat yourself!</p>
                    <a href="foods.php" class="btn-primary" style="display: inline-block; padding: 12px 30px; border-radius: 8px; text-decoration: none;">Order Food</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>