<?php
session_start();
include 'header.php';
$free_coffee_used = $_SESSION['free_coffee_used'] ?? false;
$points_earned = $_SESSION['points_earned'] ?? 0;

// Clear session vars after displaying
unset($_SESSION['free_coffee_used'], $_SESSION['points_earned']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - BrewEase</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Additional centering for extra assurance */
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body class="bg-green-50 min-h-screen flex flex-col justify-center items-center p-4">
    <div class="bg-white p-10 rounded-xl shadow-lg text-center max-w-lg w-full mx-auto">
        <h1 class="text-3xl font-bold text-blue-700 mb-4">🎉 Thank you for your order!</h1>
        
        <?php if ($free_coffee_used): ?>
            <p class="text-lg text-green-600 font-semibold mb-2">You redeemed 1 free coffee! (RM10 discount applied)</p>
        <?php endif; ?>

        <p class="text-gray-700 text-md mb-6">You earned <strong><?= $points_earned ?></strong> loyalty points from this order.</p>

        <div class="text-center">
            <a href="menu.php" class="bg-[#86CFED] text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300 hover:bg-white hover:text-[#2FA5D4]">Back to Menu</a>
        </div>
    </div>
</body>
</html>
