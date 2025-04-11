<?php
session_start();
include 'config.php'; // Database connection file

if (!isset($_SESSION['business_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$business_id = $_SESSION['business_id'];

$query = "SELECT business_name, owner_name, email, phone, category, location, business_logo FROM businesses WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $business_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $business = $result->fetch_assoc();
    echo json_encode(["status" => "success", "data" => $business]);
} else {
    echo json_encode(["status" => "error", "message" => "Business not found"]);
}
?>
