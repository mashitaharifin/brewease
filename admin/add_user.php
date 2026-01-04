<?php
session_start();
require_once '../classes/DBConnection.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$db = new DBConnection();
$conn = $db->getConnection();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];    $confirm_password = $_POST['confirm_password'];
    $status = $_POST['status'];
    $date_added = date('Y-m-d H:i:s');
    $type = 2; // Cashier

    if ($password !== $confirm_password) {
    $error = "Passwords do not match.";
    } else {
      $hashed = password_hash($password, PASSWORD_DEFAULT);
        // Check if username already exists
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Username already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, username, password, type, status, date_added) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssis", $firstname, $lastname, $username, $hashed, $type, $status, $date_added);

            if ($stmt->execute()) {
                $success = "Cashier account created successfully.";
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add User</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-indigo-300 to-fuchsia-400">

  <?php include 'navbar.php'; ?>

  <main class="max-w-7xl mx-auto p-6 mt-8 bg-white rounded-2xl shadow">
    <h1 class="text-2xl font-bold text-[#AD85FD] mb-4">Add New User</h1>

    <?php if ($success): ?>
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="bg-red-100 text-red-800 p-3 rounded mb-4"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium">First Name</label>
        <input type="text" name="firstname" required class="w-full border border-gray-300 rounded px-3 py-2">
      </div>

      <div>
        <label class="block text-sm font-medium">Last Name</label>
        <input type="text" name="lastname" required class="w-full border border-gray-300 rounded px-3 py-2">
      </div>

      <div>
        <label class="block text-sm font-medium">Username</label>
        <input type="text" name="username" required class="w-full border border-gray-300 rounded px-3 py-2">
      </div>

      <div>
        <label class="block text-sm font-medium">Password</label>
        <input type="password" name="password" required class="w-full border border-gray-300 rounded px-3 py-2">
      </div>

      <div>
        <label class="block text-sm font-medium">Confirm Password</label>
        <input type="password" name="confirm_password" required class="w-full border border-gray-300 rounded px-3 py-2">
      </div>

      <div>
        <label class="block text-sm font-medium">Role</label>
        <input type="text" value="Cashier" readonly class="w-full bg-gray-100 border border-gray-300 rounded px-3 py-2 text-gray-600">
      </div>

      <div>
        <label class="block text-sm font-medium">Status</label>
        <select name="status" class="w-full border border-gray-300 rounded px-3 py-2" required>
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>

      <button type="submit" class="hover:bg-white hover:text-indigo-700 bg-[#AD85FD] text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300">Add User</button>
    </form>
  </main>

</body>
</html>
