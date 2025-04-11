<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: loginreg.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Step 1: Collect values from POST
$fields = [
    'business_name', 'owner_name', 'email', 'phone', 'category',
    'location', 'description', 'opening_time', 'closing_time', 'website'
];

$values = [];
$types = "";

// Loop through and collect main fields
foreach ($fields as $field) {
    $values[] = $_POST[$field] ?? '';
    $types .= "s";
}

// Step 2: Handle services as comma-separated string
$services = isset($_POST['services']) ? implode(", ", $_POST['services']) : '';
$values[] = $services;
$types .= "s";
$fields[] = 'services';

// ✅ Step 3: Directly get latitude and longitude from POST (hidden fields, not location text)
$lat = $_POST['latitude'] ?? '';
$lng = $_POST['longitude'] ?? '';

// Cast to float to make sure they're numeric
$lat = (float)$lat;
$lng = (float)$lng;

$values[] = $lat;
$values[] = $lng;
$types .= "dd"; // double (float) types
$fields[] = 'latitude';
$fields[] = 'longitude';

// Step 4: Add user_id for WHERE clause
$values[] = $user_id;
$types .= "i";

// Step 5: Construct SQL
$set_clause = implode(", ", array_map(fn($f) => "$f = ?", $fields));
$query = "UPDATE businesses SET $set_clause WHERE user_id = ?";

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param($types, ...$values);

if (!$stmt->execute()) {
    die("Execution failed: " . $stmt->error);
}

$stmt->close();

header("Location: business_dashboard.php");
exit();
?>
