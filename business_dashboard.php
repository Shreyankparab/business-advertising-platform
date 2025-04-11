<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: loginreg.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch business details
$query = "SELECT * FROM businesses WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$business = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Business Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #023047;
            color: #ffffff;
            font-family: 'Poppins', sans-serif;
        }

        .container {
            margin-top: 50px;
            background: #ffffff;
            color: #023047;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }

        h2 {
            margin-bottom: 30px;
            font-weight: bold;
        }

        label {
            font-weight: 500;
        }

        .form-control[readonly], .form-control:disabled {
            background-color: #f8f9fa;
            border: none;
        }

        .btn-edit, .btn-save, .btn-cancel {
            margin-right: 10px;
        }

        .btn-edit {
            background-color: #023047;
            color: #fff;
        }

        .btn-save {
            background-color: #38b000;
            color: #fff;
        }

        .btn-cancel {
            background-color: #d90429;
            color: #fff;
        }

        .logo-preview, .images-preview {
            max-width: 100px;
            border-radius: 10px;
        }

        .form-section {
            margin-bottom: 20px;
        }
        .navbar-brand:hover,
        .btn-outline-light:hover {
            background-color: #ffffff;
            border-radius: 6px;
            color: #023047 !important;
            transition: 0.3s ease;
        }

    </style>
</head>
<body>
    <!-- Sticky Header -->
            <!-- Sticky Header -->
        <nav class="navbar navbar-expand-lg sticky-top shadow" style="background-color: #023047;">
            <div class="container-fluid">
                <a class="navbar-brand text-white fw-bold" href="index.php">🏠 Home</a>
                <div class="ms-auto d-flex gap-2">
                    <a class="btn btn-outline-light" href="post_advertisement.php">📢 Post Advertisement</a>
                    <a class="btn btn-outline-light" href="analytic_dashboard.php">📊 Analytics</a>
                </div>
            </div>
        </nav>


<div class="container">
    <h2>Your Business Details</h2>
    <form id="businessForm" method="POST" action="update_business.php" enctype="multipart/form-data">
        <?php
        $fields = [
            'business_name' => 'Business Name',
            'owner_name' => 'Owner Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'category' => 'Category',
            'description' => 'Description',
            'opening_time' => 'Opening Time',
            'closing_time' => 'Closing Time',
            'website' => 'Website',
        ];

        foreach ($fields as $field => $label): ?>
            <div class="form-section">
                <label for="<?= $field ?>"><?= $label ?>:</label>
                <input type="<?= $field == 'email' ? 'email' : 'text' ?>"
                       class="form-control"
                       id="<?= $field ?>"
                       name="<?= $field ?>"
                       value="<?= htmlspecialchars($business[$field]) ?>"
                       readonly>
            </div>
        <?php endforeach; ?>

        <!-- Services Multi-Select Dropdown -->
        <div class="form-section">
            <label for="services">Services:</label>
            <select class="form-control" id="services" name="services[]" multiple disabled>
                <?php
                $options = ["Home Delivery", "In-Store Pickup", "Online Booking", "24/7 Service", "Consultation"];
                $selectedServices = explode(",", $business['services']);
                foreach ($options as $option) {
                    $selected = in_array(trim($option), $selectedServices) ? "selected" : "";
                    echo "<option value=\"$option\" $selected>$option</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-section">
    <label for="location">Location:</label>
    <div class="input-group">
        <input type="text" 
               class="form-control" 
               id="location" 
               name="location" 
               value="<?= htmlspecialchars($business['location']) ?>" 
               readonly>

               <input type="hidden" name="latitude" id="latitude" value="<?= htmlspecialchars($business['latitude']) ?>">
                <input type="hidden" name="longitude" id="longitude" value="<?= htmlspecialchars($business['longitude']) ?>">

        <button type="button" class="btn btn-outline-secondary" onclick="clearLocation()">Clear</button>
        <button type="button" class="btn btn-outline-primary" onclick="captureLocation()">📍</button>
    </div>
</div>

        <!-- Logo Preview -->
        <div class="form-section">
            <label>Logo:</label><br>
            <img src="<?= $business['logo'] ?>" alt="Business Logo" class="logo-preview"><br>
            <input type="file" class="form-control mt-2 d-none" name="logo" id="logoInput">
        </div>

        <!-- Image Gallery -->
        <div class="form-section">
            <label>Images:</label><br>
            <?php
            $images = explode(',', $business['images']);
            foreach ($images as $img) {
                echo "<img src='$img' class='images-preview me-2 mb-2'>";
            }
            ?>
            <input type="file" class="form-control mt-2 d-none" name="images[]" id="imagesInput" multiple>
        </div>

        <button type="button" class="btn btn-edit" onclick="enableEdit()">Edit</button>
        <button type="submit" class="btn btn-save d-none">Save</button>
        <button type="button" class="btn btn-cancel d-none" onclick="cancelEdit()">Cancel</button>
    </form>
</div>

<script>
    
    function enableEdit() {
    const form = document.getElementById("businessForm");
    const inputs = form.querySelectorAll("input:not([type='file']), textarea");
    inputs.forEach(input => input.removeAttribute("readonly"));
    
    document.querySelector('.btn-edit').classList.add("d-none");
    document.querySelector('.btn-save').classList.remove("d-none");
    document.querySelector('.btn-cancel').classList.remove("d-none");
    
    document.getElementById("logoInput").classList.remove("d-none");
    document.getElementById("imagesInput").classList.remove("d-none");
    document.getElementById("services").removeAttribute("disabled"); // ✅ Enable services dropdown
}


    function cancelEdit() {
        location.reload();
    }

    function clearLocation() {
        document.getElementById("location").value = '';
    }

    function captureLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            position => {
                const lat = position.coords.latitude.toFixed(6);
                const lon = position.coords.longitude.toFixed(6);
                document.getElementById("location").value = `${lat}, ${lon}`;
                document.getElementById("latitude").value = lat;
                document.getElementById("longitude").value = lon;
            },
            error => {
                alert("Unable to retrieve location. Please check your browser permissions.");
            }
        );
    } else {
        alert("Geolocation is not supported by your browser.");
    }
}

</script>

</body>
</html>
