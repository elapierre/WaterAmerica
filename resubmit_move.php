<?php
session_start();
require 'config.php';

// Check if the user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<p style='color: red;'>❌ Access denied. Admin privileges are required.</p>";
    exit();
}

// Get the move request ID from the URL
$move_id = $_GET['id'] ?? null;

if (!$move_id) {
    echo "<p style='color: red;'>Invalid request.</p>";
    exit();
}

// Get the admin ID from the session
$admin_id = $_SESSION['user_id'];

try {
    // Fetch the move request
    $query = $pdo->prepare('SELECT * FROM move_requests WHERE id = ?');
    $query->execute([$move_id]);
    $move = $query->fetch(PDO::FETCH_ASSOC);

    if (!$move) {
        echo "<p style='color: red;'>Move request not found.</p>";
        exit();
    }

    // Prepare data for the WA-MOVE API
    $data = [
        'user_id' => $move['user_id'],
        'new_address' => $move['billing_address'],
        'move_date' => $move['move_date']
    ];

    // Call the WA-MOVE API (mock API)
    $ch = curl_init($wamove_api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Determine the status based on the API response
    if ($http_code == 200) {
        $status = 'pending'; // Set status to pending after successful resubmission attempt
        $error_message = NULL;
        echo "<p style='color: green;'>✅ Move request resubmitted successfully!</p>";
    } else {
        $status = 'failed'; // Keep status failed if the resubmission attempt fails
        $error_message = "WA-MOVE API Error: HTTP Code $http_code";
        echo "<p style='color: red;'>❌ Failed to resubmit move request. HTTP Code: $http_code</p>";
    }

    // Update the request status in the database
    $update_query = $pdo->prepare('UPDATE move_requests SET status = ?, error_message = ?, resubmission_attempts = resubmission_attempts + 1 WHERE id = ?');
    $update_query->execute([$status, $error_message, $move_id]);

    // Log the admin action into the admin_actions table
    $action_type = 'Resubmit Move Request';
    $log_query = $pdo->prepare('INSERT INTO admin_actions (admin_id, action_type, move_request_id, status) VALUES (?, ?, ?, ?)');
    $log_query->execute([$admin_id, $action_type, $move_id, $status]);

} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<!-- Button to return to the admin dashboard -->
<p><a href="admin_dashboard.php" class="button">Return to Admin Dashboard</a></p>

<!-- Inline CSS for button styling -->
<style>
    body {
        font-family: Arial, sans-serif;
        text-align: center;
        margin-top: 50px;
    }

    .button {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        font-size: 16px;
        transition: background 0.3s ease;
        margin-top: 20px;
    }

    .button:hover {
        background-color: #0056b3;
    }

    p {
        font-size: 18px;
        margin: 20px 0;
    }

    p.success {
        color: green;
    }

    p.error {
        color: red;
    }
</style>
