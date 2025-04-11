<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: loginreg.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch business_id of current user
$bizQuery = $conn->prepare("SELECT id FROM businesses WHERE user_id = ?");
$bizQuery->bind_param("i", $user_id);
$bizQuery->execute();
$bizResult = $bizQuery->get_result();
$biz = $bizResult->fetch_assoc();
$business_id = $biz['id'];

// Fetch click data
$clickQuery = $conn->prepare("SELECT clicked_at FROM track_clicks WHERE business_id = ?");
$clickQuery->bind_param("i", $business_id);
$clickQuery->execute();
$result = $clickQuery->get_result();

$clicks_by_day = [];

while ($row = $result->fetch_assoc()) {
    $date = date("Y-m-d", strtotime($row['clicked_at']));
    if (!isset($clicks_by_day[$date])) {
        $clicks_by_day[$date] = 0;
    }
    $clicks_by_day[$date]++;
}

// Prepare data for chart
$labels = json_encode(array_keys($clicks_by_day));
$data = json_encode(array_values($clicks_by_day));
$totalClicks = array_sum($clicks_by_day);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytics Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #023047;
            color: #ffffff;
            font-family: 'Poppins', sans-serif;
        }

        .container {
            margin-top: 30px;
        }

        .card {
            background-color: #ffffff;
            color: #023047;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        .header {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }

        .btn {
            border-radius: 25px;
        }

        canvas {
            background-color: #fff;
            border-radius: 12px;
            padding: 10px;
        }

    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="btn btn-light">🏠 Home</a>
        <a href="business_dashboard.php" class="btn btn-light"> Back </a>
    </div>

    <div class="container">
        <div class="card mb-4 text-center">
            <h4>Total Clicks: <?= $totalClicks ?></h4>
        </div>

        <div class="card">
            <h5>Clicks Over Time</h5>
            <canvas id="clickChart"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('clickChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= $labels ?>,
                datasets: [{
                    label: 'Daily Clicks',
                    data: <?= $data ?>,
                    borderColor: '#219ebc',
                    backgroundColor: 'rgba(33,158,188,0.2)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#023047' }
                    },
                    x: {
                        ticks: { color: '#023047' }
                    }
                },
                plugins: {
                    legend: { labels: { color: '#023047' } }
                }
            }
        });
    </script>
</body>
</html>
