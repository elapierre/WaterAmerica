<?php
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed. Only POST requests are supported.']);
    exit();
}

// Get the raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Check if the required fields are present
if (!isset($data['user_id'], $data['new_address'], $data['move_date'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid payload. Missing required fields.']);
    exit();
}

// Simulate API response logic
$user_id = $data['user_id'];
$new_address = $data['new_address'];
$move_date = $data['move_date'];

// Log the incoming data (for debugging purposes)
error_log("Received move request: User ID: $user_id, Address: $new_address, Move Date: $move_date\n", 3, 'mock_api_log.txt');

// Simulate different outcomes for testing purposes
if (rand(1, 10) <= 8) {
    // 80% chance of success
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Move request processed successfully.',
        'data' => [
            'user_id' => $user_id,
            'new_address' => $new_address,
            'move_date' => $move_date
        ]
    ]);
} else {
    // 20% chance of failure
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal Server Error. Simulated API failure.'
    ]);
}
?>
