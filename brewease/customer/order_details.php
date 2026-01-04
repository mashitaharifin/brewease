<?php
include 'header.php';
require_once '../config.php';

if (!isset($_SESSION["customer_id"])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET["id"])) {
    echo "Order ID is missing.";
    exit;
}

$order_id = intval($_GET["id"]);
$customer_id = $_SESSION["customer_id"];

// Fetch the order
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND customer_id = ?");
$stmt->bind_param("ii", $order_id, $customer_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    echo "Order not found.";
    exit;
}

$order = $order_result->fetch_assoc();
$stmt->close();

// Fetch the items for this order
$item_stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$item_stmt->bind_param("i", $order_id);
$item_stmt->execute();
$item_result = $item_stmt->get_result();
$items = [];
while ($row = $item_result->fetch_assoc()) {
    $items[] = $row;
}
$item_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?= $order_id ?> | BrewEase</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow">
        <h1 class="text-2xl font-bold text-[#86CFED] mb-4">📄 Order #<?= $order_id ?></h1>

        <div class="mb-4 text-gray-800">
            <p><strong>Customer:</strong> <?= htmlspecialchars($order["customer_name"]) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($order["phone"]) ?></p>
            <p><strong>Payment Method:</strong> <?= htmlspecialchars($order["payment_method"]) ?></p>
            <p><strong>Date:</strong> <?= date("d M Y, h:i A", strtotime($order["created_at"])) ?></p>
            <p><strong>Grand Total:</strong> RM <?= number_format($order["total_price"], 2) ?></p>
        </div>

        <h2 class="text-xl font-semibold mb-2">🛍️ Items</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border rounded overflow-hidden">
                <thead style="background-color: #86CFED;" class="text-white">
                    <tr>
                        <th class="p-2">Product</th>
                        <th class="p-2">Category</th>
                        <th class="p-2">Sugar</th>
                        <th class="p-2">Add-on</th>
                        <th class="p-2">Quantity</th>
                        <th class="p-2">Price</th>
                        <th class="p-2">Sub Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="p-2"><?= htmlspecialchars($item["product_name"]) ?></td>
                            <td class="p-2"><?= htmlspecialchars($item["type"]) ?></td>
                            <td class="p-2"><?= htmlspecialchars($item["sugar"]) ?></td>
                            <td class="p-2"><?= htmlspecialchars($item["addon"]) ?></td>
                            <td class="p-2"><?= $item["quantity"] ?></td>
                            <td class="p-2">RM <?= number_format($item["price"], 2) ?></td>
                            <td class="p-2">RM <?= number_format($item["price"] * $item["quantity"], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <a href="orders.php" class="inline-block mt-6 text-[#86CFED] hover:font-bold font-medium">← Back to Orders</a>
    </div>
</body>
</html>
