<?php
session_start();
require '../../php/db.php';

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch user details from the users table using email
$email = $_SESSION['email'];
$query = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "User not found.";
    exit();
}

$user_id = $user['id'];

// Fetch industry partner details using user_id
$query = "SELECT * FROM industry_partners WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$partner = $result->fetch_assoc();
$stmt->close();

if (!$partner) {
    echo "Industry partner profile not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Industry Partner Dashboard</title>
    <link rel="stylesheet" href="../../css/common_functionalities/industry_partners.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="normal_dashboard.php">
                <img src="../../images/UI_LOGO.jpeg" alt="Research Collaboration">
            </a>
        </div>
        <div class="logout-menu">
            <form action="../../php/profiles/logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($partner['company_name']); ?></h2>

        <!-- Profile Section -->
        <div class="card">
            <h3>Profile</h3>
            <p><strong>Industry Sector:</strong> <?php echo htmlspecialchars($partner['industry_sector']); ?></p>
            <p><strong>Website:</strong> 
                <a href="<?php echo htmlspecialchars($partner['website']); ?>" target="_blank">
                    <?php echo htmlspecialchars($partner['website']); ?>
                </a>
            </p>
            <p><strong>Areas of Interest:</strong> <?php echo htmlspecialchars($partner['areas_of_interest']); ?></p>
            <a href="../../php/common_functionalities/edit_profile.php">Edit Profile</a>
        </div>

        <!-- Funding Opportunities Section -->
        <div class="card">
            <h3>Funding Opportunities</h3>
            <a href="../../php/common_functionalities/post_funding_opportunity.php">Post New Funding Opportunity</a>
            <a href="../../php/common_functionalities/view_funding_opportunities.php">View Posted Opportunities</a>
        </div>

        <!-- Applications Section -->
        <div class="card">
            <h3>Applications</h3>
            <a href="../../php/common_functionalities/view_applications.php">View Applications</a>
        </div>

        <!-- Collaboration Requests Section -->
        <div class="card">
            <h3>Collaboration Requests</h3>
            <a href="view_collaboration_requests.php">View Collaboration Requests</a>
        </div>
    </div>

    <footer>
        <div class="footer-container">
            <p>&copy; <?php echo date("Y"); ?> University Research Collaboration Portal. All rights reserved.</p>
            <nav>
                <a href="../../php/common_functionalities/about.php">About</a>
                <a href="../../php/common_functionalities/contact.php">Contact</a>
                <a href="../../php/common_functionalities/privacy.php">Privacy Policy</a>
            </nav>
        </div>
    </footer>
</body>
</html>