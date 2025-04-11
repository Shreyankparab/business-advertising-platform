<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: loginreg.html");
    exit();
}

require 'config.php';

if (!isset($_GET['ad_id'])) {
    echo "Ad ID missing.";
    exit();
}

$ad_id = intval($_GET['ad_id']);
$user_id = $_SESSION['user_id'];

// Fetch the ad data
$stmt = $conn->prepare("SELECT * FROM advertisements WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $ad_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$ad = $result->fetch_assoc();

if (!$ad) {
    echo "Advertisement not found or unauthorized.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Advertisement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4">
        <h3>Edit Advertisement</h3>
        <form action="update_advertisement.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="ad_id" value="<?= $ad['id'] ?>">

            <label class="form-label">Heading</label>
            <input type="text" name="heading" value="<?= htmlspecialchars($ad['heading']) ?>" class="form-control" required>

            <label class="form-label mt-3">Description</label>
            <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($ad['description']) ?></textarea>

            <label class="form-label mt-3">Current Banner</label><br>
            <?php if (!empty($ad['image_path']) && file_exists($ad['image_path'])): ?>
                <img src="<?= $ad['image_path'] ?>" style="max-width: 200px;" class="mb-2">
            <?php else: ?>
                <p>No image available</p>
            <?php endif; ?>

            <label class="form-label">Change Banner (optional)</label>
            <input type="file" name="ad_image" class="form-control">

            <!-- Hidden field to preserve the current duration without allowing edits -->
            <input type="hidden" name="timeline" value="<?= $ad['timeline'] ?>">

            <button type="submit" class="btn btn-primary mt-4">Update Advertisement</button>
        </form>
    </div>
</div>

</body>
</html>
