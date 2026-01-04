<?php
session_start();
header('Content-Type: application/json');
require_once '../config.php';

$count = 0;

if (isset($_SESSION['customer_id'])) {
    $customer_id = $_SESSION['customer_id'];
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $stmt->bind_result($total);
    $stmt->fetch();
    $count = $total ?? 0;
    $stmt->close();
}

echo json_encode(['count' => $count]);
