<?php
session_start();
require 'config.php'; // Include database configuration

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $billing_address = $_POST['billing_address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip_code = $_POST['zip_code'];
    $move_date = $_POST['move_date'];

    // Check if the move date is in the future
    $current_date = date('Y-m-d');
    if ($move_date <= $current_date) {
        $_SESSION['error_message'] = "Please select a future date for the move.";
        header('Location: move_request.php'); // Redirect if date is invalid
        exit();
    }

    try 
    {
        // Update user's billing information in WA-BILL table
        $update_query = $pdo->prepare('UPDATE wa_bill SET billing_address = ?, city = ?, state = ?, zip_code = ? WHERE user_id = ?');
        $update_query->execute([$billing_address, $city, $state, $zip_code, $user_id]);

        // Insert the move request into move_requests table
        $move_query = $pdo->prepare('INSERT INTO move_requests (user_id, billing_address, move_date, status) VALUES (?, ?, ?, "pending")');
        $move_query->execute([$user_id, "$billing_address, $city, $state, $zip_code", $move_date]);

        // Display success message
        echo "
        <html>
        <head>
            <title>Move Request Submitted</title>
            <style>
                body {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    height: 100vh;
                    font-family: Arial, sans-serif;
                    background-color: #f0f4f8;
                    margin: 0;
                }
                .message-container {
                    background-color: #ffffff;
                    border-radius: 10px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                    padding: 30px;
                    max-width: 400px;
                    text-align: center;
                }
                .message-container h2 {
                    color: #333333;
                    margin-bottom: 20px;
                }
                .message-container p {
                    color: #4CAF50;
                    font-size: 16px;
                }
                .success-message {
                    color: #4CAF50;
                    font-size: 18px;
                    font-weight: bold;
                }
                .error-message {
                    color: red;
                    font-size: 16px;
                    font-weight: bold;
                }
                .back-link {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 10px 20px;
                    color: #ffffff;
                    background-color: #007bff;
                    text-decoration: none;
                    border-radius: 5px;
                    transition: background 0.3s ease;
                }
                .back-link:hover {
                    background-color: #0056b3;
                }
            </style>
        </head>
        <body>
            <div class='message-container'>
                <h2>Success</h2>
                <p class='success-message'>Move request successfully submitted!</p>
                <a href='dashboard.php' class='back-link'>Return to Dashboard</a>
            </div>
        </body>
        </html>
        ";
        exit();
    } catch (PDOException $e) 
    {
        // Display error message with styling
        echo "
        <html>
        <head>
            <title>Error</title>
            <style>
                body {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    height: 100vh;
                    font-family: Arial, sans-serif;
                    background-color: #f0f4f8;
                    margin: 0;
                }
                .message-container {
                    background-color: #ffffff;
                    border-radius: 10px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                    padding: 30px;
                    max-width: 400px;
                    text-align: center;
                }
                .message-container h2 {
                    color: #333333;
                    margin-bottom: 20px;
                }
                .error-message {
                    color: red;
                    font-size: 18px;
                    font-weight: bold;
                }
                .back-link {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 10px 20px;
                    color: #ffffff;
                    background-color: #007bff;
                    text-decoration: none;
                    border-radius: 5px;
                    transition: background 0.3s ease;
                }
                .back-link:hover {
                    background-color: #0056b3;
                }
            </style>
        </head>
        <body>
            <div class='message-container'>
                <h2>Error</h2>
                <p class='error-message'>An error occurred: " . htmlspecialchars($e->getMessage()) . "</p>
                <a href='move_request.php' class='back-link'>Go Back</a>
            </div>
        </body>
        </html>
        ";
        exit();
    }
}
?>

