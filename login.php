<?php
session_start();
require 'config.php';

$message = ""; // Variable to hold the message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check for admin credentials
    if ($email === "admin@gmail.com" && $password === "admin@123") {
        $_SESSION['user_id'] = 0; // You can set 0 or any fixed value for admin
        $_SESSION['name'] = "Admin";
        header("Location: admin.php");
        exit();
    }

    // Normal user login
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['name'] = $name;
            $message = "Login successful!";

            header("Location: index.php");
            exit();
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "No user found with this email!";
    }

    $stmt->close();
    $conn->close();
}

echo $message; // Show login result message if needed
?>
