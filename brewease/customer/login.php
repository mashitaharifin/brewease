<?php 
session_start();
require_once '../config.php'; // Adjust if needed

$email = $password = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Validate input
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (empty($password)) $errors[] = "Password is required.";

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, fullname, password FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $fullname, $hashed);
            $stmt->fetch();
            if (password_verify($password, $hashed)) {
                $_SESSION["customer_id"] = $id;
                $_SESSION["customer_name"] = $fullname;
                header("Location: index.php");
                exit;
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No account found with that email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-purple-300 via-cyan-200 to-teal-100 font-sans h-screen">

    <!-- Header section -->
    <header class="text-center pt-12 pb-16 px-4">
        <div class="flex justify-center items-center gap-6 mb-6">
            <img src="../assets/icons/brewease.png" alt="BrewEase Logo" class="w-24 rounded-full shadow-lg">
            <img src="../assets/icons/perfectplace.png" alt="The Perfect Place Logo" class="w-24 rounded-full shadow-lg">
        </div>
        <h1 class="text-4xl font-bold text-white">Welcome to BrewEase</h1>
        <p class="text-white mt-2">Your all-in-one coffee shop order management system</p>
    </header>

    <!-- Login form -->
    <div class="flex justify-center">
        <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center text-[#86CFED]">Customer Login</h2>

            <?php if (!empty($_SESSION["success"])): ?>
                <div class="mb-4 text-green-600">
                    <?= htmlspecialchars($_SESSION["success"]); unset($_SESSION["success"]); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="mb-4 text-red-600">
                    <ul class="list-disc list-inside">
                        <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="space-y-4">
                <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>" class="w-full p-2 border border-gray-300 rounded">
                <input type="password" name="password" placeholder="Password" class="w-full p-2 border border-gray-300 rounded">
                <button type="submit" class="w-full bg-[#86CFED] text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300 hover:bg-white hover:text-[#2FA5D4]">Log In</button>
            </form>

            <p class="mt-4 text-center text-gray-600">
                Don't have an account?
                <a href="register.php" class="text-[#86CFED] hover:underline hover:font-bold">Register here</a>
            </p>
        </div>
    </div>

</body>
</html>
