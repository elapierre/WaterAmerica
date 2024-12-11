<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$move_id = $_GET['id'] ?? null;

if (!$move_id) {
    echo "<p style='color: red;'>Invalid request. No move ID provided.</p>";
    exit();
}

try {
    // Update the move status to 'canceled' if the status is 'pending' or empty
    $query = $pdo->prepare('UPDATE move_requests SET status = "canceled" WHERE id = ? AND user_id = ? AND (status = "pending" OR status IS NULL OR status = "")');
    $query->execute([$move_id, $_SESSION['user_id']]);

    if ($query->rowCount() > 0) {
        echo "<p style='color: green; text-align: center;'>Move request canceled successfully.</p>";
    } else {
        echo "<p style='color: red; text-align: center;'>Unable to cancel the move request. It may have already been processed or canceled, or the status is invalid.</p>";
    }

    echo "<p style='text-align: center;'><a href='move_history.php'>Return to Move History</a></p>";
} catch (PDOException $e) {
    echo "<p style='color: red; text-align: center;'>Error canceling move request: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p style='text-align: center;'><a href='move_history.php'>Return to Move History</a></p>";
    exit();
}
?>
