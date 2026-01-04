<?php
session_start();
header('Content-Type: application/json');
require_once '../config.php';

if (!isset($_SESSION["customer_id"])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

$customer_id = $_SESSION["customer_id"];
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['action']) || $input['action'] !== 'add') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

$required = ['product_id', 'product_name', 'price', 'type', 'sugar', 'addon', 'quantity'];
foreach ($required as $field) {
    if (!isset($input[$field])) {
        echo json_encode(['status' => 'error', 'message' => "Missing field: $field"]);
        exit;
    }
}

$product_id = intval($input['product_id']);
$product_name = $input['product_name'];
$base_price = floatval($input['price']);
$type = $input['type'];
$sugar = $input['sugar'];
$quantity = intval($input['quantity']);

// Convert addon to array if it's not already (in case frontend sends a string)
$addons = is_array($input['addon']) ? $input['addon'] : array_filter(array_map('trim', explode(',', $input['addon'])));
$addon_list = implode(', ', $addons);
$addon_charge = count($addons) * 1.00;
$final_price = $base_price + $addon_charge;

require_once '../classes/DBConnection.php';
$db = new DBConnection();
$conn = $db->getConnection();

// Check if item already in cart
$stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE customer_id = ? AND product_id = ? AND type = ? AND sugar = ? AND addon = ?");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'Prepare SELECT failed: ' . $conn->error]);
    exit;
}
$stmt->bind_param("iisss", $customer_id, $product_id, $type, $sugar, $addon_list);
if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Execute SELECT failed: ' . $stmt->error]);
    exit;
}
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;

    $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    if (!$update_stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare UPDATE failed: ' . $conn->error]);
        exit;
    }
    $update_stmt->bind_param("ii", $new_quantity, $row['id']);
    if (!$update_stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Execute UPDATE failed: ' . $update_stmt->error]);
        exit;
    }
    $update_stmt->close();
} else {
    $insert_stmt = $conn->prepare("INSERT INTO cart (customer_id, product_id, product_name, price, type, sugar, addon, quantity) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$insert_stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare INSERT failed: ' . $conn->error]);
        exit;
    }
    $insert_stmt->bind_param("iisdsssi", $customer_id, $product_id, $product_name, $final_price, $type, $sugar, $addon_list, $quantity);
    if (!$insert_stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Execute INSERT failed: ' . $insert_stmt->error]);
        exit;
    }
    $insert_stmt->close();
}

$stmt->close();
$conn->close();

echo json_encode(['status' => 'success', 'message' => "$product_name added to cart."]);