<?php
session_start();
// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username']; // Retrieve the username from the session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Water America Move Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-cover bg-center bg-fixed" style="background-image: url('https://images.pexels.com/photos/5321497/pexels-photo-5321497.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1');">

<!-- Navbar -->
<nav class="bg-blue-600 bg-opacity-75 p-4 fixed top-0 left-0 w-full flex justify-between items-center z-50">
    <div class="text-white text-2xl font-bold">Water America Move Service</div>
    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-md">
        Logout
    </a>
</nav>

<!-- Welcome Message -->
<div class="absolute top-28 left-8 text-blue-600 text-5xl font-roboto font-bold italic z-40">
    <div>Welcome, <?php echo htmlspecialchars($username); ?>!</div>
</div>

<!-- Dashboard Buttons -->
<div class="flex justify-center items-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96 z-50">
        <h2 class="text-2xl font-semibold text-center mb-6">Your Dashboard</h2>

        <!-- Buttons -->
        <div class="flex flex-col space-y-4">
            <a href="move_request.php" class="bg-blue-500 text-white px-6 py-2 rounded-md text-center hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                Request Move
            </a>
            <a href="move_history.php" class="bg-blue-500 text-white px-6 py-2 rounded-md text-center hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                View Move Requests
            </a>
        </div>
    </div>
</div>

</body>
</html>
