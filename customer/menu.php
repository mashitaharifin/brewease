<?php 
include 'header.php';
require_once '../config.php';

if (!isset($_SESSION["customer_id"])) {
    header("Location: login.php");
    exit;
}

// Fetch menu items
$products = [];
$res = $conn->query("
    SELECT p.*, c.name AS category_name
    FROM product_list p
    INNER JOIN category_list c ON p.category_id = c.id
    WHERE p.delete_flag = 0 AND p.status = 1
    ORDER BY p.name ASC
");

while ($row = $res->fetch_assoc()) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
<div class="max-w-5xl mx-auto">
    <h1 class="text-3xl font-bold text-white mb-6">Choose Your Drinks ☕️</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($products as $product): ?>
            <div class="bg-white p-4 w-93 rounded-xl shadow space-y-3 product-card" data-id="<?= $product["id"] ?>">
                <?php if (!empty($product['photo'])): ?>
                <img src="../admin/uploads/products/<?= htmlspecialchars($product['photo']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-40 object-cover rounded-xl transition-transform duration-300 hover:scale-110">
                <?php else: ?>
                <div class="w-full h-40 bg-gray-100 rounded flex items-center justify-center text-gray-400">No Image</div>
                <?php endif; ?>


                <h2 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($product["name"]) ?></h2>
                <?php if (!empty($product["description"])): ?>
                    <p class="text-sm text-gray-600"><?= htmlspecialchars($product["description"]) ?></p>
                <?php endif; ?>
                <p class="text-gray-500 font-medium">RM <?= number_format($product["price"], 2) ?></p>

                <input type="hidden" class="product-name" value="<?= htmlspecialchars($product["name"]) ?>">
                <input type="hidden" class="product-price" value="<?= $product["price"] ?>">
                <input type="hidden" class="product-id" value="<?= $product["id"] ?>">

                <label class="block">
                    <span class="text-sm text-gray-700">Category</span>
                    <input 
                        type="text" 
                        class="type w-full mt-1 border rounded p-2 bg-gray-100 cursor-not-allowed" 
                        value="<?= htmlspecialchars($product['category_name']) ?>" 
                        readonly
                        >
                </label>

                <label class="block">
                    <span class="text-sm text-gray-700">Sugar Level</span>
                    <select class="sugar w-full mt-1 border rounded p-2">
                        <option value="0%">0%</option>
                        <option value="25%">25%</option>
                        <option value="50%">50%</option>
                        <option value="75%">75%</option>
                        <option value="100%" selected>100%</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm text-gray-700">Add-on (+RM1 each)</span>
                    <div class="space-y-1">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" class="addon" value="Vanilla Syrup">
                            <span>Vanilla Syrup</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" class="addon" value="Caramel Drizzle">
                            <span>Caramel Drizzle</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" class="addon" value="Extra Shot">
                            <span>Extra Shot</span>
                        </label>
                    </div>
                </label>

                <label class="block">
                    <span class="text-sm text-gray-700">Quantity</span>
                    <input type="number" class="quantity w-full mt-1 border rounded p-2" value="1" min="1">
                </label>

                <button type="button" class="add-to-cart w-full bg-[#86CFED] text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300 hover:bg-white hover:text-[#2FA5D4]">
                    Add to Cart
                </button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
$('.add-to-cart').click(function () {
    const card = $(this).closest('.product-card');
    const product_id = card.find('.product-id').val();
    const product_name = card.find('.product-name').val();
    const base_price = parseFloat(card.find('.product-price').val());
    const type = card.find('.type').val();
    const sugar = card.find('.sugar').val();
    const quantity = parseInt(card.find('.quantity').val());

    // Collect all checked add-ons
    const addons = [];
    card.find('input.addon:checked').each(function () {
        addons.push($(this).val());
    });

    const addonCharge = addons.length * 1.00;
    const totalPrice = (base_price + addonCharge) * quantity;
    const addonList = addons.join(', ');

    // Prepare payload
    const payload = {
        action: 'add',
        product_id: product_id,
        product_name: product_name,
        price: base_price,
        type: type,
        sugar: sugar,
        addon: addons,
        quantity: quantity
    };

    // Send to cart via AJAX
    $.ajax({
        url: 'cart_api.php',
        type: 'POST',
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify(payload),
        success: function (response) {
            if (response.status === 'success') {
                alert(`✔ ${product_name} added to cart\n➕ Add-ons: ${addonList || 'None'}\n💲 Total: RM ${totalPrice.toFixed(2)}`);
                updateCartCount(); // ✅ Update live cart count
            } else {
                alert('❌ Error: ' + response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error('Error:', error);
            alert('❌ Failed to add item to cart. Please try again.');
        }
    });

    // Function to update cart count
    function updateCartCount() {
        fetch('cart_count_api.php')
        .then(res => res.json())
        .then(data => {
            const countElement = document.getElementById('cart-count');
            if (countElement) {
                countElement.textContent = data.count;
            }
        })
        .catch(error => {
            console.error('Failed to fetch cart count:', error);
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    fetch('cart_count_api.php')
    .then(res => res.json())
    .then(data => {
        const countElement = document.getElementById('cart-count');
        if (countElement) {
            countElement.textContent = data.count;
        }
    })
    .catch(error => {
        console.error('Failed to fetch cart count:', error);
    });
});
</script>
</body>
</html>
