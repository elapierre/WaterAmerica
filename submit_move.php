<?php
session_start();
require 'config.php';
require 'vendor/autoload.php'; // Include PHPMailer library
require_once'validate_address.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

// Check if the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;
    $billing_address = $_POST['billing_address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip_code = $_POST['zip_code'];
    $move_date = $_POST['move_date'];

    $address = [
        'line1' => $billing_address,
        'line2' => "$city, $state $zip_code",
        'region_code' => "US",
    ];

    $standardizedAddress = validate_address($address);


    $full_address = $standardizedAddress['address'] . ', ' .
        $standardizedAddress['city'] . ', ' .
        $standardizedAddress['state'] . ' ' .
        $standardizedAddress['zipCode'];

    // Check if the move date is in the future
    $current_date = date('Y-m-d');
    if ($move_date <= $current_date) {
        $_SESSION['error_message'] = "Please select a future date for the move.";
        header('Location: move_request.php');
        exit();
    }

    try {
        // Check if the user already has billing information
        $check_query = $pdo->prepare('SELECT * FROM wa_bill WHERE user_id = ?');
        $check_query->execute([$user_id]);
        $billing_exists = $check_query->fetch(PDO::FETCH_ASSOC);

        if ($billing_exists) {
            // Update billing information if it exists
            $update_query = $pdo->prepare(
                'UPDATE wa_bill SET billing_address = ?, city = ?, state = ?, zip_code = ? WHERE user_id = ?'
            );
            $update_query->execute([$billing_address, $city, $state, $zip_code, $user_id]);
        } else {
            // Insert new billing information if it does not exist
            $insert_query = $pdo->prepare(
                'INSERT INTO wa_bill (user_id, billing_address, city, state, zip_code) VALUES (?, ?, ?, ?, ?)'
            );
            $insert_query->execute([$user_id, $billing_address, $city, $state, $zip_code]);
        }

        echo "<p class='success'>✅ Move request inserted successfully into the database.</p>";

        // Insert the move request into the move_requests table
        $move_query = $pdo->prepare(
            'INSERT INTO move_requests (user_id, billing_address, move_date, status) VALUES (?, ?, ?, "pending")'
        );
        $move_query->execute([$user_id, $full_address, $move_date]);

        echo "<p class='success'>✅ Move request saved successfully.</p>";

        // Prepare data for the API call
        $data = [
            'user_id' => $user_id,
            'new_address' => $full_address,
            'move_date' => $move_date
        ];

        // Call the WA-MOVE API (mock API)
        $ch = curl_init($wamove_api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code != 200) {
            throw new Exception("API Error: Failed to submit the move request. HTTP Code: $http_code");
        }

        echo "<p class='success'>✅ API call successful. Move request sent to WA-MOVE API.</p>";

        // Fetch the logged-in user's email
        $user_query = $pdo->prepare('SELECT email FROM users WHERE id = ?');
        $user_query->execute([$user_id]);
        $user = $user_query->fetch(PDO::FETCH_ASSOC);

        if (!$user || !filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            // Log invalid email and display error
            error_log("Invalid email for user ID: $user_id");
            throw new Exception("Invalid email address associated with the user account. Please verify your email.");
        }

        $recipient_email = $user['email'];

        // Send confirmation email using PHPMailer
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'wateramerica6@gmail.com';  // Your Gmail address
            $mail->Password = 'wpyzaoakuholjfrg';         // Your Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Email settings
            $mail->setFrom('wateramerica6@gmail.com', 'WaterAmerica');
            $mail->addAddress($recipient_email);
            $mail->isHTML(true);
            $mail->Subject = 'Move Request Confirmation';
            $mail->Body = "
                <h2>Move Request Submitted Successfully</h2>
                <p><strong>New Address:</strong> $full_address</p>
                <p><strong>Move Date:</strong> $move_date</p>
                <p>Thank you for choosing WaterAmerica!</p>
            ";

            $mail->send();
            echo "<p class='success'>✅ Confirmation email sent successfully!</p>";

            // Insert email log into email_logs table
            $log_query = $pdo->prepare(
                'INSERT INTO email_logs (user_id, email, subject, status) VALUES (?, ?, ?, ?)'
            );
            $log_query->execute([$user_id, $recipient_email, $mail->Subject, 'sent']);
        } catch (Exception $emailError) {
            error_log("Email sending failed for user ID: $user_id. Error: " . $emailError->getMessage());
            echo "<p class='error'>❌ Email service unavailable. Please try again later.</p>";

            // Log email failure
            $log_query = $pdo->prepare(
                'INSERT INTO email_logs (user_id, email, subject, status) VALUES (?, ?, ?, ?)'
            );
            $log_query->execute([$user_id, $recipient_email, 'Move Request Confirmation', 'failed']);
        }

    } catch (Exception $e) {
        echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<!-- CSS for styling messages and button -->
<style>
    body {
        font-family: Arial, sans-serif;
        text-align: center;
        margin-top: 50px;
    }
    .success {
        color: green;
        font-size: 18px;
        margin: 10px 0;
    }
    .error {
        color: red;
        font-size: 18px;
        margin: 10px 0;
    }
    .button {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        font-size: 16px;
    }
    .button:hover {
        background-color: #0056b3;
    }
</style>

<!-- Button to go back to the dashboard -->
<a href="dashboard.php" class="button">Return to Dashboard</a>
