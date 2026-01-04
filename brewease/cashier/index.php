<?php
session_start();
require_once '../classes/DBConnection.php';

// Instantiate the DBConnection class
$db = new DBConnection();
$conn = $db->getConnection();  // now $conn holds the mysqli object

// If already logged in, redirect to menu.php
if (isset($_SESSION['user']) && $_SESSION['user']['type'] == 2) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND type = 2 AND status = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Incorrect password.';
        }
    } else {
        $error = 'Cashier not found or invalid user type.';
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cashier Login - BrewEase</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-tr from-lime-200 to-cyan-300 font-sans h-screen">

  <!-- Header section -->
  <header class="text-center pt-12 pb-16 px-4">
    <div class="flex justify-center items-center gap-6 mb-6">
      <img src="../assets/icons/brewease.png" alt="BrewEase Logo" class="w-24 rounded-full shadow-lg">
      <img src="../assets/icons/perfectplace.png" alt="The Perfect Place Logo" class="w-24 rounded-full shadow-lg">
    </div>
    <h1 class="text-4xl font-bold text-white">Welcome to BrewEase</h1>
    <p class="text-white mt-2">Your all-in-one coffee shop order management system</p>
  </header>

  <!-- Login form -->
  <div class="flex justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-center text-[#4CAF50]">Cashier Login</h2>

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
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
          />
        </div>

        <div>
          <label class="block mb-1 font-medium" for="password">Password</label>
          <input
            id="password"
            name="password"
            type="password"
            required
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
          />
        </div>

        <button
          type="submit"
          class="w-full bg-[#8FDEC2] text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300 hover:bg-white hover:text-[#38813A]"
        >
          Login
        </button>
      </form>
    </div>
  </div>

</body>
</html>
