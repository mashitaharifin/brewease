<?php
require_once '../classes/DBConnection.php';
require_once 'check_auth.php';

// Database connection
$db = new DBConnection();
$conn = $db->getConnection();

// Update status
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
$cashierName = $_SESSION['user']['firstname'] ?? 'Cashier';
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
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Orders - BrewEase Cashier</title>
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
      border-radius: 0.375rem;
      padding: 0.25rem 0;
      border: 1px solid #e5e7eb;
    }
    .dropdown-content button {
      width: 100%;
      padding: 0.5rem 1rem;
      text-align: left;
      font-size: 0.875rem;
      color: #374151;
      background: none;
      border: none;
      cursor: pointer;
    }
    .dropdown-content button:hover {
      background-color: #f9fafb;
    }
    .dropdown.show .dropdown-content {
      display: block;
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-tr from-lime-200 to-cyan-300">
<?php include 'navbar.php'; ?>

<div class="max-w-7xl mx-auto p-4">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-3xl font-bold text-white text-center">Order Management</h1>
    <a href="neworder.php" class="bg-white text-[#438E74] hover:bg-[#8FDEC2] hover:text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300">
      + Add New Order</a>
  </div>


  <div class="overflow-x-auto bg-white rounded-2xl shadow">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
          <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total (RM)</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($order = $result->fetch_assoc()): ?>
            <tr>
              <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($order['id']) ?></td>
              <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($order['customer_name']) ?></td>
              <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($order['phone']) ?></td>
              <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($order['payment_method']) ?></td>
              <td class="px-6 py-4 text-sm text-right text-gray-900"><?= number_format($order['total_price'], 2) ?></td>
              <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($order['created_at']) ?></td>
              <td class="px-6 py-4 text-sm text-gray-700">
                <div class="dropdown" tabindex="0">
                  <?= statusBadge($order['status']) ?>
                  <div class="dropdown-content mt-1">
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
              <td class="px-6 py-4 text-center text-sm font-medium">
                <a href="orderdetails.php?id=<?= urlencode($order['id']) ?>" class="text-indigo-600 hover:text-purple-500">View Details</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="8" class="px-6 py-4 text-center text-gray-500">No orders found.</td>
          </tr>
        <?php endif; ?>
        <?php $result->free(); ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  document.querySelectorAll('.dropdown').forEach(dropdown => {
    dropdown.addEventListener('click', function (e) {
      e.stopPropagation();
      document.querySelectorAll('.dropdown').forEach(d => {
        if (d !== this) d.classList.remove('show');
      });
      this.classList.toggle('show');
    });
  });

  document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('show'));
  });
</script>

</body>
</html>