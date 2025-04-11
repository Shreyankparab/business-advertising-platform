<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['ad_id'])) {
    header("Location: loginreg.html");
    exit();
}

$ad_id = intval($_POST['ad_id']);
$user_id = $_SESSION['user_id'];
$heading = $_POST['heading'];
$description = $_POST['description'];
$timeline = intval($_POST['timeline']);

$imagePath = null;

// Check if a new image was uploaded
if (!empty($_FILES['ad_image']['name'])) {
    $targetDir = "uploads/advertisements/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $fileName = uniqid() . "_" . basename($_FILES["ad_image"]["name"]);
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["ad_image"]["tmp_name"], $targetFilePath)) {
        $imagePath = $targetFilePath;
    } else {
        echo "Image upload failed.";
        exit();
    }
}

if ($imagePath) {
    $stmt = $conn->prepare("UPDATE advertisements SET heading = ?, description = ?, image_path = ?, timeline = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sssiii", $heading, $description, $imagePath, $timeline, $ad_id, $user_id);
} else {
    $stmt = $conn->prepare("UPDATE advertisements SET heading = ?, description = ?, timeline = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssiii", $heading, $description, $timeline, $ad_id, $user_id);
}

if ($stmt->execute()) {
    header("Location: post_advertisement.php");
    exit();
} else {
    echo "Update failed.";
}
