<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - WaterAmerica</title>

    <!-- Inline CSS for styling the dashboard page -->
    <style>
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f0f4f8;
        }

        .dashboard-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 30px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .dashboard-container h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .dashboard-container p {
            color: #666;
            margin-bottom: 30px;
        }

        .dashboard-container .button-container {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .dashboard-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .dashboard-button:hover {
            background-color: #0056b3;
        }

        .logout-button {
            background-color: #dc3545;
        }

        .logout-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<div class="dashboard-container">

    <!-- Main welcome heading for the dashboard -->
    <h1>Welcome to Your Dashboard</h1>
     
    <!-- Description for the user -->
    <p>Manage your account and move requests below.</p>
    <div class="button-container">

	<!-- Link button to create a new move request -->
        <a href="move_request.php" class="dashboard-button">Create New Move Request</a>

	<!-- Link button to log out of the dashboard -->
        <a href="logout.php" class="dashboard-button logout-button">Logout</a>
    </div>

</div>

</body>
</html>
