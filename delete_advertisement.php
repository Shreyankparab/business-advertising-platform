<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['ad_id'])) {
    header("Location: loginreg.html");
    exit();
}

$ad_id = intval($_POST['ad_id']);
$user_id = $_SESSION['user_id'];

// Optional: Delete the image file from server
$stmt = $conn->prepare("SELECT image_path FROM advertisements WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $ad_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$ad = $result->fetch_assoc();
if ($ad && !empty($ad['image_path']) && file_exists($ad['image_path'])) {
    unlink($ad['image_path']);
}

// Delete ad from database
$stmt = $conn->prepare("DELETE FROM advertisements WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $ad_id, $user_id);

if ($stmt->execute()) {
    header("Location: post_advertisement.php");
    exit();
} else {
    echo "Failed to delete ad.";
}
