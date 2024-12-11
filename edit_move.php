<?php
session_start();
require 'config.php';
require 'vendor/autoload.php'; // Include PHPMailer library

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$move_id = $_GET['id'] ?? null;

if (!$move_id) {
    echo "<p class='error'>❌ Invalid request.</p>";
    exit();
}

// Fetch the move request details
try {
    $query = $pdo->prepare('SELECT billing_address, move_date FROM move_requests WHERE id = ? AND user_id = ?');
    $query->execute([$move_id, $user_id]);
    $move = $query->fetch(PDO::FETCH_ASSOC);

    if (!$move) {
        echo "<p class='error'>❌ Move request not found.</p>";
        exit();
    }
} catch (PDOException $e) {
    echo "<p class='error'>❌ Error fetching move request: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_billing_address = $_POST['billing_address'];
    $new_move_date = $_POST['move_date'];

    try {
        // Update the move request in the database
        $update_query = $pdo->prepare('UPDATE move_requests SET billing_address = ?, move_date = ? WHERE id = ?');
        $update_query->execute([$new_billing_address, $new_move_date, $move_id]);

        echo "<p class='success'>✅ Move request updated successfully in the database.</p>";

        // Prepare data for the WA-MOVE API call
        $data = [
            'user_id' => $user_id,
            'new_address' => $new_billing_address,
            'move_date' => $new_move_date
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

        if ($http_code == 200) {
            echo "<p class='success'>✅ Updated information sent to WA-MOVE service successfully!</p>";
        } else {
            throw new Exception("WA-MOVE Service Error: Failed to send updated information. HTTP Code: $http_code");
        }

        echo "<p><a href='move_history.php' class='button'>Return to Move History</a></p>";
        exit();
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><a href='edit_move.php?id=$move_id' class='button'>Retry</a></p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Move Request</title>

    <!-- Inline CSS for styling the edit move page -->
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

        .edit-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 30px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .edit-container h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .edit-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            text-align: left;
        }

        .edit-container label {
            font-weight: bold;
            color: #333;
        }

        .edit-container input[type="text"],
        .edit-container input[type="date"] {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
            width: 100%;
        }

        .edit-container button {
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .edit-container button:hover {
            background-color: #0056b3;
        }

        .back-link, .button {
            margin-top: 20px;
            display: inline-block;
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
            padding: 10px 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #007bff;
            transition: background 0.3s ease, color 0.3s ease;
        }

        .back-link:hover, .button:hover {
            background-color: #007bff;
            color: #fff;
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
    </style>
</head>
<body>

<div class="edit-container">
    <h2>Edit Move Request</h2>

    <form method="POST">
        <label for="billing_address">Billing Address:</label>
        <input type="text" id="billing_address" name="billing_address" value="<?php echo htmlspecialchars($move['billing_address']); ?>" required>

        <label for="move_date">Move Date:</label>
        <input type="date" id="move_date" name="move_date" value="<?php echo htmlspecialchars($move['move_date']); ?>" required>

        <button type="submit">Update Move Request</button>
    </form>

    <a href="move_history.php" class="back-link">Cancel and Return to Move History</a>
</div>

</body>
</html>
