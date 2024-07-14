<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include('db_connection.php');

// Fetch all users along with total food ordered and total reviews given
$sql = "
    SELECT 
        u.id, 
        u.name, 
        u.email,
        (SELECT COUNT(*) FROM orders WHERE user_id = u.id) AS total_orders,
        (SELECT COUNT(*) FROM review_food WHERE user_id = u.id) AS total_reviews
    FROM 
        users u
";

$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <section class="food-search text-center">
        <div class="container">
            <h1>Manage Users</h1>
            <p>This administration page provides tools to manage user accounts,<br>
            including viewing, editing, and deleting profiles, as well as handling user roles and permissions.</p>
        </div>
    </section>

    <section class="content">
        <div class="wrapper">
            <br>
            <div class="tbl-full">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Total Orders</th>
                            <th>Total Reviews</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['total_orders']); ?></td>
                                <td><?php echo htmlspecialchars($user['total_reviews']); ?></td>
                                <td>
                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <section class="footer">
        <div class="container text-center">
            <p>All rights reserved - KhaiDai</p>
        </div>
    </section>
</body>
</html>
