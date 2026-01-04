<?php
session_start();
require_once '../config.php'; // Adjust path based on your structure

$fullname = $email = $password = $confirm_password = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Basic validations
    if (empty($fullname)) $errors[] = "Full name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Email is already registered.";
        } else {
            // Insert new customer
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO customers (fullname, email, password) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $fullname, $email, $hashed);
            if ($insert->execute()) {
                $_SESSION["success"] = "Registration successful! You can now log in.";
                header("Location: login.php");
                exit;
            } else {
                $errors[] = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-purple-300 via-cyan-200 to-teal-100 font-sans flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-[#86CFED]">Create Your Account</h2>

        <?php if (!empty($errors)): ?>
            <div class="mb-4 text-red-600">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="post" class="space-y-4">
            <input type="text" name="fullname" placeholder="Full Name" value="<?= htmlspecialchars($fullname) ?>" class="w-full p-2 border border-gray-300 rounded">
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>" class="w-full p-2 border border-gray-300 rounded">
            <input type="password" name="password" placeholder="Password" class="w-full p-2 border border-gray-300 rounded">
            <input type="password" name="confirm_password" placeholder="Confirm Password" class="w-full p-2 border border-gray-300 rounded">
            <button type="submit" class="w-full bg-[#86CFED] text-white px-4 py-2 rounded-xl font-semibold shadow transition-colors duration-300 hover:bg-white hover:text-[#2FA5D4]">Register</button>
        </form>

        <p class="mt-4 text-center text-gray-600">
            Already have an account?
            <a href="login.php" class="text-[#86CFED] hover:underline hover:font-bold">Log in here</a>
        </p>
    </div>
</body>
</html>
