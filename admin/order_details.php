<?php
session_start();
require_once '../classes/DBConnection.php';

$db = new DBConnection();
$conn = $db->getConnection();

$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($orderId <= 0) {
    header('Location: orders.php');
    exit;
}

// Fetch order info
$stmt = $conn->prepare("SELECT id, customer_name, phone, payment_method, total_price, created_at, status FROM orders WHERE id = ?");
$stmt->bind_param('i', $orderId);
$stmt->execute();
$orderResult = $stmt->get_result();
$order = $orderResult->fetch_assoc();

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Fetch order items
$stmtItems = $conn->prepare("SELECT product_name, quantity, price, addon FROM order_items WHERE order_id = ?");
$stmtItems->bind_param('i', $orderId);
$stmtItems->execute();
$itemsResult = $stmtItems->get_result();

function statusBadge($status) {
    $colors = [
        'Preparing' => 'bg-yellow-100 text-yellow-800',
        'Ready' => 'bg-blue-100 text-blue-800',
        'Completed' => 'bg-green-100 text-green-800',
    ];
    $colorClass = $colors[$status] ?? 'bg-gray-100 text-gray-800';
    return "<span class=\"px-2 inline-flex text-xs leading-5 font-semibold rounded-full $colorClass\">$status</span>";
}
?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Order Details - BrewEase Manager</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-indigo-300 to-fuchsia-400">
<?php include 'navbar.php'; ?>

<div class="max-w-3xl mx-auto p-6">
    <header class="mb-6">
        <h1 class="text-3xl font-bold text-white mb-2">Order Details</h1>
        <a href="orders.php" class="text-white hover:text-indigo-800">&larr; Back to Orders</a>
    </header>

    <section class="mb-8 bg-white rounded-2xl shadow p-6">
        <h2 class="text-xl font-semibold text-[#AD85FD] mb-4">Order Information</h2>
        <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm text-gray-700">
            <dt class="font-semibold">Order ID:</dt>
            <dd><?= htmlspecialchars($order['id']) ?></dd>

            <dt class="font-semibold">Customer Name:</dt>
            <dd><?= htmlspecialchars($order['customer_name']) ?></dd>

            <dt class="font-semibold">Phone:</dt>
            <dd><?= htmlspecialchars($order['phone']) ?></dd>

            <dt class="font-semibold">Payment Method:</dt>
            <dd><?= htmlspecialchars($order['payment_method']) ?></dd>

            <dt class="font-semibold">Order Date:</dt>
            <dd><?= htmlspecialchars($order['created_at']) ?></dd>

            <dt class="font-semibold">Status:</dt>
            <dd><?= statusBadge($order['status']) ?></dd>
        </dl>
    </section>

    <section class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-xl font-semibold text-[#AD85FD] mb-4">Items</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Add-on</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Price (RM)</th>
                        <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Subtotal (RM)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <?php
                    $totalCalculated = 0;
                    while ($item = $itemsResult->fetch_assoc()):
                        $subtotal = $item['quantity'] * $item['price'];
                        $totalCalculated += $subtotal;
                    ?>
                    <tr>
                        <td class="px-6 py-4"><?= htmlspecialchars($item['product_name']) ?></td>
                        <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($item['addon'] ?? '-') ?></td>
                        <td class="px-6 py-4 text-right"><?= intval($item['quantity']) ?></td>
                        <td class="px-6 py-4 text-right"><?= number_format($item['price'], 2) ?></td>
                        <td class="px-6 py-4 text-right"><?= number_format($item["price"] * $item["quantity"], 2) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-semibold text-gray-900">
                        <td colspan="3" class="px-6 py-3 text-right">Total</td>
                        <td class="px-6 py-3 text-right"><?= number_format($order['total_price'], 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>
</div>

</body>
</html>
