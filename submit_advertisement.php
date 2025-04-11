<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check required POST keys exist
    $timeline = isset($_POST['timeline']) ? $_POST['timeline'] : '';
    $cardNumber = isset($_POST['card_number']) ? $_POST['card_number'] : '';
    $cardExpiry = isset($_POST['card_expiry']) ? $_POST['card_expiry'] : '';
    $cardCVV = isset($_POST['card_cvv']) ? $_POST['card_cvv'] : '';
    $heading = isset($_POST['heading']) ? trim($_POST['heading']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';


    $fileValid = isset($_FILES['ad_image']) && $_FILES['ad_image']['error'] === 0;

    if ($timeline && $cardNumber && $cardExpiry && $cardCVV && $fileValid) {
        // Handle file upload
        $targetDir = "uploads/advertisements/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = basename($_FILES["ad_image"]["name"]);
        $targetFile = $targetDir . time() . "_" . $fileName;
        $uploadSuccess = move_uploaded_file($_FILES["ad_image"]["tmp_name"], $targetFile);

        $priceMap = [
            '1' => 100,
            '3' => 250,
            '5' => 400,
            '7' => 500,
            '14' => 900
        ];

        $basePrice = isset($priceMap[$timeline]) ? $priceMap[$timeline] : 0;
        $taxRate = 0.18;
        $taxAmount = $basePrice * $taxRate;
        $totalPrice = $basePrice + $taxAmount;

        // Dummy payment validation
        if ($uploadSuccess && strlen($cardNumber) == 16 && strlen($cardCVV) == 3) {
            $user_id = $_SESSION['user_id'] ?? 1; // For testing if session is not set

            $sql = "INSERT INTO advertisements (user_id, image_path, timeline, base_price, tax_amount, total_price, heading, description, posted_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isiddiss", $user_id, $targetFile, $timeline, $basePrice, $taxAmount, $totalPrice, $heading, $description);

            $stmt->execute();
            $stmt->close();

            echo "<h2 style='color: green; text-align: center;'>✅ Advertisement Posted Successfully!</h2>";
            echo "<p style='text-align: center;'>Total charged: ₹" . number_format($totalPrice, 2) . " (including tax)</p>";
            echo "<div style='text-align: center;'><a href='business_dashboard.php'>Return to Dashboard</a></div>";
            exit();
        } else {
            echo "<h3 style='color: red; text-align: center;'>❌ Invalid card or image upload failed.</h3>";
        }
    } else {
        echo "<h3 style='color: red; text-align: center;'>❌ Please fill all fields and upload a valid image.</h3>";
    }
}
?>
