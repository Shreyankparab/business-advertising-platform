<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: loginreg.html");
    exit();
}
require 'config.php';

$userId = $_SESSION['user_id'];

$adsQuery = "SELECT * FROM advertisements WHERE user_id = ?";
$stmt = $conn->prepare($adsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$ads = [];
while ($row = $result->fetch_assoc()) {
    $ads[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post Advertisement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f4f8; font-family: 'Segoe UI', sans-serif; }
        .container { max-width: 700px; margin: 50px auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .info-section { background-color: #023047; color: #ffffff; padding: 25px; border-radius: 10px; margin-bottom: 30px; }
        .info-section h4 { font-weight: bold; }
        .price-list { margin-top: 15px; }
        .price-list li { margin-bottom: 8px; font-size: 16px; }
        .form-select, .form-control { margin-bottom: 20px; }
        .price-display { font-size: 1.2rem; font-weight: bold; }
        .btn-primary { background-color: #023047; border: none; }
        .btn-primary:hover { background-color: #03588c; }
        .navbar-brand { font-weight: bold; }
        .navbar .btn { margin-left: 10px; }
        @media (max-width: 576px) {
            .navbar .btn { margin-top: 5px; margin-left: 0; }
        }
        .ad-card { border: 1px solid #ccc; border-radius: 10px; padding: 15px; margin-top: 20px; background-color: #f8f9fa; }
        .ad-card img { max-width: 100%; border-radius: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top shadow" style="background-color: #023047;">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <a class="navbar-brand text-white" href="index.php">🏠 Home</a>
        <div>
            <?php if (!empty($ads)): ?>
                <button onclick="toggleAds()" class="btn btn-light me-2">View Ads</button>
            <?php endif; ?>
            <a href="business_dashboard.php" class="btn btn-light">Back</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="info-section">
        <h4>📢 Advertise Your Business</h4>
        <p>Boost your visibility by displaying a featured advertisement on our homepage and category sections. Choose your campaign duration, upload a banner, and attract more customers!</p>
        <ul class="price-list">
            <li><strong>1 Day:</strong> ₹100</li>
            <li><strong>3 Days:</strong> ₹250</li>
            <li><strong>5 Days:</strong> ₹400</li>
            <li><strong>7 Days:</strong> ₹500</li>
            <li><strong>14 Days:</strong> ₹900</li>
        </ul>
    </div>

    <form action="submit_advertisement.php" method="POST" enctype="multipart/form-data">

    <label for="heading" class="form-label">Advertisement Heading:</label>
    <input type="text" name="heading" id="heading" class="form-control" required>

    <!-- New Description Field -->
    <label for="description" class="form-label">Advertisement Description:</label>
    <textarea name="description" id="description" rows="4" class="form-control" required></textarea>

        <label for="image" class="form-label">Advertisement Banner:</label>
        <input type="file" name="ad_image" id="image" class="form-control" required>

        <label for="duration" class="form-label">Select Duration:</label>
        <select class="form-select" name="timeline" id="duration" onchange="updateCost()" required>
            <option value="">-- Choose Duration --</option>
            <option value="1">1 Day</option>
            <option value="3">3 Days</option>
            <option value="5">5 Days</option>
            <option value="7">7 Days</option>
            <option value="14">14 Days</option>
        </select>

        <div class="price-display">Base Price: ₹<span id="base-price">0</span></div>
        <div class="price-display">Tax (18%): ₹<span id="tax">0</span></div>
        <div class="price-display text-success">Total Price: ₹<span id="total">0</span></div>

        <hr>

        <h5 class="mb-3">💳 Payment Details</h5>
        <input type="text" name="card_number" class="form-control" placeholder="Card Number (16 digits)" maxlength="16" required>
        <input type="text" name="card_expiry" class="form-control" placeholder="Expiry Date (MM/YY)" required>
        <input type="text" name="card_cvv" class="form-control" placeholder="CVV (3 digits)" maxlength="3" required>

        <button type="submit" class="btn btn-primary w-100 mt-3">Submit Advertisement</button>
    </form>

    <!-- Toggle Section for Active Ads -->
    <div id="viewAdsSection" style="display:none;">
        <h4 class="mt-5">📺 Your Active Advertisements</h4>
        <?php foreach ($ads as $index => $ad): 
            $posted_at = strtotime($ad['posted_at']);
            $end_time = strtotime("+{$ad['timeline']} days", $posted_at);
            ?>
            <div class="ad-card">
                            <?php if (!empty($ad['image_path']) && file_exists($ad['image_path'])): ?>
                    <img src="<?= $ad['image_path'] ?>" alt="Ad Image" style="max-width:100%; height:auto; margin-bottom:10px;">
                    <p><strong>Heading:</strong> <?= htmlspecialchars($ad['heading']) ?></p>
                    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($ad['description'])) ?></p>

                <?php else: ?>
                    <p><em>No image available.</em></p>
                <?php endif; ?>



                <p><strong>Duration:</strong> <?= htmlspecialchars($ad['timeline']) ?> Day(s)</p>
                <p><strong>Time Left:</strong> <span id="timer-<?= $index ?>"></span></p>
                <script>
                const countdown<?= $index ?> = setInterval(function () {
                    const now = new Date().getTime();
                    const endTime = <?= $end_time * 1000 ?>;
                    const distance = endTime - now;

                    if (distance < 0) {
                        clearInterval(countdown<?= $index ?>);
                        document.getElementById("timer-<?= $index ?>").innerText = "Expired";
                        return;
                    }

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    document.getElementById("timer-<?= $index ?>").innerText = 
                        `${days}d ${hours}h ${minutes}m ${seconds}s`;
                }, 1000);
                </script>
            </div>
            <div class="d-flex justify-content-end mt-3">
    <form action="edit_advertisement.php" method="GET" class="me-2">
        <input type="hidden" name="ad_id" value="<?= $ad['id'] ?>">
        <button type="submit" class="btn btn-warning btn-sm">✏️ Edit</button>
    </form>
    <form action="delete_advertisement.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this ad?');">
        <input type="hidden" name="ad_id" value="<?= $ad['id'] ?>">
        <button type="submit" class="btn btn-danger btn-sm">🗑️ Delete</button>
    </form>
</div>


        <?php endforeach; ?>
    </div>
</div>

<script>
const costMapping = {1: 100, 3: 250, 5: 400, 7: 500, 14: 900};

function updateCost() {
    const selected = document.getElementById("duration").value;
    const base = costMapping[selected] || 0;
    const tax = Math.round(base * 0.18);
    const total = base + tax;
    document.getElementById("base-price").innerText = base;
    document.getElementById("tax").innerText = tax;
    document.getElementById("total").innerText = total;
}

function toggleAds() {
    const section = document.getElementById("viewAdsSection");
    section.style.display = section.style.display === "none" ? "block" : "none";
}
</script>

</body>
</html>
