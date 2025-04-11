<?php
// Start session only if it's not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config.php';

header("Content-Type: application/json");

$response = ["status" => "error", "message" => "Something went wrong"];

// Debug: Log received POST data
error_log("Received POST Data: " . print_r($_POST, true));

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $response["message"] = "User not logged in";
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];
$business_id = isset($_POST['business_id']) ? intval($_POST['business_id']) : 0;

// Debug: Log business ID
error_log("Extracted business_id: " . $business_id);

if ($business_id > 0) {
    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO track_clicks (user_id, business_id, clicked_at) VALUES (?, ?, NOW())");

    if ($stmt) {
        $stmt->bind_param("ii", $user_id, $business_id);
        if ($stmt->execute()) {
            $response = ["status" => "success", "message" => "Click tracked"];
        } else {
            $response["message"] = "Database execution failed: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $response["message"] = "SQL preparation failed: " . $conn->error;
    }
} else {
    $response["message"] = "Invalid business ID";
}

// Debug: Log response before sending it
error_log("Response: " . json_encode($response));

$conn->close();
echo json_encode($response);
?>
