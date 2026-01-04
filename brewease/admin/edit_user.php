<?php
require_once '../classes/dbconnection.php';
$db = new DBConnection();
$conn = $db->getConnection();

if (!$conn) {
    die("Database connection failed");
}

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid user ID");
}

// Fetch user data
$stmt = $conn->prepare("SELECT id, firstname, lastname, username, status FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $type = 2; // Force role to cashier
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ?, username = ?, type = ?, status = ? WHERE id = ?");
    $stmt->bind_param("ssssii", $firstname, $lastname, $username, $type, $status, $id);

    if ($stmt->execute()) {
        header("Location: users.php");
        exit();
    } else {
        echo "Failed to update user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit User</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-indigo-300 to-fuchsia-400">

  <?php include 'navbar.php'; ?>

  <main class="pt-6">
  <div class="max-w-7xl mx-auto p-6">
    <div class="max-w-md mx-auto bg-white p-6 rounded-2xl shadow-md">
    <h1 class="text-2xl font-bold mb-6 text-[#AD85FD] text-center">Edit User</h1>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-gray-700">First Name</label>
        <input type="text" name="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" required class="w-full p-2 border rounded" />
      </div>

      <div>
        <label class="block text-gray-700">Last Name</label>
        <input type="text" name="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" required class="w-full p-2 border rounded" />
      </div>

      <div>
        <label class="block text-gray-700">Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required class="w-full p-2 border rounded" />
      </div>

      <div>
        <label class="block text-gray-700">Role</label>
        <input type="text" value="<?= $user['username'] === 'manager' ? 'Manager' : 'Cashier' ?>" readonly class="w-full bg-gray-100 text-gray-600 p-2 border rounded" />
      </div>

      <div>
        <label class="block text-gray-700">Status</label>
        <select name="status" class="w-full p-2 border rounded" required>
        <option value="1" <?= $user['status'] == 1 ? 'selected' : '' ?>>Active</option>
        <option value="0" <?= $user['status'] == 0 ? 'selected' : '' ?>>Inactive</option></select>
      </div>

      <button type="submit" class="bg-white text-indigo-700 hover:bg-[#AD85FD] hover:text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300 w-full">
        Update User</button>
    </form>
  </div>
  </>

</body>
</html>