<?php


session_start();


if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


include 'caterings.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart'])) {
        
        $service_id = intval($_POST['service_id']);

        foreach ($caterings as $catering) {
            if ($catering['id'] === $service_id) {
                if (isset($_SESSION['cart'][$service_id])) {
                    $_SESSION['cart'][$service_id]['quantity'] += 1;
                } else {
                    $_SESSION['cart'][$service_id] = [
                        'name' => $catering['name'],
                        'price' => $catering['price'],
                        'quantity' => 1
                    ];
                }
                break;
            }
        }
        header("Location: cart.php");
        exit();
    }

    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantities'] as $service_id => $quantity) {
            $service_id = intval($service_id);
            $quantity = intval($quantity);
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$service_id]);
            } else {
                $_SESSION['cart'][$service_id]['quantity'] = $quantity;
            }
        }
        header("Location: cart.php");
        exit();
    }
}
$cart_items = [];
$total = 0;
foreach ($_SESSION['cart'] as $service_id => $item) {
    foreach ($caterings as $catering) {
        if ($catering['id'] === $service_id) {
            $cart_items[$service_id] = [
                'name' => $catering['name'],
                'price' => $catering['price'],
                'quantity' => $item['quantity']
            ];
            $total += $catering['price'] * $item['quantity'];
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link href="./shop.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .main-container {
            background-color: rgba(255, 255, 255, 0.95); 
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="main-container container px-4 py-5">
        <h2 class="pb-2 border-bottom">Your Quotation</h2>
        <?php if (empty($cart_items)): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <form method="POST" action="cart.php">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cart_items as $service_id => $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name'], ENT_QUOTES); ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <input type="number" name="quantities[<?php echo intval($service_id); ?>]" value="<?php echo intval($item['quantity']); ?>" min="0" class="form-control" style="width: 80px;">
                                </td>
                                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td><strong>$<?php echo number_format($total, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
                <button type="submit" name="update_cart" class="btn btn-primary">Update Cart</button>
               
                <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
