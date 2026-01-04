<?php
session_start();
require_once '../classes/dbconnection.php';

$db = new DBConnection();
$conn = $db->getConnection();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

$product_id = intval($_GET['id'] ?? 0);
if ($product_id <= 0) {
    header('Location: menu.php');
    exit;
}

$stmt = $conn->prepare("UPDATE product_list SET delete_flag = 1, date_updated = NOW() WHERE id = ? AND delete_flag = 0");
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    $stmt->close();
    header('Location: menu.php?msg=deleted');
    exit;
} else {
    $stmt->close();
    header('Location: menu.php?error=delete_failed');
    exit;
}
?>
