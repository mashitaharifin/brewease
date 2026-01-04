<?php
require_once '../classes/DBConnection.php';
$db = new DBConnection();
$conn = $db->getConnection();

session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$users = [];
if ($conn) {
    $result = $conn->query("SELECT id, firstname, lastname, username, type, date_added, status FROM users ORDER BY id DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
} else {
    echo "Database connection failed";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Users Management - BrewEase Manager</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-indigo-300 to-fuchsia-400">

  <?php include 'navbar.php'; ?>

  <main class="pt-5">
    <div class="max-w-7xl mx-auto px-4">
        
    <div class="flex items-center justify-between mb-1">
      <h1 class="text-3xl font-bold text-center text-white">Users Management</h1>
      <a href="add_user.php" class="bg-white text-indigo-700 hover:bg-[#AD85FD] hover:text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300">
        + Add New User</a>
    </div>

    <div class="flex items-center justify-between mb-6"></div>

    <table class="min-w-full bg-white rounded-2xl shadow overflow-hidden">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Added</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <?php if (count($users) > 0): ?>
          <?php foreach ($users as $user): ?>
            <tr class="border-b border-gray-200 hover:bg-gray-100">
              <td class="py-3 px-6 text-left"><?= htmlspecialchars($user['id']) ?></td>
              <td class="py-3 px-6 text-left"><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?></td>
              <td class="py-3 px-6 text-left"><?= htmlspecialchars($user['username']) ?></td>
              <td class="py-3 px-6 text-left">
                <?= htmlspecialchars($user['username'] === 'manager' ? 'Manager' : 'Cashier') ?>
              </td>
              <td class="py-3 px-6 text-left"><?= htmlspecialchars($user['date_added']) ?></td>
              <td class="py-3 px-6 text-left"><span class="inline-block px-2 py-1 text-sm rounded-full font-medium 
                  <?= $user['status'] == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                  <?= $user['status'] == 1 ? 'Active' : 'Inactive' ?></span></td>
              <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                <a href="edit_user.php?id=<?= $user['id'] ?>" class="text-indigo-600 hover:text-[#AD85FD]">Edit</a>
                <a href="delete_user.php?id=<?= $user['id'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Delete this user?');">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-center py-4">No users found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </main>

</body>
</html>
