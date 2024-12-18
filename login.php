<?php

session_start(); // Start the session to manage user login sessions

require 'config.php'; // Include database configuration

// Initialize error variable
$error = null;

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Retrieve username and password entered by the user
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare and execute the query to check user credentials
        $query = $pdo->prepare('SELECT * FROM users WHERE username = ? AND password = ?');

        // Execute the query with the provided username and password as parameters
        $query->execute([$username, $password]);
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // User authenticated successfully
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role']; // Store the user's role in the session

            // Redirect based on the user's role
            if ($user['role'] === 'admin') {
                header('Location: admin_dashboard.php'); // Redirect admin to the admin dashboard
            } else {
                header('Location: dashboard.php'); // Redirect regular users to the user dashboard
            }
            exit(); // Always exit after header redirection
        } else {
            // Authentication failed, set error message in session
            $_SESSION['error'] = "Invalid username or password.";
            header('Location: login.php'); // Redirect back to login page
            exit();
        }
    }
} catch (PDOException $e) {
    // Handle database connection error
    $_SESSION['error'] = "Service error: Unable to connect to the authentication service. Please try again later.";
    header('Location: login.php'); // Redirect back to login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Water America Move Service - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-cover bg-center bg-fixed" style="background-image: url('https://images.unsplash.com/photo-1535868268694-c4fec652390e?fm=jpg&q=60&w=3000&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8N3x8d2F0ZXIlMjBkcm9wbGV0fGVufDB8fDB8fHww'); min-height: 100vh;">

<!-- Navbar -->
<nav class="bg-blue-600 bg-opacity-75 p-4 fixed top-0 left-0 w-full flex justify-between items-center z-50">
    <div class="text-white text-2xl font-bold">Water America Move Service</div>
</nav>

<!-- Stacked Welcome Message -->
<div class="absolute top-28 left-8 text-blue-200 text-5xl font-roboto font-bold italic z-40">
    <div>Login with</div>
    <div>your Username</div>
    <div>and Password</div>
</div>

<!-- Login Box (Centered) -->
<div class="flex justify-center items-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96 z-50">
        <h2 class="text-2xl font-semibold text-center mb-6">Login</h2>

        <!-- Display error message if exists -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-500 text-white text-center p-2 mb-4 rounded-md">
                <?php echo $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <!-- Username Field -->
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username</label>
                <input type="text" id="username" name="username" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <!-- Password Field -->
            <div class="mb-6">
                <label for="password" class="block text-gray-700">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <!-- Login Button -->
            <div class="flex justify-center">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Login
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
