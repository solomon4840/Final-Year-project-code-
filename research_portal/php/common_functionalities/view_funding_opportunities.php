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
$query = "SELECT id, role FROM users WHERE email = ?";
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
$role = strtolower($user['role']); // Convert to lowercase for consistency

// Fetch all funding opportunities
$query = "SELECT fo.*, ip.company_name 
          FROM funding_opportunities fo
          JOIN industry_partners ip ON fo.partner_id = ip.partner_id
          ORDER BY fo.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$opportunities = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Funding Opportunities</title>
    <link rel="stylesheet" href="../../css/common_functionalities/view_funding_opportunities.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="../../php/dashboards/normal_dashboard.php">
                <img src="../../images/UI_LOGO.jpeg" alt="Research Collaboration">
            </a>
        </div>
        <h1>View Funding Opportunities</h1>
        <div class="logout-menu">
            <form action="../../php/profiles/logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    <div class="container">
        <h2>Funding Opportunities</h2>

        <!-- Show "Post Funding Opportunity" button for industry partners -->
        <?php if ($role === 'industry partner'): ?>
            <div class="post-funding-button">
                <a href="post_funding_opportunity.php" class="button">Post New Funding Opportunity</a>
            </div>
        <?php endif; ?>

        <?php if (empty($opportunities)): ?>
            <p>No funding opportunities available.</p>
        <?php else: ?>
            <ul class="opportunities-list">
                <?php foreach ($opportunities as $opportunity): ?>
                    <li class="opportunity-card">
                        <h3><?php echo htmlspecialchars($opportunity['title']); ?></h3>
                        <p><strong>Posted by:</strong> <?php echo htmlspecialchars($opportunity['company_name']); ?></p>
                        <p><?php echo htmlspecialchars($opportunity['description']); ?></p>
                        <p><strong>Funding Amount:</strong> $<?php echo htmlspecialchars($opportunity['funding_amount']); ?></p>
                        <p><strong>Eligibility Criteria:</strong> <?php echo htmlspecialchars($opportunity['eligibility_criteria']); ?></p>
                        <p><strong>Deadline:</strong> <?php echo htmlspecialchars($opportunity['deadline']); ?></p>
                        <p><strong>Created At:</strong> <?php echo htmlspecialchars($opportunity['created_at']); ?></p>

                        <!-- Show Apply Button only for students and researchers -->
                        <?php if ($role === 'student' || $role === 'researcher'): ?>
                            <a href="apply_funding.php?opportunity_id=<?php echo htmlspecialchars($opportunity['opportunity_id']); ?>" class="apply-button">Apply for Funding</a>
                        <?php elseif ($role === 'industry partner'): ?>
                            <p><em>Industry partners cannot apply for funding.</em></p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
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