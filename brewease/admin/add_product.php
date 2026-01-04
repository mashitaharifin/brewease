<?php
session_start();
require_once '../classes/dbconnection.php';

$db = new DBConnection();
$conn = $db->getConnection();

$error = '';
$success = '';

// Fetch active categories for dropdown
$categories = [];
$catSql = "SELECT id, name FROM category_list WHERE status = 1 AND delete_flag = 0 ORDER BY name ASC";
$catResult = $conn->query($catSql);
if ($catResult && $catResult->num_rows > 0) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = intval($_POST['category_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $status = isset($_POST['status']) && $_POST['status'] == '1' ? 1 : 0;
    $photoName = '';

    // Handle image upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $uploadDir = __DIR__ . '/uploads/products/';
        $relativePath = 'uploads/products/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $originalName = basename($_FILES['photo']['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $sanitizedBaseName = preg_replace("/[^A-Za-z0-9_\-]/", '_', pathinfo($originalName, PATHINFO_FILENAME));
        $photoName = time() . '_' . $sanitizedBaseName . '.' . $extension;

        $targetPath = $uploadDir . $photoName;
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
            $error = 'Failed to upload image.';
        }
    }

    if ($category_id <= 0 || empty($name) || $price <= 0) {
        $error = 'Please fill in all required fields with valid data.';
    } elseif (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO product_list (category_id, name, description, price, photo, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdsi", $category_id, $name, $description, $price, $photoName, $status);

        if ($stmt->execute()) {
            $success = 'Product added successfully.';
            $_POST = []; // clear form on success
        } else {
            $error = 'Database error: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add Product - BrewEase Manager</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-indigo-300 to-fuchsia-400">
<?php include 'navbar.php'; ?>

<div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl shadow">
    <h1 class="text-2xl font-bold text-[#AD85FD] mb-6">Add New Product</h1>

    <?php if ($error): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label for="category_id" class="block font-medium mb-1">Category <span class="text-red-600">*</span></label>
            <select id="category_id" name="category_id" required class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="name" class="block font-medium mb-1">Product Name <span class="text-red-600">*</span></label>
            <input type="text" id="name" name="name" required
                value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                class="w-full border border-gray-300 rounded px-3 py-2" />
        </div>

        <div>
            <label for="description" class="block font-medium mb-1">Description</label>
            <textarea id="description" name="description" rows="4"
                class="w-full border border-gray-300 rounded px-3 py-2"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>

        <div>
            <label for="price" class="block font-medium mb-1">Price (RM) <span class="text-red-600">*</span></label>
            <input type="number" step="0.01" min="0" id="price" name="price" required
                value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>"
                class="w-full border border-gray-300 rounded px-3 py-2" />
        </div>

        <div>
            <label class="block font-medium mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="1" <?php echo (isset($_POST['status']) && $_POST['status'] == '1') ? 'selected' : ''; ?>>Available</option>
                <option value="0" <?php echo (isset($_POST['status']) && $_POST['status'] == '0') ? 'selected' : ''; ?>>Not Available</option>
            </select>
        </div>

        <label class="block mt-4">Product Image:</label>
        <input type="file" name="photo" accept="image/*" class="border p-2 rounded w-full">

        <div class="flex space-x-4">
            <button type="submit" class="hover:bg-white hover:text-indigo-700 bg-[#AD85FD] text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300">
                Add Product
            </button>
            <a href="menu.php" class="inline-block bg-white text-indigo-700 hover:bg-[#AD85FD] hover:text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
