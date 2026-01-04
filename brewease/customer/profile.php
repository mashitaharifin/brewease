<?php
session_start();
require_once '../config.php';
include 'header.php';

$customer_id = $_SESSION['customer_id'] ?? null;

if (!$customer_id) {
    header("Location: login.php");
    exit;
}

$stmt = $conn->prepare("SELECT fullname, email, points FROM customers WHERE id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$stmt->bind_result($name, $email, $points);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - BrewEase</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10 px-4">
    <div class="max-w-5xl mx-auto bg-white p-8 rounded-xl shadow">
        <h1 class="text-3xl font-bold text-[#86CFED] mb-6 text-center">My Profile</h1>

        <div class="space-y-4 text-gray-700">
            <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
            <p>
                <span class="items-center px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-full">
                    ⭐ Loyalty Points: <strong class="ml-1"><?php echo $points; ?></strong>
                </span>
            </p>

            <?php if ($points >= 1000): ?>
                <div class="mt-4 p-4 bg-yellow-100 border border-yellow-400 rounded-lg text-yellow-800 font-semibold">
                    🎉 You have a free coffee ready to redeem at checkout!
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-6 text-center space-x-4">
            <a href="menu.php" class="inline-block bg-[#86CFED] text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300 hover:bg-white hover:text-[#2FA5D4]">Back to Menu</a>
            <a href="loyalty_history.php" class="inline-block bg-[#86CFED] text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300 hover:bg-white hover:text-[#2FA5D4]">View Loyalty Point History</a>
        </div>
    </div>
</body>
</html>
