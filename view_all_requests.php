<?php
session_start();
require 'config.php';

// Check if the user is an admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<p style='color: red; text-align: center; margin-top: 50px;'>❌ Access denied. Admin privileges are required.</p>";
    exit();
}

// Fetch all move requests
try {
    $query = $pdo->query('SELECT * FROM move_requests');
    $move_requests = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p style='color: red; text-align: center;'>Error fetching move requests: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - All Move Requests</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f0f4f8;
            padding: 50px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 90%;
            border-collapse: collapse;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        table th {
            background-color: #007bff;
            color: white;
            text-transform: uppercase;
            font-size: 15px;
        }

        table td {
            color: #333;
        }

        .error {
            color: red;
        }

        .button {
            display: inline-block;
            padding: 8px 12px;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .button:hover {
            background-color: #218838;
        }

        .disabled-button {
            display: inline-block;
            padding: 8px 12px;
            background-color: #ccc;
            color: #fff;
            border-radius: 5px;
            font-size: 14px;
            cursor: not-allowed;
        }

        .logout-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #dc3545;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .logout-button:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<h2>Admin Dashboard - All Move Requests</h2>

<table>
    <tr>
        <th>ID</th>
        <th>User ID</th>
        <th>Billing Address</th>
        <th>Move Date</th>
        <th>Status</th>
        <th>Error Message</th>
        <th>Resubmission Attempts</th>
        <th>Actions</th>
    </tr>

    <?php if (empty($move_requests)): ?>
        <tr>
            <td colspan="8">No move requests found.</td>
        </tr>
    <?php else: ?>
        <?php foreach ($move_requests as $request): ?>
            <tr>
                <td><?php echo $request['id']; ?></td>
                <td><?php echo $request['user_id']; ?></td>
                <td><?php echo htmlspecialchars($request['billing_address']); ?></td>
                <td><?php echo htmlspecialchars($request['move_date']); ?></td>
                <td><?php echo htmlspecialchars($request['status']); ?></td>
                <td class="error"><?php echo htmlspecialchars($request['error_message'] ?? ''); ?></td>
                <td><?php echo $request['resubmission_attempts']; ?></td>
                <td>
                    <?php if ($request['status'] === 'failed'): ?>
                        <a href="resubmit_move.php?id=<?php echo $request['id']; ?>" class="button">Resubmit</a>
                    <?php else: ?>
                        <span class="disabled-button">N/A</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>

<!-- Logout Button -->
<a href="logout.php" class="logout-button">Logout</a>

</body>
</html>
