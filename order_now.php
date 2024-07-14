<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$sql_user = "SELECT * FROM users WHERE id = $user_id";
$result_user = $conn->query($sql_user);

if ($result_user->num_rows == 1) {
    $user = $result_user->fetch_assoc();
    $user_name = $user['name'];
    $user_location = $user['location']; 
} else {
    header('Location: food.php');
    exit();
}

$item_id = isset($_GET['id']) ? $_GET['id'] : null;
$item_details = [];
$error_message = '';

if (!empty($item_id)) {
    $sql_item = "SELECT * FROM items WHERE id = $item_id";
    $result_item = $conn->query($sql_item);

    if ($result_item->num_rows == 1) {
        $item_details = $result_item->fetch_assoc();
    } else {
        $error_message = "Item not found.";
    }
} else {
    $error_message = "Item ID not provided.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['quantity'])) {
    $quantity = $_POST['quantity'];
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';

    $total_price = $item_details['price'] * $quantity;

    $sql_insert_order = "INSERT INTO orders (user_id, item_id, quantity, total_price, payment_method)
                         VALUES ($user_id, $item_id, $quantity, $total_price, '$payment_method')";

    if ($conn->query($sql_insert_order) === TRUE) {
       
        header("Location: my_orders.php");
        exit();
    } else {
        echo "Error inserting order: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Now - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/form.css"> 
    <style>
        
        .order-summary {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }
        .order-summary h3 {
            margin-bottom: 10px;
            color: #333;
        }
        .order-summary p {
            margin-bottom: 5px;
        }
        .order-summary ul {
            list-style-type: none;
            padding-left: 0;
        }
        .order-summary ul li {
            margin-bottom: 5px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .form-group input[type="number"] {
            width: 80px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            text-align: center;
        }
        .btn-primary {
            background-color: #4CAF50;
            color: white;
            padding: 12px 24px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .btn-primary:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?> 

    <div class="container">
        <h2 class="text-center">Order Now</h2>

        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php else: ?>
            <div class="order-summary">
                <h3>Your Location:</h3>
                <p><?php echo htmlspecialchars($user_location); ?></p>
                <p>Dear <?php echo htmlspecialchars($user_name); ?>, please confirm your order:</p>
                <ul>
                    <li><?php echo htmlspecialchars($item_details['name']); ?> - $<?php echo number_format($item_details['price'], 2); ?></li>
                </ul>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $item_id; ?>" method="POST" class="order-form">
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="10" required>
                    </div>

                    <h3>Payment Options</h3>
                    <div class="form-group">
                        <label><input type="radio" name="payment_method" value="বিকাশ"> বিকাশ </label>
                    </div>
                    <div class="form-group">
                        <label><input type="radio" name="payment_method" value="নগদ"> নগদ </label>
                    </div>
                    <div class="form-group">
                        <label><input type="radio" name="payment_method" value="ক্যাশ-অন-ডেলিভারি"> ক্যাশ-অন-ডেলিভারি</label>
                    </div>

                    <input type="submit" value="Place Order" class="btn btn-primary">
                </form>
            </div>

            <?php if (isset($total_price)): ?>
                <div class="order-summary">
                    <h3>Total Amount:</h3>
                    <p>$<?php echo number_format($total_price, 2); ?></p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?> 

</body>
</html>

<?php
$conn->close();
?>
