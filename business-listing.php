<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config.php'; // Ensure this file has a valid database connection

// Get the selected category from the URL (if any)
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';

// Fetch distinct categories
$categoryQuery = "SELECT DISTINCT category FROM businesses";
$categoryResult = $conn->query($categoryQuery);

// Fetch businesses based on the selected category
if ($selectedCategory) {
    $sql = "SELECT * FROM businesses WHERE category = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $selectedCategory);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM businesses";
    $result = $conn->query($sql);
}

// Check if query was successful
if (!$result || !$categoryResult) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Listings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
    .business-card {
        width: 100%;
        max-width: 350px;
        border-radius: 10px;
        overflow: hidden;
        padding: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: 0.3s;
        cursor: pointer;
    }
    .business-card img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        border-radius: 10px 10px 0 0;
    }
    .business-card .card-body {
        padding: 10px;
    }
    
    /* Category Filter Links */
    .category-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .category-link {
        padding: 8px 16px;
        text-decoration: none;
        border: 1px solid #023047;
        border-radius: 5px;
        color: #023047;
        transition: 0.3s;
    }
    .category-link:hover, .category-link.active {
        background-color: #023047;
        color: white;
    }

    /* Buttons */
    .btn-primary, .btn-outline-primary {
        background-color: #023047 !important;
        border-color: #023047 !important;
        color: white !important;
    }
    .btn-primary:hover, .btn-outline-primary:hover {
        background-color: #03537A !important;
        border-color: #03537A !important;
    }
</style>

</head>
<body>

    <div class="container mt-4">
        <h2 class="text-center">Business Listings</h2>

        <!-- Category Filter Links -->
        <div class="category-buttons">
            <?php
                $categories = [];
                while ($categoryRow = $categoryResult->fetch_assoc()) {
                    $categories[] = htmlspecialchars($categoryRow['category']);
                }
                foreach ($categories as $categoryName) {
                    $activeClass = ($selectedCategory == $categoryName) ? 'active' : '';
                    echo '<a href="?category=' . urlencode($categoryName) . '" class="category-link ' . $activeClass . '">' . $categoryName . '</a>';
                }
            ?>
        </div>

        <!-- Business Cards Section -->
        <div class="row justify-content-center" id="business-list">
        <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imagePath = !empty($row['logo']) ? $row['logo'] : 'uploads/business_images/default-placeholder.png';

                    if (!file_exists($imagePath)) {
                        $imagePath = 'uploads/business_images/default-placeholder.png';
                    }

                    echo '
                    <div class="col-md-4 col-sm-6 mb-4 d-flex justify-content-center business-card-container">
                        <div class="business-card card" data-id="' . htmlspecialchars($row['id']) . '">
                            <img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($row['business_name']) . '">
                            <div class="card-body">
                                <h5 class="card-title">' . htmlspecialchars($row['business_name']) . '</h5>
                                <p class="card-text"><strong>Owner:</strong> ' . htmlspecialchars($row['owner_name']) . '</p>
                                
                                <p class="card-text location-name" 
                                   data-lat="' . htmlspecialchars($row['latitude']) . '" 
                                   data-lng="' . htmlspecialchars($row['longitude']) . '">
                                   <strong>Location:</strong> Fetching...
                                </p>

                                <p class="card-text"><strong>Category:</strong> ' . htmlspecialchars($row['category']) . '</p>
                                <p class="card-text"><strong>Opening Hours:</strong> ' . $row['opening_time'] . ' - ' . $row['closing_time'] . '</p>
                                <p class="card-text"><strong>Services:</strong> ' . htmlspecialchars($row['services']) . '</p>';

                                if (!empty($row['website'])) {
                                    echo '<a href="' . htmlspecialchars($row['website']) . '" target="_blank" class="btn btn-sm btn-primary">Visit Website</a>';
                                }

                                echo '
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo "<p class='text-center'>No businesses found.</p>";
            }
        ?>
        </div>
    </div>

    <!-- JavaScript for Click Handling -->
    <script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".business-card").forEach(card => {
        card.addEventListener("click", function (event) {
            if (event.target.tagName.toLowerCase() === "a" || event.target.tagName.toLowerCase() === "button") {
                return;
            }

            const businessId = this.getAttribute("data-id");
            console.log("Clicked Business ID:", businessId); // Debugging

            if (!businessId) {
                console.error("Business ID is undefined or empty.");
                return;
            }

            fetch("track_click.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `business_id=${encodeURIComponent(businessId)}`
            })
            .then(response => response.json())
            .then(data => {
                console.log("Response:", data);
                if (data.status === "success") {
                    console.log("Click tracked successfully!");
                } else {
                    console.error("Error tracking click:", data.message);
                }
            })
            .catch(error => console.error("Fetch error:", error));

            window.location.href = `business-details.php?id=${businessId}`;
        });
    });
});


</script>



</body>
</html>
