<?php
session_start();
require 'config.php'; // Include database configuration

// Check if the user is logged in, redirect to login if not
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Set a default error message variable
$error_message = null;

try {
    // Attempt to retrieve the user's billing address and phone number from the database
    $user_id = $_SESSION['user_id'];
    $query = $pdo->prepare('SELECT billing_address, city, state, zip_code FROM wa_bill WHERE user_id = ?');
    $query->execute([$user_id]);
    $billing_info = $query->fetch(PDO::FETCH_ASSOC);

    // Fetch the user's phone number
    $phone_query = $pdo->prepare('SELECT phone_number FROM users WHERE id = ?');
    $phone_query->execute([$user_id]);
    $user = $phone_query->fetch(PDO::FETCH_ASSOC);
    $phone_number = $user['phone_number'] ?? '';
    
    // Check if billing info is available
    if (!$billing_info) {
        $error_message = "Billing information unavailable. Please enter your address manually.";
    }
} catch (PDOException $e) {
    // Display error message
    $error_message = "Service error: Unable to retrieve billing information. Please enter your address manually.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Move Request - WaterAmerica</title>

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

        .form-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 30px;
            max-width: 500px;
            width: 100%;
        }

        .form-container h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container p.error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
            text-align: center;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-container label {
            font-weight: bold;
            color: #333;
            text-align: left;
        }

        .form-container input[type="text"],
        .form-container input[type="date"],
        .form-container input[type="tel"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            width: 100%;
        }

        .form-container .sms-section {
            margin-top: 15px;
        }

        .form-container button {
            padding: 10px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .form-container button:hover {
            background: #0056b3;
        }

        .form-container .back-link {
            margin-top: 15px;
            text-align: center;
        }

        .form-container .back-link a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        .form-container .back-link a:hover {
            text-decoration: underline;
        }

        #phone_number {
            display: none;
        }
    </style>

    <script>
        // Function to restrict move date to future dates
        function setMinDate() {
            const today = new Date();
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            const minDate = `${year}-${month}-${day}`;
            document.getElementById("move_date").setAttribute("min", minDate);
        }

        // Function to show/hide the phone number input based on checkbox
        function togglePhoneNumberInput() {
            const smsCheckbox = document.getElementById("send_sms");
            const phoneNumberInput = document.getElementById("phone_number");

            if (smsCheckbox.checked) {
                phoneNumberInput.style.display = "block";
            } else {
                phoneNumberInput.style.display = "none";
            }
        }

        window.onload = setMinDate;
    </script>
</head>
<body>

<div class="form-container">
    <h2>Move Request Form</h2>

    <?php if ($error_message): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form action="submit_move.php" method="POST">
        <label for="billing_address">Billing Address:</label>
        <input type="text" id="billing_address" name="billing_address" 
               value="<?php echo $billing_info ? htmlspecialchars($billing_info['billing_address']) : ''; ?>" required>

        <label for="city">City:</label>
        <input type="text" id="city" name="city" 
               value="<?php echo $billing_info ? htmlspecialchars($billing_info['city']) : ''; ?>" required>

        <label for="state">State:</label>
        <input type="text" id="state" name="state" 
               value="<?php echo $billing_info ? htmlspecialchars($billing_info['state']) : ''; ?>" required>

        <label for="zip_code">Zip Code:</label>
        <input type="text" id="zip_code" name="zip_code" 
               value="<?php echo $billing_info ? htmlspecialchars($billing_info['zip_code']) : ''; ?>" required>

        <label for="move_date">Move Date:</label>
        <input type="date" id="move_date" name="move_date" required>

        <div class="sms-section">
            <label for="send_sms">
                <input type="checkbox" id="send_sms" name="send_sms" onchange="togglePhoneNumberInput()"> Send Move Info via SMS
            </label>

            <input type="tel" id="phone_number" name="phone_number" placeholder="Confirm your phone number" 
                   value="<?php echo htmlspecialchars($phone_number); ?>">
        </div>

        <button type="submit">Submit Move Request</button>
    </form>

    <div class="back-link">
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
