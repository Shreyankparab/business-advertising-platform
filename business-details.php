<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config.php'; // Database connection

// Get business ID from URL
$business_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($business_id <= 0) {
    die("Invalid business ID.");
}

// Fetch business details
$sql = "SELECT * FROM businesses WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $business_id);
$stmt->execute();
$result = $stmt->get_result();
$business = $result->fetch_assoc();

if (!$business) {
    die("Business not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($business['business_name']); ?> - Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
    body {
    background-color: #023047;
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.business-container {
    max-width: 800px;
    margin: 40px auto;
    padding: 30px;
    background: #ffffff;
    border-radius: 15px;
    border: 2px solid #ffffff;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.business-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25);
}

.business-img {
    width: 100%;
    max-height: 300px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 20px;
}

h2, h3 {
    color: #023047;
}

.btn-primary,
.btn-outline-primary {
    background-color: #023047 !important;
    border-color: #023047 !important;
    color: white !important;
}

.btn-primary:hover,
.btn-outline-primary:hover {
    background-color: #03537A !important;
    border-color: #03537A !important;
}

.business-details p {
    font-size: 16px;
    color: #333;
    line-height: 1.6;
}

</style>

</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top shadow" style="background-color:rgb(255, 255, 255);">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <a class="navbar-brand text-blue font-weight-bold" href="index.php">🏠 Home</a>
        <div>
            <?php if (!empty($ads)): ?>
                <button onclick="toggleAds()" class="btn btn-light me-2">View Ads</button>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container business-container">
    <h2 class="text-center"><?php echo htmlspecialchars($business['business_name']); ?></h2>
    
    <img src="<?php echo !empty($business['logo']) ? htmlspecialchars($business['logo']) : 'uploads/business_images/default-placeholder.png'; ?>" 
         alt="Business Image" class="business-img">
    
    <p><strong>Owner:</strong> <?php echo htmlspecialchars($business['owner_name']); ?></p>
    <p><strong>Category:</strong> <?php echo htmlspecialchars($business['category']); ?></p>
    <p><strong>Services:</strong> <?php echo htmlspecialchars($business['services']); ?></p>
    <p><strong>Opening Hours:</strong> <?php echo $business['opening_time'] . ' - ' . $business['closing_time']; ?></p>

    <p><strong>Location:</strong> <span id="business-location">Fetching...</span></p>
    
    <?php if (!empty($business['website'])): ?>
        <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($business['website']); ?>" target="_blank"><?php echo htmlspecialchars($business['website']); ?></a></p>
    <?php endif; ?>

    <a href="index.php" class="btn btn-primary">Back to Listings</a>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const lat = "<?php echo htmlspecialchars($business['latitude']); ?>";
    const lng = "<?php echo htmlspecialchars($business['longitude']); ?>";
    const locationElement = document.getElementById("business-location");

    if (lat && lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10`)
            .then(response => response.json())
            .then(data => {
                if (data && data.address) {
                    let place = data.address.village || data.address.town || data.address.city || data.address.suburb || data.address.county || data.address.state || "Location Not Found";
                    let googleMapsLink = `<a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank" class="btn btn-outline-primary btn-sm">View on Google Maps</a>`;
                    locationElement.innerHTML = `${place} <br> ${googleMapsLink}`;
                } else {
                    locationElement.innerHTML = "Not Available";
                }
            })
            .catch(error => {
                console.error("Error fetching location:", error);
                locationElement.innerHTML = "API Error";
            });
    }
});
</script>

</body>
</html>
