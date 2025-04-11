<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Update profile on form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $user_id);

    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Error updating profile.";
    }
    $stmt->close();
}

// Fetch user info
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background-color: #f4f4f4; }
        .container { max-width: 400px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
        input[type=text], input[type=email] {
            width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px;
        }
        input[type=submit], .logout-btn {
            background: #023047; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;
        }
        .logout-btn { background: #e63946; text-decoration: none; display: inline-block; margin-top: 10px; }
        .message { color: green; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Your Profile</h2>
    <?php if ($message) echo "<p class='message'>$message</p>"; ?>
    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

        <input type="submit" value="Update Profile">
    </form>

    <a class="logout-btn" href="logout.php">Logout</a>
</div>

</body>
</html>
