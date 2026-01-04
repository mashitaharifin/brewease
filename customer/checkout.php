<?php
session_start();
require_once '../config.php';  // Adjust path as needed
include 'header.php';


$customer_id = $_SESSION['customer_id'] ?? null;
$error = '';

if (!$customer_id) {
    $error = "You must be logged in to proceed to checkout.";
}

// Fetch cart items
$cart_items = [];
$total_price = 0;

if ($customer_id) {
    $stmt = $conn->prepare("SELECT * FROM cart WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($item = $result->fetch_assoc()) {
        $cart_items[] = $item;
        $total_price += $item['price'] * $item['quantity'];
    }
    $stmt->close();

    // Get customer points
    $stmt = $conn->prepare("SELECT points FROM customers WHERE id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $stmt->bind_result($current_points);
    $stmt->fetch();
    $stmt->close();

    // Check if eligible for free coffee
    $free_coffee_applied = false;
    if ($current_points >= 1000) {
        $total_price -= 10.00;
        if ($total_price < 0) $total_price = 0;
        $current_points -= 1000;
        $free_coffee_applied = true;
    }
}

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $customer_id && !empty($cart_items)) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $payment_method = $_POST['payment_method'];

    if (empty($name) || empty($phone) || empty($payment_method)) {
        $error = "Please fill in all required fields.";
    } else {
        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (customer_id, customer_name, phone, total_price, payment_method) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issds", $customer_id, $name, $phone, $total_price, $payment_method);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        // Insert order items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, type, sugar, addon, quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($cart_items as $item) {
            $stmt->bind_param(
                "iisdsssi",
                $order_id,
                $item['product_id'],
                $item['product_name'],
                $item['price'],
                $item['type'],
                $item['sugar'],
                $item['addon'],
                $item['quantity']
            );
        $stmt->execute();
        }
        $stmt->close();

        // Clear cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE customer_id = ?");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $stmt->close();

        // Calculate new points
        $points_earned = floor($total_price); // 1 point per RM1
        $new_total_points = $current_points + $points_earned;

        // Update customer points
        $stmt = $conn->prepare("UPDATE customers SET points = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_total_points, $customer_id);
        $stmt->execute();
        $stmt->close();

        // Insert loyalty point history
        $stmt = $conn->prepare("INSERT INTO loyalty_history (customer_id, order_id, points_earned, points_redeemed) VALUES (?, ?, ?, ?)");
        $points_redeemed = $free_coffee_applied ? 1000 : 0;
        $stmt->bind_param("iiii", $customer_id, $order_id, $points_earned, $points_redeemed);
        $stmt->execute();
        $stmt->close();

        // Pass free coffee info to thank_you page
        $_SESSION['free_coffee_used'] = $free_coffee_applied;
        $_SESSION['points_earned'] = $points_earned;

        header("Location: thank_you.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans pt-20">

    <div class="max-w-2xl w-full mx-auto space-y-8 px-4">
        <h1 class="text-4xl font-bold text-center text-white">Checkout</h1>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 p-4 rounded">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-white shadow-md rounded-xl px-8 pt-6 pb-8 space-y-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Name</label>
                <input type="text" name="name" class="w-full border border-gray-300 px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required />
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Phone Number</label>
                <input type="text" name="phone" class="w-full border border-gray-300 px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required />
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-1">Payment Method</label>
                <select name="payment_method" class="w-full border border-gray-300 px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    <option value="">-- Select Payment Method --</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="Cash">Cash</option>
                    <option value="Online Banking">Online Banking</option>
                </select>
            </div>

            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-2">Your Items</h2>
                <ul class="list-disc list-inside text-gray-700 space-y-1">
                    <?php foreach ($cart_items as $item): ?>
                        <li>
                            <?= htmlspecialchars($item['product_name']) ?> (x<?= $item['quantity'] ?>) - RM <?= number_format($item['price'] * $item['quantity'], 2) ?>
                            <br>
                            <small class="text-gray-500">
                                Category: <?= htmlspecialchars($item['type']) ?> | Sugar: <?= htmlspecialchars($item['sugar']) ?> | Add-ons: <?= $item['addon'] ? htmlspecialchars($item['addon']) : 'None' ?>
                            </small>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if ($free_coffee_applied): ?>
                    <p class="mt-4 text-green-600 font-semibold">🎉 RM10 discount applied for your free coffee redemption!</p>
                <?php endif; ?>

                <p class="mt-4 font-bold text-right text-blue-700 text-lg">Total: RM <?= number_format($total_price, 2) ?></p>
            </div>

            <div class="text-center">
                <button type="submit" class="bg-[#86CFED] text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300 hover:bg-white hover:text-[#2FA5D4]">
                    Place Order
                </button>
            </div>
        </form>
    </div>
</body>
</html>