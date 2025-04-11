<?php
session_start();
include 'config.php';


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
        }
        .sidebar {
            height: 100vh;
            background-color: #023047;
            color: white;
            padding-top: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar a:hover {
            background-color: #03537A;
        }
        .content {
            padding: 30px;
        }
        .card {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 sidebar">
            <div>
                <h4 class="text-center mb-4">Admin Panel</h4>
                <a href="?view=users">View Users</a>
                <a href="?view=businesses">View Businesses</a>
            </div>
            <div class="text-center mb-4">
                <a href="logout.php" class="btn btn-danger w-75">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 content">
            <h3>Dashboard</h3>

            <?php
            if (isset($_GET['view']) && $_GET['view'] == 'users') {
                echo "<h4 class='mt-4'>Registered Users</h4>";
                $userQuery = "SELECT id, name, email FROM users";
                $userResult = mysqli_query($conn, $userQuery);

                if ($userResult && mysqli_num_rows($userResult) > 0) {
                    echo "<div class='card p-3'><table class='table table-striped'>
                            <thead><tr><th>ID</th><th>Name</th><th>Email</th></tr></thead><tbody>";
                    while ($row = mysqli_fetch_assoc($userResult)) {
                        echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['email']}</td></tr>";
                    }
                    echo "</tbody></table></div>";
                } else {
                    echo "<p>No users found.</p>";
                }
            }

            if (isset($_GET['view']) && $_GET['view'] == 'businesses') {
                echo "<h4 class='mt-4'>Listed Businesses</h4>";
                $bizQuery = "SELECT id, business_name, owner_name, email, phone, category, location FROM businesses";
                $bizResult = mysqli_query($conn, $bizQuery);

                if ($bizResult && mysqli_num_rows($bizResult) > 0) {
                    echo "<div class='card p-3'><table class='table table-bordered'>
                            <thead>
                                <tr>
                                    <th>ID</th><th>Business Name</th><th>Owner</th><th>Email</th><th>Phone</th><th>Category</th><th>Location</th>
                                </tr>
                            </thead>
                            <tbody>";
                    while ($row = mysqli_fetch_assoc($bizResult)) {
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['business_name']}</td>
                            <td>{$row['owner_name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['phone']}</td>
                            <td>{$row['category']}</td>
                            <td>{$row['location']}</td>
                        </tr>";
                    }
                    echo "</tbody></table></div>";
                } else {
                    echo "<p>No businesses found.</p>";
                }
            }
            ?>
        </div>
    </div>
</div>

</body>
</html>
