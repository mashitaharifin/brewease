<?php
session_start();

// If already logged in, redirect to dashboard.php
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // TODO: Replace with your actual admin validation (DB or hardcoded)
    $valid_username = 'manager';
    $valid_password = 'password123'; // Replace with hashed password in real app!

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Manager Login - BrewEase</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-indigo-300 to-fuchsia-400 flex flex-col items-center min-h-screen font-sans">

  <!-- Header section: same placement as customer login -->
  <header class="text-center pt-12 pb-16 px-4">
    <div class="flex justify-center items-center gap-6 mb-6">
      <img src="../assets/icons/brewease.png" alt="BrewEase Logo" class="w-24 rounded-full shadow-lg">
      <img src="../assets/icons/perfectplace.png" alt="The Perfect Place Logo" class="w-24 rounded-full shadow-lg">
    </div>
    <h1 class="text-4xl font-bold text-white">Welcome to BrewEase</h1>
    <p class="text-white mt-2">Your all-in-one coffee shop order management system</p>
  </header>

  <!-- Login form container -->
  <div class="flex justify-center w-full px-4">
    <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-center text-[#AD85FD]">Manager Login</h2>

      <?php if (!empty($error)): ?>
        <div class="mb-4 text-red-600 font-semibold"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="" class="space-y-4">
        <div>
          <label class="block mb-1 font-medium" for="username">Username</label>
          <input
            id="username"
            name="username"
            type="text"
            required
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <div>
          <label class="block mb-1 font-medium" for="password">Password</label>
          <input
            id="password"
            name="password"
            type="password"
            required
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <button
          type="submit"
          class="hover:bg-white hover:text-indigo-700 bg-[#AD85FD] text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300 w-full"
        >Login</button>
      </form>
    </div>
  </div>

</body>
</html>
