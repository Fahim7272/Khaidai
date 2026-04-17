<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
include('db_connection.php');

$sql = "SELECT u.id, u.name, u.email,
        (SELECT COUNT(*) FROM orders WHERE user_id = u.id) AS total_orders,
        (SELECT COUNT(*) FROM review_food WHERE user_id = u.id) AS total_reviews
        FROM users u";
$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modern.css">
    <style>
        .table-container { background: var(--white); padding: 30px; border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th { background: var(--dark-bg); color: var(--white); padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
        .btn-delete { background: #e74c3c; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 0.9rem; transition: var(--transition); }
        .btn-delete:hover { background: #c0392b; }
        .btn-edit { background: #3498db; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 0.9rem; margin-right: 5px; transition: var(--transition); }
        .btn-edit:hover { background: #2980b9; }
    </style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

    <section class="hero-section" style="height: 25vh; min-height: 200px;">
        <div class="hero-overlay"></div>
        <div class="container hero-content text-center">
            <h1 class="hero-title" style="font-size: 2.2rem;">Manage Customers</h1>
        </div>
    </section>

    <section class="section-padding">
        <div class="container">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Total Orders</th>
                            <th>Reviews</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><strong>#<?php echo htmlspecialchars($user['id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><span style="background: var(--light-bg); padding: 5px 10px; border-radius: 20px; font-weight: bold;"><?php echo htmlspecialchars($user['total_orders']); ?></span></td>
                                <td><?php echo htmlspecialchars($user['total_reviews']); ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this user? This cannot be undone.');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>