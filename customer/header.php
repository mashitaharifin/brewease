<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-purple-300 via-cyan-200 to-teal-100 font-sans">
  
 <!-- customer/header.php -->
<nav class="bg-purple-500 bg-opacity-20 p-4 flex justify-between items-center">
  <!-- Left: Logo -->
  <a href="index.php" class="text-xl font-semibold text-white">BrewEase: Customer</a>

  <!-- Center: Navigation Links -->
  <ul class="flex gap-6 text-white">
    <li><a href="index.php" class="hover:text-[#236DAA] font-bold hover:font-bold transition">Dashboard</a></li>
    <li><a href="menu.php" class="hover:text-[#236DAA] font-bold hover:font-bold transition">Menu</a></li>
    <li><a href="orders.php" class="hover:text-[#236DAA] font-bold hover:font-bold transition">Orders</a></li>
    <li><a href="profile.php" class="hover:text-[#236DAA] font-bold hover:font-bold transition">Profile</a></li>
    <li><a href="loyalty_history.php" class="hover:text-[#236DAA] font-bold hover:font-bold transition">Loyalty</a></li>
    <li><a href="cart.php" class="relative text-gray-600 hover:text-indigo-600">🛒<span id="cart-count" class="absolute -top-2 -right-3 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span></a></li>
  </ul>

  <!-- Right: Customer Info + Cart + Logout -->
  <div class="flex items-center gap-6">
    <!-- Customer Avatar & Label -->
    <div class="flex items-center gap-2 font-bold text-white">
      <span>👤 Customer</span>
    </div>
    
    <!-- Logout Link -->
    <a href="logout.php" class="text-white hover:text-[#B983F6] transition font-bold hover:font-bold">Logout</a>
  </div>
</nav>

    <main class="max-w-4xl mx-auto px-4 py-8"></main>