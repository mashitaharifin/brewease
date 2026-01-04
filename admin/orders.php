<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

require_once '../classes/DBConnection.php';

$db = new DBConnection();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $orderId = intval($_POST['order_id']);
    $status = $_POST['status'];
    $validStatuses = ['Preparing', 'Ready', 'Completed'];
    if (in_array($status, $validStatuses)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $orderId);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: orders.php');
    exit;
}

$result = $conn->query("SELECT id, customer_name, phone, payment_method, total_price, created_at, status FROM orders ORDER BY created_at DESC");

function statusBadge($status) {
    $colors = [
        'Preparing' => 'bg-yellow-100 text-yellow-800',
        'Ready' => 'bg-blue-100 text-blue-800',
        'Completed' => 'bg-green-100 text-green-800',
    ];
    $colorClass = $colors[$status] ?? 'bg-gray-100 text-gray-800';
    return "<span class=\"px-2 inline-flex text-xs leading-5 font-semibold rounded-full $colorClass cursor-pointer\">$status</span>";
}
?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Order Management - BrewEase Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .dropdown {
      position: relative;
      display: inline-block;
    }
    .dropdown-content {
      display: none;
      position: absolute;
      z-index: 10;
      background-color: white;
      min-width: 140px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border-radius: 0.375rem; /* rounded-md */
      padding: 0.25rem 0;
      border: 1px solid #e5e7eb; /* border-gray-200 */
    }
    .dropdown-content button {
      width: 100%;
      padding: 0.5rem 1rem;
      text-align: left;
      font-size: 0.875rem;
      color: #374151; /* gray-700 */
      background: none;
      border: none;
      cursor: pointer;
    }
    .dropdown-content button:hover {
      background-color: #f9fafb; /* gray-50 */
    }
    .dropdown.show .dropdown-content {
      display: block;
    }
  </style>
</head>
<body class="bg-gradient-to-r from-indigo-300 to-fuchsia-400">
<?php include 'navbar.php'; ?>

<main class="pt-5">
  <div class="max-w-7xl mx-auto px-4">
    <h1 class="text-3xl font-bold text-white mb-6">Order Management</h1>

    <div class="overflow-x-auto bg-white rounded-2xl shadow">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">

                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total (RM)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium"><?= htmlspecialchars($order['id']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($order['customer_name']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($order['phone']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($order['payment_method']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900"><?= number_format($order['total_price'], 2) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($order['created_at']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <div class="dropdown" tabindex="0">
                                    <?= statusBadge($order['status']) ?>
                                    <div class="dropdown-content mt-1 rounded-md border border-gray-200">
                                        <?php foreach (['Preparing', 'Ready', 'Completed'] as $statusOption): ?>
                                            <?php if ($order['status'] !== $statusOption): ?>
                                                <form method="POST" class="m-0">
                                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>" />
                                                    <input type="hidden" name="status" value="<?= $statusOption ?>" />
                                                    <button type="submit"><?= $statusOption ?></button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                <a href="order_details.php?id=<?= urlencode($order['id']) ?>" class="text-indigo-600 hover:text-[#AD85FD]">View Details</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No orders found.</td>
                    </tr>
                <?php endif; ?>
                <?php $result->free(); ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.querySelectorAll('.dropdown').forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            document.querySelectorAll('.dropdown').forEach(d => {
                if (d !== this) d.classList.remove('show');
            });
            this.classList.toggle('show');
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown').forEach(dropdown => dropdown.classList.remove('show'));
    });
</script>

</body>
</html>
