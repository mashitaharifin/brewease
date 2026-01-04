<?php
require_once '../classes/DBConnection.php';
$db = new DBConnection();
$conn = $db->getConnection();

if (!$conn) {
    die("Database connection failed");
}

$id = $_GET['id'] ?? null;

if ($id) {
    // Use prepared statement for safety
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: users.php");
        exit();
    } else {
        echo "Error deleting user.";
    }
} else {
    echo "Invalid user ID.";
}
?>
