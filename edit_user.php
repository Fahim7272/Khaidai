<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
include('db_connection.php');

if (isset($_POST['submit'])) {
    $id = $_POST['id']; $name = $_POST['name']; $email = $_POST['email'];
    $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql); $stmt->bind_param("ssi", $name, $email, $id);
    $stmt->execute(); $stmt->close(); $conn->close();
    header("Location: manage_users.php"); exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT id, name, email FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql); $stmt->bind_param("i", $id); $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc(); $stmt->close();
} else { header("Location: manage_users.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><title>Edit User</title>
    <link rel="stylesheet" href="css/style.css"><link rel="stylesheet" href="css/modern.css">
    <style>.form-container{display:flex;justify-content:center;align-items:center;min-height:60vh;} .form-card{background:var(--white);padding:40px;border-radius:var(--radius-lg);box-shadow:var(--shadow-soft);width:100%;max-width:500px;} .form-control{width:100%;padding:12px;border:1px solid #ccc;border-radius:8px;margin-bottom:20px;font-family:'Poppins';}</style>
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>
    <section class="form-container section-padding">
        <div class="form-card">
            <h2 class="text-center" style="margin-bottom: 30px;">Edit User Detail</h2>
            <form action="edit_user.php" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                <label style="font-weight: 500;">Name:</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                <label style="font-weight: 500;">Email:</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                <button type="submit" name="submit" class="btn-primary" style="width: 100%; padding: 15px; border-radius: 8px; border:none; cursor:pointer;">Save Changes</button>
            </form>
            <div class="text-center" style="margin-top: 20px;"><a href="manage_users.php" style="color: var(--text-muted);">&larr; Back</a></div>
        </div>
    </section>
    <?php include 'footer.php'; ?>
</body>
</html>