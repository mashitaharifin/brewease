<?php
session_start();
require_once '../classes/dbconnection.php'; // adjust path if needed

$db = new DBConnection();
$conn = $db->getConnection();

// Simple admin check (customize as per your auth system)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Fetch products with category names, only active & not deleted
$sql = "SELECT p.id, p.name as product_name, p.description, p.price, p.status, 
        c.name as category_name
        FROM product_list p
        JOIN category_list c ON p.category_id = c.id
        WHERE p.delete_flag = 0 AND c.delete_flag = 0 AND c.status = 1
        ORDER BY p.name ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Menu Management - BrewEase Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-indigo-300 to-fuchsia-400">
<?php include 'navbar.php'; ?>

    <main class="pt-5">
        <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-5">
        <h1 class="text-3xl font-bold text-white">Menu Management</h1>
        <a href="add_product.php" class="bg-white text-indigo-700 hover:bg-[#AD85FD] hover:text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300">
        + Add New Product</a>
        </div>


        <div class="overflow-x-auto bg-white rounded-2xl shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price (RM)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo number_format($row['price'], 2); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if ($row['status'] == 1): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Available
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Not Available
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" 
                                   class="text-indigo-600 hover:text-[#AD85FD]">Edit</a>

                                <a href="delete_product.php?id=<?php echo $row['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this product?');"
                                   class="text-red-600 hover:text-red-900">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No products found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
