<?php

session_start(); // Start the session to manage user login sessions

require 'config.php'; // Include database configuration

$error = null; // Initialize the error variable to store potential error messages

try {

    if ($_SERVER['REQUEST_METHOD'] == 'POST') 
    {
	// Retrieve username and password entered by the user
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare and execute the query to check user credentials
        $query = $pdo->prepare('SELECT * FROM users WHERE username = ? AND password = ?');
	
	// Execute the query with the provided username and password as parameters
        $query->execute([$username, $password]);
        $user = $query->fetch();

        if ($user) 
	{
            // User authenticated successfully
            $_SESSION['user_id'] = $user['id'];
	    
	    // Redirect the authenticated user to the dashboard page
            header('Location: dashboard.php');
            exit(); // Always exit after header redirection
        } 
	else {
            // Authentication failed
            $error = "Invalid username or password.";
        }
    }
} catch (PDOException $e) 
{
    // Handle database connection error
    $error = "Service error: Unable to connect to the authentication service. Please try again later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - WaterAmerica</title>
    <style>
        
        *{
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

        .login-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            text-align: left;
        }

        .login-container label {
            font-weight: bold;
            color: #333;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
        }

        .login-container button {
            padding: 10px;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        .login-container p {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
        }

        .reset-link a {
            color: #007bff;
            text-decoration: none;
        }

        .reset-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">

    <h2>Login to WaterAmerica</h2>

    <!-- Login form, POST method to send data securely -->
    <form action="login.php" method="POST">

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>

        <button type="submit">Login</button>  <!-- Submit button to log in -->
    </form>
     
    <!-- Display error message if login fails -->
    <?php if ($error): ?>
        <p class="error-message"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Link to reset password if user forgot it -->
    <p class="reset-link"><a href="reset_password.php">Forgot Password?</a></p>
</div>

</body>
</html>
