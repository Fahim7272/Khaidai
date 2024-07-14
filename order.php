<?php
session_start();

if (!isset($_SESSION['user'])) {
    
    header("Location: login.html");
    exit();
}


include('db_connection.php');

function getItemDetails($conn, $item_id) {
    $sql = "SELECT id, name, price, image FROM items WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();
    return $item;
}

$user = $_SESSION['user'];
$user_id = $user['id'];

if (isset($_SESSION['selected_items']) && !empty($_SESSION['selected_items'])) {
    $selected_items = $_SESSION['selected_items'];

    $order_items = [];
    foreach ($selected_items as $item_id => $quantity) {
        $item = getItemDetails($conn, $item_id);
        if ($item) {
            $item['quantity'] = $quantity;
            $item['total_price'] = $item['price'] * $quantity; 
            $order_items[] = $item;
        }
    }

    unset($_SESSION['selected_items']);
} else {
    header("Location: cart.php"); 
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - KhaiDai</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
       
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .order-table th, .order-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .order-table th {
            background-color: #f4f4f4;
        }
        .order-table td {
            vertical-align: middle;
        }
        .order-actions {
            text-align: right;
            margin-top: 20px;
        }
        .btn-order {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .btn-order:hover {
            background-color: #45a049;
        }
        .btn-order:focus {
            outline: none;
        }
        
        .item-image {
            max-width: 100px;
            max-height: 100px;
            border-radius: 5px;
            object-fit: cover;
        }
        .quantity-input {
            width: 50px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h2>Order Details</h2>
        <form id="orderForm" action="place_order.php" method="post">
            <table class="order-table">
                <thead>
                    <tr>
                        <th colspan="2">Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr id="itemRow<?php echo $item['id']; ?>">
                            <td>
                                <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image">
                               
                            </td>
                            <td> <?php echo htmlspecialchars($item['name']); ?></td>
                            <td>$<?php echo htmlspecialchars($item['price']); ?></td>
                            <td>
                                <input type="number" name="quantity[<?php echo $item['id']; ?>]" id="quantity<?php echo $item['id']; ?>" class="quantity-input" value="<?php echo $item['quantity']; ?>" min="1" max="99" onchange="updateTotal(<?php echo $item['id']; ?>)">
                            </td>
                            <td id="totalPrice<?php echo $item['id']; ?>" class="item-total">
                                $<?php echo number_format($item['total_price'], 2); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="order-actions">
                <input type="submit" class="btn-order" value="Place Order">
            </div>
        </form>
    </div>
    <section class="social">
        <div class="container text-center">
            <ul>
                <li><a href="#"><img src="https://img.icons8.com/fluent/50/000000/facebook-new.png"/></a></li>
                <li><a href="#"><img src="https://img.icons8.com/fluent/48/000000/instagram-new.png"/></a></li>
                <li><a href="#"><img src="https://img.icons8.com/fluent/48/000000/twitter.png"/></a></li>
            </ul>
        </div>
    </section>
    <section class="footer">
        <div class="container text-center">
            <p>All rights reserved - KhaiDai</p>
        </div>
    </section>

    <script>
        function updateTotal(itemId) {
            const quantity = document.getElementById(`quantity${itemId}`).value;
            const price = <?php echo json_encode($item['price']); ?>; 
            const totalElement = document.getElementById(`totalPrice${itemId}`);
            const total = price * quantity;
            totalElement.textContent = `$${total.toFixed(2)}`;
        }
    </script>
</body>
</html>
