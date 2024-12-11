<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch move requests for the logged-in user
try {
    $query = $pdo->prepare('SELECT id, billing_address, move_date, status FROM move_requests WHERE user_id = ?');
    $query->execute([$user_id]);
    $moves = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error fetching move history: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Move History - WaterAmerica</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f4f8;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            background: #fff;
        }
        table th {
            background: #007bff;
            color: white;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        .edit-link {
            color: #28a745;
            font-weight: bold;
        }
        .cancel-link {
            color: #dc3545;
            font-weight: bold;
        }
        .cancel-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Your Move History</h2>

    <?php if (empty($moves)): ?>
        <p>No move requests found.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Billing Address</th>
                <th>Move Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($moves as $move): ?>
                <tr>
                    <td><?php echo htmlspecialchars($move['billing_address']); ?></td>
                    <td><?php echo htmlspecialchars($move['move_date']); ?></td>
                    <td><?php echo htmlspecialchars($move['status']); ?></td>
                    <td>
                        <a href="edit_move.php?id=<?php echo $move['id']; ?>" class="edit-link">Edit</a>
                        <?php if ($move['status'] == 'pending'): ?>
                            | <a href="cancel_move.php?id=<?php echo $move['id']; ?>" class="cancel-link" onclick="return confirm('Are you sure you want to cancel this move request?');">Cancel</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <p><a href="dashboard.php">Return to Dashboard</a></p>
</body>
</html>
