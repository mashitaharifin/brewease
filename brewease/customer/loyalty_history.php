<?php
session_start();
require_once '../config.php';
include 'header.php';

$customer_id = $_SESSION['customer_id'] ?? null;
if (!$customer_id) {
    header("Location: login.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM loyalty_history WHERE customer_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$history = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loyalty Point History - BrewEase</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans pt-20 px-4">

    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow p-8">
        <h1 class="text-3xl font-bold text-[#86CFED] mb-6 text-center">Loyalty Point History</h1>

        <?php if (empty($history)): ?>
            <p class="text-gray-500 text-center">You have no loyalty activity yet.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border">
                    <thead>
                        <tr class="border-b bg-gray-100">
                            <th class="py-2 px-4">Date</th>
                            <th class="py-2 px-4">Order ID</th>
                            <th class="py-2 px-4 text-green-600">Points Earned</th>
                            <th class="py-2 px-4 text-red-600">Points Redeemed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $entry): ?>
                            <tr class="border-b">
                                <td class="py-2 px-4"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($entry['created_at']))) ?></td>
                                <td class="py-2 px-4"><?= $entry['order_id'] ?? '-' ?></td>
                                <td class="py-2 px-4 text-green-700 font-semibold"><?= $entry['points_earned'] ?></td>
                                <td class="py-2 px-4 text-red-500 font-semibold"><?= $entry['points_redeemed'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
</body>
</html>
