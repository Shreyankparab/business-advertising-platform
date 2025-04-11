<?php
session_start(); // Start session to get user_id

$servername = "localhost";
$username = "root";
$password = "1234";
$database = "business_hub";

// Create database connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User is not logged in!");
}
$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $business_name = $_POST['business_name'];
    $owner_name = $_POST['owner_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $category = $_POST['category'];
    $location = $_POST['location'];
    $latitude = isset($_POST['latitude']) && is_numeric($_POST['latitude']) ? $_POST['latitude'] : NULL;
    $longitude = isset($_POST['longitude']) && is_numeric($_POST['longitude']) ? $_POST['longitude'] : NULL;
    $description = $_POST['description'];
    $opening_time = $_POST['opening_time'];
    $closing_time = $_POST['closing_time'];
    $website = isset($_POST['website']) ? $_POST['website'] : NULL;
    $services = isset($_POST['services']) ? implode(", ", $_POST['services']) : "";

    // Handle logo upload
    $logo_target_file = "uploads/logos/default-placeholder.jpg"; // Default logo
    if (isset($_FILES["logo"]) && $_FILES["logo"]["error"] == 0) {
        $logo_target_dir = "uploads/logos/";
        $logo_file_name = time() . "_" . basename($_FILES["logo"]["name"]);
        $logo_target_file = $logo_target_dir . $logo_file_name;
        move_uploaded_file($_FILES["logo"]["tmp_name"], $logo_target_file);
    }

    // Handle multiple business images upload
    $image_paths = [];
    $image_target_dir = "uploads/business_images/";
    if (isset($_FILES["business_images"]) && !empty($_FILES["business_images"]["name"][0])) {
        foreach ($_FILES["business_images"]["tmp_name"] as $key => $tmp_name) {
            $image_file_name = time() . "_" . basename($_FILES["business_images"]["name"][$key]);
            $image_target_file = $image_target_dir . $image_file_name;
            if (move_uploaded_file($_FILES["business_images"]["tmp_name"][$key], $image_target_file)) {
                $image_paths[] = $image_target_file;
            }
        }
    }
    $images = implode(", ", $image_paths);

    // Prepare SQL statement to prevent SQL injection
    $sql = "INSERT INTO businesses (user_id, business_name, owner_name, email, phone, category, location, latitude, longitude, description, opening_time, closing_time, services, website, logo, images) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssddsssssss", $user_id, $business_name, $owner_name, $email, $phone, $category, $location, $latitude, $longitude, $description, $opening_time, $closing_time, $services, $website, $logo_target_file, $images);
    
    if ($stmt->execute()) {
        echo "<script>alert('Business registered successfully!'); window.location.href='index.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();
?>