<?php
$servername = "localhost"; // Change if necessary
$username = "root"; // Change if you have a different username
$password = "1234"; // Change if you set a MySQL password
$database = "business_hub";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
