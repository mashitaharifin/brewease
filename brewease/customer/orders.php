<?php
include 'header.php';
// session_start(); // ❌ Remove this line if session is already started in header.php
require_once '../config.php';

if (!isset($_SESSION["customer_id"])) {
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION["customer_id"];

$stmt = $conn->prepare("SELECT id, total_price, payment_method, created_at FROM orders WHERE customer_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6 min-h-screen">
    <div class="max-w-5xl mx-auto bg-white p-6 rounded-xl shadow">
        <h1 class="text-3xl font-bold text-[#86CFED] mb-6">🧾 Your Orders</h1>

        <?php if (empty($orders)): ?>
            <p class="text-gray-600">You haven’t placed any orders yet.</p>
        <?php else: ?>
            <table class="w-full text-sm text-left border rounded overflow-hidden">
                <thead style="background-color: #86CFED;" class="text-white">
                    <tr>
                        <th class="p-3">Order ID</th>
                        <th class="p-3">Total (RM)</th>
                        <th class="p-3">Payment</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="p-3">#<?= $order['id'] ?></td>
                            <td class="p-3"><?= number_format($order['total_price'], 2) ?></td>
                            <td class="p-3"><?= htmlspecialchars($order['payment_method']) ?></td>
                            <td class="p-3"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></td>
                            <td class="p-3">
                                <a href="order_details.php?id=<?= $order['id'] ?>" class="text-[#86CFED] hover:underline font-medium">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
