<?php
session_start();
require_once '../classes/dbconnection.php';

$db = new DBConnection();
$conn = $db->getConnection();

// Fetch products
$productStmt = $conn->prepare("SELECT id, name, price FROM product_list WHERE delete_flag = 0 AND status = 1 ORDER BY name");
$productStmt->execute();
$products = $productStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$cashierName = $_SESSION['user']['firstname'] ?? 'Cashier';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>New Order - BrewEase Cashier</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    let orderItems = [];
    let editingIndex = null;

    function addToCart(productId) {
        const name = document.getElementById(`name-${productId}`).value;
        const price = parseFloat(document.getElementById(`price-${productId}`).value);
        const quantity = parseInt(document.getElementById(`qty-${productId}`).value);
        const type = document.getElementById(`type-${productId}`).value;
        const sugar = document.getElementById(`sugar-${productId}`).value;
        const addonCheckboxes = document.querySelectorAll(`.addon-checkbox[data-id='${productId}']:checked`);
        const addon = Array.from(addonCheckboxes).map(cb => cb.value);


        if (quantity < 1) return alert("Please enter a valid quantity.");

        orderItems.push({ productId, name, price, quantity, type, sugar, addon });
        renderOrderSummary();
    }

    function renderOrderSummary() {
        const tableBody = document.getElementById('cart-body');
        tableBody.innerHTML = '';

        let total = 0;

        orderItems.forEach((item, index) => {
            const addonTotal = item.addon.length * 1.00; // RM1 per add-on
            const subtotal = (item.price + addonTotal) * item.quantity;

            total += subtotal;

            const row = document.createElement('tr');
            row.classList.add('border-b');

            if (editingIndex === index) {
                row.innerHTML = `
                    <td class="p-2">${item.name}</td>
                    <td class="p-2 text-center"><input type="number" id="edit-qty" value="${item.quantity}" class="w-16 p-1 border rounded text-sm"></td>
                    <td class="p-2 text-center">${item.type}</td>
                    <td class="p-2 text-center"><select id="edit-sugar" class="p-1 border rounded text-sm">
                        <option value="0%" ${item.sugar === '0%' ? 'selected' : ''}>0%</option>
                        <option value="25%" ${item.sugar === '25%' ? 'selected' : ''}>25%</option>
                        <option value="50%" ${item.sugar === '50%' ? 'selected' : ''}>50%</option>
                        <option value="75%" ${item.sugar === '75%' ? 'selected' : ''}>75%</option>
                        <option value="100%" ${item.sugar === '100%' ? 'selected' : ''}>100%</option>
                    </select></td>
                    <td class="p-2 text-center">
                        <label><input type="checkbox" value="Extra Shot" class="edit-addon" ${item.addon.includes("Vanilla Syrup") ? 'checked' : ''}> Vanilla Syrup</label><br>
                        <label><input type="checkbox" value="Oat Milk" class="edit-addon" ${item.addon.includes("Caramel Drizzle") ? 'checked' : ''}> Caramel Drizzle</label><br>
                        <label><input type="checkbox" value="Whipped Cream" class="edit-addon" ${item.addon.includes("Extra Shot") ? 'checked' : ''}> Extra Shot</label>
                    </td>

                    <td class="p-2 text-right">RM ${subtotal.toFixed(2)}</td>
                    <td class="p-2 text-center space-x-1">
                        <button onclick="saveEdit(${index})" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">Save</button>
                        <button onclick="cancelEdit()" class="bg-gray-400 hover:bg-gray-500 text-white px-2 py-1 rounded text-xs">Cancel</button>
                    </td>
                `;
            } else {
                row.innerHTML = `
                    <td class="p-2">${item.name}</td>
                    <td class="p-2 text-center">${item.quantity}</td>
                    <td class="p-2 text-center">${item.type}</td>
                    <td class="p-2 text-center">${item.sugar}</td>
                    <td class="p-2 text-center">${item.addon.length ? item.addon.join(', ') : '-'}</td>
                    <td class="p-2 text-right">RM ${subtotal.toFixed(2)}</td>
                    <td class="p-2 text-center space-x-1">
                        <button onclick="editItem(${index})" class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs">Edit</button>
                        <button onclick="removeItem(${index})" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">Remove</button>
                    </td>
                `;
            }

            tableBody.appendChild(row);
        });

        document.getElementById('total').textContent = `RM ${total.toFixed(2)}`;
    }

    function editItem(index) {
        editingIndex = index;
        renderOrderSummary();
    }

    function removeItem(index) {
        if (confirm("Remove this item from the order?")) {
            orderItems.splice(index, 1);
            renderOrderSummary();
        }
    }

    function cancelEdit() {
        editingIndex = null;
        renderOrderSummary();
    }

    function saveEdit(index) {
        const newQty = parseInt(document.getElementById('edit-qty').value);
        const newSugar = document.getElementById('edit-sugar').value;
        const newAddonCheckboxes = document.querySelectorAll('.edit-addon:checked');
        const newAddon = Array.from(newAddonCheckboxes).map(cb => cb.value);
        orderItems[index].addon = newAddon;


        if (isNaN(newQty) || newQty < 1) return alert("Invalid quantity.");

        orderItems[index].quantity = newQty;
        orderItems[index].sugar = newSugar;
        orderItems[index].addon = newAddon;

        editingIndex = null;
        renderOrderSummary();
    }

    function prepareOrderSubmission() {
        console.log("Order items:", orderItems);
        document.getElementById("order-data").value = JSON.stringify(orderItems);
    }
  </script>
</head>
<body class="bg-gradient-to-tr from-lime-200 to-cyan-300 min-h-screen">
<?php include 'navbar.php'; ?>

<div class="max-w-6xl mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold text-white mb-6">Add New Order (Walk-In)</h1>

    <!-- Product List -->
    <div class="bg-white p-4 rounded-2xl shadow mb-6">
        <h2 class="text-2xl font-semibold text-center text-[#207B52] mb-4">Menu List</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?php foreach ($products as $product): ?>
            <div class="border p-4 rounded shadow-sm bg-gray-50">
                <input type="hidden" id="name-<?= $product['id'] ?>" value="<?= htmlspecialchars($product['name']) ?>">
                <input type="hidden" id="price-<?= $product['id'] ?>" value="<?= $product['price'] ?>">
                <h3 class="text-lg font-semibold"><?= htmlspecialchars($product['name']) ?> - RM <?= number_format($product['price'], 2) ?></h3>
                <div class="grid grid-cols-2 gap-2 mt-2">
                    <input type="number" id="qty-<?= $product['id'] ?>" class="p-1 border rounded" placeholder="Qty" min="1" value="1">

                    <?php
                    $type = stripos($product['name'], 'iced') !== false ? 'Iced' : 'Hot';
                    ?>
                    <input type="hidden" id="type-<?= $product['id'] ?>" value="<?= $type ?>">
                    <div class="p-1 border rounded bg-gray-100 text-sm text-center"><?= $type ?></div>


                    <select id="sugar-<?= $product['id'] ?>" class="p-1 border rounded">
                        <option value="0%">0%</option>
                        <option value="25%">25%</option>
                        <option value="50%">50%</option>
                        <option value="75%">75%</option>
                        <option value="100%">100%</option>
                    </select>
                    <div class="col-span-2 space-y-1 text-sm text-gray-700">
                        <label class="block font-medium">Add-ons (RM1 each):</label>
                        <label><input type="checkbox" value="Vanilla Syrup" class="addon-checkbox" data-id="<?= $product['id'] ?>"> Vanilla Syrup</label><br>
                        <label><input type="checkbox" value="Caramel Drizzle" class="addon-checkbox" data-id="<?= $product['id'] ?>"> Caramel Drizzle</label><br>
                        <label><input type="checkbox" value="Extra Shot" class="addon-checkbox" data-id="<?= $product['id'] ?>"> Extra Shot</label>
                    </div>

                </div>
                <button onclick="addToCart(<?= $product['id'] ?>)" class="bg-white text-[#438E74] hover:bg-[#8FDEC2] hover:text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300">+ Add Item</button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Order Summary -->
    <form action="submitorder.php" method="POST" onsubmit="prepareOrderSubmission(); return true;">
        <input type="hidden" name="customer_id" value="0">
        <input type="hidden" name="customer_name" value="Offline Customer">
        <input type="hidden" id="order-data" name="order_data">

        <div class="bg-white p-4 rounded-2xl shadow mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Order Summary</h2>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b font-semibold text-gray-600">
                        <th class="p-2">Product</th>
                        <th class="p-2 text-center">Qty</th>
                        <th class="p-2 text-center">Type</th>
                        <th class="p-2 text-center">Sugar</th>
                        <th class="p-2 text-center">Addon</th>
                        <th class="p-2 text-center">Subtotal</th>
                        <th class="p-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="cart-body"></tbody>
            </table>
            <div class="text-right text-lg mt-4 font-semibold text-gray-800">Total: <span id="total">RM 0.00</span></div>
        </div>

        <!-- Payment -->
        <div class="bg-white p-4 rounded-2xl shadow">
            <label class="block mb-2 text-sm font-medium text-gray-700">Payment Method</label>
            <select name="payment_method" class="w-full p-2 border rounded mb-4" required>
                <option value="Cash">Cash</option>
                <option value="Credit card">Credit Card</option>
                <option value="Online banking">Online Banking</option>
            </select>
            <button type="submit" class="bg-[#8FDEC2] text-white hover:bg-white hover:text-[#4CAF50] px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300">Submit Order</button>
        </div>
    </form>
</div>
</body>
</html>
