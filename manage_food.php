<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM items WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Food Item Deleted Successfully.";
    } else {
        $_SESSION['error'] = "Failed to Delete Food Item.";
    }
    $stmt->close();
    header("Location: manage_food.php");
    exit();
}

// Handle Add Food Form
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];

    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = file_get_contents($_FILES['image']['tmp_name']);
    }

    $insert_sql = "INSERT INTO items (name, price, description, image, category_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("sdsbi", $name, $price, $description, $image, $category_id);
    if ($image !== null) {
        $stmt->send_long_data(3, $image); 
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = "Food Item Added Successfully.";
    } else {
        $_SESSION['error'] = "Failed to Add Food Item.";
    }
    $stmt->close();
    header("Location: manage_food.php");
    exit();
}

// Fetch Categories and Items
$categories_result = $conn->query("SELECT id, category_name FROM category");
$items_sql = "SELECT items.*, category.category_name FROM items LEFT JOIN category ON items.category_id = category.id ORDER BY items.id DESC";
$items_result = $conn->query($items_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Food - Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .manage-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start; }
        .form-card, .table-container { background: var(--white); padding: 30px; border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); }
        .form-control { width: 100%; padding: 12px; border: 1px solid #dcdde1; border-radius: 8px; font-family: 'Poppins', sans-serif; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th { background: var(--dark-bg); color: var(--white); padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: middle; }
        .btn-delete { background: #e74c3c; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 0.85rem; }
        @media (max-width: 900px) { .manage-grid { grid-template-columns: 1fr; } .table-container { overflow-x: auto; } }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <section class="hero-section" style="height: 25vh; min-height: 200px;">
        <div class="hero-overlay"></div>
        <div class="container hero-content text-center">
            <h1 class="hero-title" style="font-size: 2.2rem;">Menu Management</h1>
        </div>
    </section>

    <section class="section-padding">
        <div class="container">
            <?php if (isset($_SESSION['message'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="manage-grid">
                <div class="form-card">
                    <h3 style="margin-bottom: 20px; border-bottom: 2px solid var(--light-bg); padding-bottom: 10px;">Add New Dish</h3>
                    <form action="manage_food.php" method="POST" enctype="multipart/form-data">
                        <label style="font-weight: 500;">Food Name:</label>
                        <input type="text" name="name" class="form-control" required>

                        <label style="font-weight: 500;">Price (৳):</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>

                        <label style="font-weight: 500;">Category:</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php while($row = $categories_result->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['category_name']); ?></option>
                            <?php endwhile; ?>
                        </select>

                        <label style="font-weight: 500;">Description:</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>

                        <label style="font-weight: 500;">Image:</label>
                        <input type="file" name="image" class="form-control" accept="image/*">

                        <button type="submit" name="submit" class="btn-primary" style="width: 100%; padding: 12px; border-radius: 8px; border:none; cursor:pointer;">Add Food</button>
                    </form>
                </div>

                <div class="table-container">
                    <h3 style="margin-bottom: 20px;">Current Menu Items</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Category</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($items_result->num_rows > 0): ?>
                                <?php while ($item = $items_result->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <?php if ($item['image']): ?>
                                                <img src="data:image/jpeg;base64,<?php echo base64_encode($item['image']); ?>" width="50" height="50" style="object-fit: cover; border-radius: 5px;">
                                            <?php else: ?>
                                                <span>No Img</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                                        <td>৳<?php echo htmlspecialchars($item['price']); ?></td>
                                        <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                                        <td>
                                            <a href="manage_food.php?delete_id=<?php echo $item['id']; ?>" class="btn-delete" onclick="return confirm('Delete this item?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center">No food items added yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>