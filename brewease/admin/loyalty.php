<?php
session_start();
require_once '../classes/dbconnection.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$db = new DBConnection();
$conn = $db->getConnection();

// Handle search query
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

// Prepare SQL with optional search filter
$sql = "SELECT id, fullname, email, points FROM customers";
$params = [];

if ($search !== '') {
    $sql .= " WHERE fullname LIKE ? OR email LIKE ?";
    $likeSearch = "%$search%";
    $params = [$likeSearch, $likeSearch];
}

$stmt = $conn->prepare($sql);

if ($params) {
    $stmt->bind_param('ss', ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Loyalty Points Management - BrewEase Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-indigo-300 to-fuchsia-400">
<?php include 'navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 pt-5">
        <h1 class="text-3xl font-bold text-white mb-5">Loyalty Points Management</h1>

        <form method="GET" action="loyalty.php" class="mb-6 flex flex-wrap items-center gap-3">
            <input
                type="text"
                name="search"
                placeholder="Search by name or email"
                value="<?php echo htmlspecialchars($search); ?>"
                class="px-4 py-2 border border-gray-300 rounded w-72 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            />
            <button type="submit" class="bg-white text-indigo-700 hover:bg-[#AD85FD] hover:text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300">
                Search
            </button>
            <?php if ($search !== ''): ?>
                <a href="loyalty.php" class="bg-white text-indigo-700 hover:bg-[#AD85FD] hover:text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300">Clear</a>
            <?php endif; ?>
        </form>

        <div class="overflow-x-auto bg-white rounded-2xl shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-indigo-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo $row['id']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['fullname']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600"><?php echo htmlspecialchars($row['email']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800"><?php echo $row['points']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No customers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
