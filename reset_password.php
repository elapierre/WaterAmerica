<?php
session_start();
require 'config.php'; // Include database configuration

// Check if the request method is POST to handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $username = $_POST['username']; // Retrieve the submitted username
    $new_password = $_POST['new_password']; // Retrieve the new password

    // Check if the username exists in the database
    $query = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $query->execute([$username]);
    $user = $query->fetch();

    if ($user) 
    {
        // Update password
        $update_query = $pdo->prepare('UPDATE users SET password = ? WHERE username = ?');
        $update_query->execute([$new_password, $username]);

        $success_message = "Password has been updated successfully."; // Confirmation message for successful password reset
    } 
    else {
        // Set error message if the username is not found
        $error_message = "Username not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - WaterAmerica</title>

    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background: #f0f4f8;
            font-family: Arial, sans-serif;
        }

        .reset-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .reset-container h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .reset-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            text-align: left;
        }

        .reset-container label {
            font-weight: bold;
            color: #333;
        }

        .reset-container input[type="text"],
        .reset-container input[type="password"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        .reset-container button {
            padding: 10px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
            margin-top: 10px;
        }

        .reset-container button:hover {
            background: #0056b3;
        }

        .back-button {
            background: #6c757d;
        }

        .back-button:hover {
            background: #5a6268;
        }

        .message {
            font-size: 14px;
            margin-top: 10px;
        }

        .message.error {
            color: red;
        }

        .message.success {
            color: green;
        }
    </style>
</head>
<body>

<div class="reset-container">

    <h2>Reset Password</h2>

    <!-- Password reset form -->
    <form action="reset_password.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" required>

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" placeholder="Enter your new password" required>

        <button type="submit">Reset Password</button>
    </form>

    <!-- Display success or error message -->
    <?php if (isset($error_message)): ?>
        <p class="message error"><?php echo $error_message; ?></p>
    <?php elseif (isset($success_message)): ?>
        <p class="message success"><?php echo $success_message; ?></p>
    <?php endif; ?>

    <!-- Link back to the login page -->
    <a href="login.php" class="back-button" style="display: inline-block; padding: 10px; background: #6c757d; color: #fff; border-radius: 5px; text-decoration: none; margin-top: 15px;">Back to Login</a>
</div>

</body>
</html>
