<!-- cashier/navbar.php -->
<nav class="bg-lime-600 bg-opacity-20 p-4 text-white flex justify-between items-center">
  <div class="text-xl font-bold text-white">BrewEase: Cashier</div>

  <ul class="flex gap-6">
    <li><a href="dashboard.php" class="font-bold text-white hover:text-[#207B52] hover:font-bold">Dashboard</a></li>
    <li><a href="orders.php" class="font-bold text-white hover:text-[#207B52] hover:font-bold">Orders</a></li>
  </ul>

  <div class="flex items-center gap-6">
    <div class="flex font-bold text-white items-center gap-2">      
      <span>👤 <?php echo htmlspecialchars($cashierName); ?></span>
    </div>
    <a href="logout.php" class="text-white hover:text-[#0D4286] font-bold hover:font-bold">Logout</a>
  </div>
</nav>

