<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>BrewEase Portal</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .role-card:hover {
      transform: scale(1.05);
    }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-tr from-emerald-300 via-sky-300 to-purple-300 min-h-screen flex flex-col justify-between">

  <!-- Header -->
  <header class="text-center pt-8 pb-12 px-4">
    <div class="flex justify-center items-center gap-6 mb-4">
        <img src="assets/icons/brewease.png" alt="BrewEase Logo" class="w-24 rounded-full">
        <img src="assets/icons/perfectplace.png" alt="The Perfect Place Logo" class="w-24 rounded-full">
    </div>
    <h1 class="text-4xl font-bold text-white">Welcome to BrewEase</h1>
    <p class="text-white mt-2">Your all-in-one coffee shop order management system</p>
  </header>

  <!-- Main Role Selector -->
  <main class="flex-1 container mx-auto px-4 pb-12">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
      
      <!-- Admin Card -->
      <div class="role-card bg-white rounded-2xl shadow-lg p-6 text-center transition-transform duration-300 hover:shadow-2xl cursor-pointer">
        <img src="assets/icons/admin.png" alt="Admin Icon" class="w-16 h-16 mx-auto mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Manager</h2>
        <p class="text-sm text-gray-500 mt-2">Manage users, menu, orders, and generate sales analytic & reporting.</p>
        <a href="admin/index.php" target="_blank" class="inline-block mt-4 bg-[#C7B6FD] text-white px-4 py-2 rounded-xl hover:bg-[#87D9FA]">Login as Manager</a>
      </div>

      <!-- Cashier Card -->
      <div class="role-card bg-white rounded-2xl shadow-lg p-6 text-center transition-transform duration-300 hover:shadow-2xl cursor-pointer">
        <img src="assets/icons/cashier.png" alt="Cashier Icon" class="w-16 h-16 mx-auto mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Cashier</h2>
        <p class="text-sm text-gray-500 mt-2">Take orders for offline customers and process orders.</p>
        <a href="cashier/index.php" target="_blank" class="inline-block mt-4 bg-[#C7B6FD] text-white px-4 py-2 rounded-lg hover:bg-[#87D9FA]">Login as Cashier</a>
      </div>

      <!-- Customer Card -->
      <div class="role-card bg-white rounded-2xl shadow-lg p-6 text-center transition-transform duration-300 hover:shadow-2xl cursor-pointer">
        <img src="assets/icons/customer.png" alt="Customer Icon" class="w-16 h-16 mx-auto mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Customer</h2>
        <p class="text-sm text-gray-500 mt-2">View menu, place orders, collect loyalty points and redeem points.</p>
        <a href="customer/login.php" target="_blank" class="inline-block mt-4 bg-[#C7B6FD] text-white px-4 py-2 rounded-lg hover:bg-[#87D9FA]">Continue as Customer</a>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-white/70 text-center py-4 shadow-inner">
    <p class="text-gray-500 text-sm">
      &copy; <?= date("Y") ?> BrewEase. All rights reserved. &nbsp;|&nbsp; Version 1.0.0
    </p>
  </footer>

</body>
</html>