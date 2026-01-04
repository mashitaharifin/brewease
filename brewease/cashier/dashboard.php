<?php
require_once '../classes/DBConnection.php';

// Instantiate the DBConnection class
$db = new DBConnection();
$conn = $db->getConnection();  // now $conn holds the mysqli object

require_once 'check_auth.php';
$cashierName = $_SESSION['user']['firstname'] ?? 'Cashier';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cashier Dashboard - BrewEase</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-tr from-lime-200 to-cyan-300">

  <!-- Navbar -->
  <?php include 'navbar.php'; ?>

  <!-- Main Content Wrapper -->
  <main class="p-6">
    <div class="max-w-7xl mx-auto">
      <h1 class="text-3xl text-white text-center font-bold mb-4">Welcome, <?php echo htmlspecialchars($cashierName); ?>!</h1>
      <p class="mb-6 text-lg text-white text-center">Here’s what you can do today.</p>

      <!-- Action Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="neworder.php" class="bg-white rounded-2xl shadow-lg p-6 hover:bg-green-100 transition-all">
          <h2 class="text-xl font-semibold text-green-700 mb-2">Start New Order</h2>
          <p class="text-gray-600">Create and manage new customer orders quickly.</p>
        </a>

        <a href="orders.php" class="bg-white rounded-2xl shadow-lg p-6 hover:bg-green-100 transition-all">
          <h2 class="text-xl font-semibold text-green-700 mb-2">View Orders</h2>
          <p class="text-gray-600">See a list of recent transactions handled.</p>
        </a>
      </div>
    </div>
  </main>

</body>
</html>
