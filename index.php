<?php
session_start();
include 'config.php'; // Include database connection

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Check if the user has already registered a business
    $query = "SELECT * FROM businesses WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User has a business registered, redirect to dashboard
        $business_link = "business_dashboard.php";
    } else {
        // User has no business, redirect to business registration
        $business_link = "business_register_page.html";
    }

    $stmt->close();
} else {
    // If not logged in, redirect to login page
    $business_link = "loginreg.html";
}

$ads = [];

$query = "SELECT a.*, b.latitude, b.longitude 
          FROM advertisements a 
          JOIN businesses b ON a.user_id = b.user_id 
          WHERE DATE_ADD(a.posted_at, INTERVAL a.timeline DAY) >= CURDATE()
          ORDER BY a.posted_at DESC";

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $ads = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BCH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

 
</head>

<body>
    <div class="header-hero-bg">
        <div class="container-section">
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container-fluid">
                <!-- Navbar Toggler -->
                <button class="navbar-toggler order-lg-1 order-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
        
                <!-- Logo (Fixed in Position) -->
                <a class="navbar-brand mx-lg-3" href="#">VISTORA</a>
        
                <!-- Navbar Content -->
                <div class="collapse navbar-collapse justify-content-between order-lg-2" id="navbarNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#display-business">Details</a></li>
                        <li class="nav-item"><a class="nav-link" href="#ranks">Ranks</a></li>
                        <li class="nav-item"><a class="nav-link" href="#advertisements">Advertisement</a></li>

                        <li class="nav-item"><a class="nav-link" href="business-listing.php">View all</a></li>
                        <!-- <li class="nav-item"><a class="nav-link" href="#">Get Started</a></li> -->
                        
                    </ul>
        
                   <!-- Login/Register or Profile Dropdown -->
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <!-- Show Login/Registration Button if Not Logged In -->
                        <button class="button d-none d-lg-block" onclick="window.location.href='loginreg.html'">
                            <span class="button-content">Login/Registration</span>
                        </button>
                    <?php else: ?>
                        <!-- Show Profile Icon & Dropdown Menu if Logged In -->
                        <!-- Profile Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle d-flex align-items-center" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="assets/img/profile-icon.png" alt="Profile" class="profile-img me-2">
                                <span class="dropdown-icon"></span> <!-- Unicode ▼ arrow -->
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="#">Settings</a></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </div>

                    <?php endif; ?>

                    
                      
                </div>
            </div>

            <!-- Login/Register Button (Inside Menu at 425px and Below) -->
            <div class="collapse navbar-collapse d-lg-none" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <div class="d-lg-none mt-3 text-center">
                        <?php if (!isset($_SESSION['user_id'])): ?>
                    <button class="button d-none d-lg-block" onclick="window.location.href='loginreg.html'">
                        <span class="button-content">Login/Registration</span>
                    </button>
                    <?php endif; ?>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        
        <!-- Navbar Separator -->
        <div class="navbar-separator" id="navSeparator"></div>

        <div class="hero-content text-white">
            <h1 class="display-3 fw-bold" style="
            font-size: 5rem; 
            font-weight: 900; 
            font-family: 'Poppins Black', sans-serif;
            color: var(--primary-purple); 
            -webkit-text-stroke: 2px var(--primary-purple); 
            text-shadow: 1px 1px 0 var(--primary-purple), 
                         -1px -1px 0 var(--primary-purple),
                         2px 2px 0 var(--primary-purple);">Discover & Connect with Businesses</h1>
            <p class="lead">Empowering SMBs to grow and thrive in the digital world.</p>
            <!-- <div class="get-started-btn"> -->
            <a href="<?php echo $business_link; ?>">
                <button class="button-get-started">
                    <span class="button-content">&nbsp; Get Started &nbsp; </span>
                </button>
            </a>
            <!-- </div> -->
            </div>

      
    </div>
    </div>
    <div class="display-business" id="display-business">
    <div class="container">
        <!-- Section 1: For Customers -->
        <div class="business business-1">
            <div class="business-text">
                <h3>Discover Local Gems Instantly</h3>
                <p>
                    Our platform connects you with nearby businesses that cater to your every need—from cafes and clothing boutiques to repair services and more. Skip the search engine hassle and explore personalized recommendations with verified reviews and up-to-date offers.
                </p>
            </div>
            <div class="business-img">
                <img src="assets/img/image1.jpg" alt="business image">
            </div>
        </div>

        <!-- Section 2: For Business Owners -->
        <div class="business business-2">
            <div class="business-img">
                <img src="assets/img/image2.jpg" alt="business image" style="border-radius: 100px 0px  0px 100px;">
            </div>
            <div class="business-text">
                <h3>Grow Your Business with Visibility</h3>
                <p>
                    Whether you're launching a new venture or running an established business, our hub gives you the tools to stand out. Post ads, track views, and reach a wider audience—all from one seamless dashboard designed with business owners in mind.
                </p>
            </div>
        </div>

        <!-- Section 3: Mutual Growth & Digital Edge -->
        <div class="business business-3">
            <div class="business-text">
                <h3>Empowering Communities Through Technology</h3>
                <p>
                    By bridging the gap between local businesses and their customers, we’re fostering stronger communities and supporting small-scale entrepreneurship. Our digital-first approach ensures everyone stays ahead in today’s fast-moving market.
                </p>
            </div>
            <div class="business-img">
                <img src="assets/img/image3.jpg" alt="business image">
            </div>
        </div>
    </div>
</div>

    

    </div>


    <div class="demo-class2 mt-3 mt-md-4 mt-lg-4" id="advertisements">
    <h2 class="text-center mb-4"><strong>Featured Advertisements</strong></h2>
    <div class="container">
        <div class="row">
            <?php foreach ($ads as $ad): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 custom-ad-card">
                        <?php
                            // Get only the filename from full path
                            $imageName = basename($ad['image_path']);
                        ?>
                        <img src="uploads/advertisements/<?php echo htmlspecialchars($imageName); ?>" class="card-img-top" alt="Advertisement Image">

                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($ad['heading']); ?></h5>
                            <p class="card-text">
                                <?php echo nl2br(htmlspecialchars($ad['description'])); ?>
                            </p>
                            <a href="business-details.php?id=<?php echo $ad['id']; ?>" class="btn custom-btn">View Business</a>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>









<div class="demo-class1" id="ranks">
<div class="glass-div">
    <h1>TOP BUSINESS</h1>
    <div class="top-business-list">
        <?php
        include 'config.php';

        $sql = "
            SELECT b.business_name
            FROM businesses b
            JOIN (
                SELECT business_id, COUNT(*) AS total_clicks
                FROM track_clicks
                GROUP BY business_id
                ORDER BY total_clicks DESC
                LIMIT 4
            ) AS top_clicks ON b.id = top_clicks.business_id
        ";

        $result = mysqli_query($conn, $sql);

        $rank = 1;
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $icon = '';
        switch ($rank) {
            case 1:
                $icon = '<i class="fas fa-trophy" style="color: gold;"></i>';
                break;
            case 2:
                $icon = '<i class="fas fa-medal" style="color: silver;"></i>';
                break;
            case 3:
                $icon = '<i class="fas fa-award" style="color: #cd7f32;"></i>';
                break;
            default:
                $icon = "#$rank";
        }

        echo '<div class="business-rank-card">';
        echo '<div class="rank-number">' . $icon . '</div>';
        echo '<div class="business-name">' . htmlspecialchars($row['business_name']) . '</div>';
        echo '</div>';
        $rank++;
    }
} else {
    echo "<div class='business-rank-card'><span>No top businesses found</span></div>";
}

        ?>
    </div>
</div>



    <div class="demo-img-div">
        <div class="display-img-box dib1">
            <div class="display d1"></div>
            <div class="display d2"></div>
        </div>
        <div class="display-img-box dib2">
            <div class="display d3"></div>
            <div class="display d4"></div>
        </div>
    </div>
</div>






    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="assets/js/script.js"></script>


</body>
</html>
