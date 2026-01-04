<?php
require_once '../classes/dbconnection.php';

// Instantiate the DBConnection class
$db = new DBConnection();
$conn = $db->getConnection();  // now $conn holds the mysqli object

// Example: Fetch today's sales amount
$sales_today = 0;
$total_orders = 0;
$total_customers = 0;
$total_staff = 0;

// Check if connection exists
if ($conn) {
    // Your existing queries...
    $result = $conn->query("SELECT SUM(total_price) as total_sales FROM orders WHERE DATE(created_at) = CURDATE()");
    if ($result && $row = $result->fetch_assoc()) {
        $sales_today = $row['total_sales'] ?? 0;
    }

    $result = $conn->query("SELECT COUNT(*) as order_count FROM orders");
    if ($result && $row = $result->fetch_assoc()) {
        $total_orders = $row['order_count'] ?? 0;
    }

    $result = $conn->query("SELECT COUNT(*) as customer_count FROM customers WHERE id != 5");
    if ($result && $row = $result->fetch_assoc()) {
        $total_customers = $row['customer_count'] ?? 0;
    }

} else {
    echo "Database connection failed";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manager Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-indigo-300 to-fuchsia-400">

  <?php include 'navbar.php'; ?>

  <main class="p-6">
    <h1 class="text-3xl font-bold text-white text-center mb-5">Dashboard Overview</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-7xl mx-auto">
      <div class="bg-white p-6 rounded-2xl shadow">
        <h2 class="text-gray-600">Today's Total Sales</h2>
        <p class="text-2xl font-bold text-green-600">RM <?= number_format($sales_today, 2) ?></p>
      </div>
      <div class="bg-white p-6 rounded-2xl shadow">
        <h2 class="text-gray-600">Total Orders</h2>
        <p class="text-2xl font-bold text-blue-600"><?= $total_orders ?></p>
      </div>
      <div class="bg-white p-6 rounded-2xl shadow">
        <h2 class="text-gray-600">Registered Customers</h2>
        <p class="text-2xl font-bold text-purple-600"><?= $total_customers ?></p>
      </div>
      <div class="bg-white p-6 rounded-2xl shadow">
        <h2 class="text-gray-600">Today’s Revenue</h2>
        <p class="text-2xl font-bold text-orange-600">RM <?= number_format($sales_today, 2) ?></p>
      </div>
    </div>
  </main>

</body>
</html>
