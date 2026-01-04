<?php
session_start();
require_once '../classes/DBConnection.php';

// Initialize database connection
$db = new DBConnection();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $customer_id = 2; // walk-in customer
    $customer_name = 'Offline Customer';
    $phone = 'N/A';
    $payment_method = $_POST['payment_method'] ?? '';
    $order_data_json = $_POST['order_data'] ?? '';

    if (empty($order_data_json) || empty($payment_method)) {
        die('Invalid order data.');
    }

    // Decode order items
    $orderItems = json_decode($order_data_json, true);
    if (!is_array($orderItems) || count($orderItems) === 0) {
        die('Invalid order items.');
    }

    // Calculate total price
    $total_price = 0;
    foreach ($orderItems as $item) {
        $price = floatval($item['price']);
        $quantity = intval($item['quantity']);
        $total_price += $price * $quantity;
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, customer_name, phone, payment_method, total_price, created_at, status) 
                            VALUES (?, ?, ?, ?, ?, NOW(), 'Preparing')");
    if (!$stmt) {
    die("Order insert failed: " . $conn->error);
    }

    $stmt->bind_param("isssd", $customer_id, $customer_name, $phone, $payment_method, $total_price);
    $stmt->execute();

    $order_id = $conn->insert_id; // Get the newly created order ID

    // Prepare to insert order_items
    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, type, sugar, addon) 
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$item_stmt) die("Order item prepare failed: " . $conn->error);

    foreach ($orderItems as $item) {
        $product_id = $item['productId'];
        $product_name = $item['name'];
        $price = $item['price'];
        $quantity = $item['quantity'];
        $type = $item['type'];
        $sugar = $item['sugar'];
        $addon = is_array($item['addon']) ? implode(', ', $item['addon']) : '';

        $item_stmt->bind_param("iisdisss", $order_id, $product_id, $product_name, $price, $quantity, $type, $sugar, $addon);
        $item_stmt->execute();
    }

    $item_stmt->close();

        // Redirect to orders page with success
        header("Location: orders.php?success=1");
        exit();
    } else {
        echo "Invalid request.";
    }
?>
