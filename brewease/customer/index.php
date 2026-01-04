<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Customer Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gradient-to-r from-purple-300 via-cyan-200 to-teal-100 font-sans">
  <?php
    require_once '../config.php';
    include 'header.php';

    if (!isset($_SESSION['customer_id'])) {
      header("Location: login.php");
      exit();
    }
    $customer_id = $_SESSION['customer_id'];
    $stmt = $conn->prepare("SELECT points FROM customers WHERE id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $stmt->bind_result($points);
    $stmt->fetch();
    $stmt->close();
  ?>

  <div class="max-w-5xl mx-auto px-4 py-6">
    <!-- Welcome Card -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-6">
      <h1 class="text-3xl font-bold text-[#86CFED] mb-2">Welcome, <?php echo $_SESSION['customer_name']; ?> 👋</h1>
      <p class="text-gray-600">Here’s your BrewEase dashboard summary.</p>

      <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-3">
          <span class="items-center px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-full">
            ⭐ Loyalty Points: <strong class="ml-1"><?php echo $points; ?></strong>
          </span>
        </div>

        <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-400 text-blue-700 rounded-md">
          💡 Earn 1 point for every RM1 spent. Once you reach <strong>1000 points</strong>, you can redeem a <strong>free coffee</strong> at checkout!
        </div>

        <?php if ($points >= 1000): ?>
        <div class="mt-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-md">
          🎉 You have a <strong>free coffee</strong> ready to claim at your next checkout!
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
      <a href="menu.php" class="bg-white shadow hover:shadow-md hover:bg-blue-100 transition rounded-lg p-4 text-center">
        <div class="text-yellow-600 text-2xl mb-2">☕</div>
        <p class="text-gray-800 font-semibold">View Menu</p>
      </a>
      <a href="cart.php" class="bg-white shadow hover:shadow-md hover:bg-blue-100 transition rounded-lg p-4 text-center">
        <div class="text-yellow-600 text-2xl mb-2">🛒</div>
        <p class="text-gray-800 font-semibold">Your Cart</p>
      </a>
      <a href="orders.php" class="bg-white shadow hover:shadow-md hover:bg-blue-100 transition rounded-lg p-4 text-center">
        <div class="text-yellow-600 text-2xl mb-2">📦</div>
        <p class="text-gray-800 font-semibold">Order History</p>
      </a>
      <a href="loyalty_history.php" class="bg-white shadow hover:shadow-md hover:bg-blue-100 transition rounded-lg p-4 text-center">
        <div class="text-yellow-600 text-2xl mb-2">🏱</div>
        <p class="text-gray-800 font-semibold">Loyalty History</p>
      </a>
    </div>

   <!-- About & Contact Section -->
<div class="grid grid-cols-1 md:grid-cols-1 gap-6 mb-6">
  <!-- About -->
  <div class="bg-white shadow-md rounded-xl p-6 hover:shadow-lg transition-shadow duration-300">
    <h2 class="text-xl font-bold text-[#86CFED] flex items-center mb-3">
      <i class="fas fa-mug-hot mr-2"></i>About The Perfect Place
    </h2>
    <p class="text-gray-700 mb-3">
      Nestled in the heart of the city, <span class="font-semibold text-[#AD85FD]">The Perfect Place</span> is your cozy neighborhood café where every cup brings warmth and joy. Whether you're working, catching up with friends, or just unwinding, we're always happy to serve you your perfect brew! 💖
    </p>
    <p class="text-gray-700 mb-1">
      <i class="fas fa-map-marker-alt mr-2 text-pink-400"></i>📍 <strong>Location:</strong> No 43, Jalan Mawar 9, Taman Mawar, 81700 Pasir Gudang, Johor
    </p>
    <p class="text-gray-700">
      <i class="fas fa-clock mr-2 text-yellow-500"></i>📅 <strong>Opening Hours:</strong> Everyday except Wednesday</span>, from 3PM to 11PM
    </p>
    <p class="text-gray-700 mb-1">
      <i class="fas fa-contact-alt mr-2 text-pink-400"></i>📞 <strong>Contact:</strong> +6012-3456789
    </p>
    <p class="text-gray-700 mb-1">
      <i class="fas fa-instagram-alt mr-2 text-pink-400"></i>📸 <strong>Instagram:</strong> <a href="http://instagram.com/perfectplaceofc" target="_blank" class="text-blue-500 hover:underline font-medium">@perfectplaceofc</a>
    </p>
  </div>
</div>


    <!-- Coffee Quote -->
    <div class="bg-white shadow rounded-xl p-5 text-center text-gray-700 italic">
      "Coffee is a hug in a mug. ☕ Keep sipping, keep earning!"
    </div>
  </div>

  <script>
    function updateCartCount() {
      $.get('../customer/cart_count_api.php', function(data) {
        $('#cart-count').text(data.count);
      });
    }

    $(document).ready(function() {
      updateCartCount();
      setInterval(updateCartCount, 5000);
    });
  </script>
</body>
</html>
