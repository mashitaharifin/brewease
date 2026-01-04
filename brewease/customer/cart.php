<?php
session_start();
include 'header.php';

if (!isset($_SESSION["customer_id"])) {
    header("Location: login.php");
    exit;
}

require_once '../config.php';

$customer_id = $_SESSION["customer_id"];
$cart_items = [];
$total = 0;

// Handle quantity and sugar update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['update_item'])) {
        $cart_id = intval($_POST['cart_id']); // ✅ Moved here
        $new_qty = max(1, intval($_POST['quantity']));
        $new_sugar = $_POST['sugar'];

        $stmt = $conn->prepare("UPDATE cart SET quantity = ?, sugar = ? WHERE id = ? AND customer_id = ?");
        $stmt->bind_param("isii", $new_qty, $new_sugar, $cart_id, $customer_id);
        $stmt->execute();
        $stmt->close();
    }

    // Handle item delete
    if (isset($_POST['delete_item'])) {
        $cart_id = intval($_POST['cart_id']);
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND customer_id = ?");
        $stmt->bind_param("ii", $cart_id, $customer_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: cart.php");
    exit;
}

// Fetch cart items
$stmt = $conn->prepare("SELECT * FROM cart WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-md p-6">
    <h1 class="text-3xl font-bold text-[#86CFED] mb-6">🛒 Your Cart</h1>

    <?php if (empty($cart_items)): ?>
      <p class="text-gray-600">Your cart is empty.</p>
    <?php else: ?>
      <table class="w-full table-auto mb-6">
        <thead style="background-color: #86CFED" class="text-white">
          <tr>
            <th class="px-4 py-2 text-left">Drink</th>
            <th class="px-4 py-2">Sugar</th>
            <th class="px-4 py-2">Quantity</th>
            <th class="px-4 py-2">Add-on(s)</th>
            <th class="px-4 py-2">Price</th>
            <th class="px-4 py-2">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cart_items as $item): ?>
            <tr class="border-t">
              <form method="POST">
                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">

                <!-- Product Name -->
                <td class="px-4 py-2"><?= htmlspecialchars($item['product_name']) ?></td>

                <!-- Sugar -->
                <td class="px-4 py-2 text-center">
                    <select name="sugar" class="border rounded px-2 py-1">
                        <option value="0%" <?= $item['sugar'] == '0%' ? 'selected' : '' ?>>0%</option>
                        <option value="25%" <?= $item['sugar'] == '25%' ? 'selected' : '' ?>>25%</option>
                        <option value="50%" <?= $item['sugar'] == '50%' ? 'selected' : '' ?>>50%</option>
                        <option value="75%" <?= $item['sugar'] == '75%' ? 'selected' : '' ?>>75%</option>
                        <option value="100%" <?= $item['sugar'] == '100%' ? 'selected' : '' ?>>100%</option>
                    </select>
                </td>

                <!-- Quantity -->
                <td class="px-4 py-2 text-center">
                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="w-16 text-center border rounded px-2 py-1">
                </td>

                <!-- Addon -->
                <td class="px-4 py-2"><?= htmlspecialchars($item['addon']) ?: '-' ?></td>

                <!-- Price -->
                <td class="px-4 py-2 text-right">RM <?= number_format($item['price'] * $item['quantity'], 2) ?></td>

                <!-- Actions -->
                <td class="px-4 py-2 text-center flex flex-col items-center gap-2">
                  <button type="submit" name="update_item" class="text-indigo-600 font-bold hover:text-[#AD85FD] px-2 py-1">
                    Update
                  </button>
              </form>

              <!-- Delete Form -->
              <form method="POST">
                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                <button type="submit" name="delete_item" class="text-red-500 font-bold hover:text-red-400 px-2 py-1">
                  Delete
                </button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>

          <!-- Total -->
          <tr class="font-bold bg-gray-100">
            <td colspan="4" class="px-4 py-2 text-right">Grand Total:</td>
            <td class="px-4 py-2 text-right">RM <?= number_format($total, 2) ?></td>
            <td></td>
          </tr>
        </tbody>
      </table>

      <div class="flex justify-between items-center">
        <a href="menu.php" class="inline-block text-white px-4 py-2 rounded-2xl" style="background-color: #86CFED;" onmouseover="this.style.backgroundColor='#2FA5D4'" onmouseout="this.style.backgroundColor='#86CFED'">
          ← Continue Browsing Menu
        </a>
        <a href="checkout.php" class="inline-block text-white px-4 py-2 rounded-2xl" style="background-color: #86CFED;" onmouseover="this.style.backgroundColor='#2FA5D4'" onmouseout="this.style.backgroundColor='#86CFED'">
          Proceed to Checkout →
        </a>
      </div>

    <?php endif; ?>
  </div>
</body>
</html>