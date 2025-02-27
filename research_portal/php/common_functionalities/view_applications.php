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

// Check if the user is an industry partner
if ($role !== 'industry partner') {
    echo "You are not authorized to view applications.";
    exit();
}

// Fetch the industry partner's ID
$query = "SELECT partner_id FROM industry_partners WHERE user_id = ?";
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

$partner_id = $partner['partner_id'];

// Fetch all funding opportunities posted by the industry partner
$query = "SELECT * FROM funding_opportunities WHERE partner_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $partner_id);
$stmt->execute();
$result = $stmt->get_result();
$opportunities = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch all applications for the industry partner's funding opportunities
$applications = [];
foreach ($opportunities as $opportunity) {
    $opportunity_id = $opportunity['opportunity_id'];
    $query = "SELECT fa.*, u.first_name, u.last_name 
              FROM funding_applications fa
              JOIN users u ON fa.user_id = u.id
              WHERE fa.opportunity_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $opportunity_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $applications = array_merge($applications, $result->fetch_all(MYSQLI_ASSOC));
    $stmt->close();
}

// Handle approval/rejection
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['application_id']) && isset($_POST['status'])) {
        $application_id = $_POST['application_id'];
        $status = $_POST['status'];

        // Update the application status
        $query = "UPDATE funding_applications SET status = ? WHERE application_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $application_id);
        $stmt->execute();
        $stmt->close();

        // Refresh the page to reflect the updated status
        header("Location: view_applications.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applications</title>
    <link rel="stylesheet" href="../../css/common_functionalities/view_applications.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="../../php/dashboards/industry_partners_dashboard.php">
                <img src="../../images/UI_LOGO.jpeg" alt="Research Collaboration">
            </a>
        </div>
        <h1>View Applications</h1>
        <div class="logout-menu">
            <form action="../../php/profiles/logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    <div class="container">
        <h2>Applications for Your Funding Opportunities</h2>
        <?php if (empty($applications)): ?>
            <p>No applications found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Applicant Name</th>
                        <th>Proposal</th>
                        <th>Budget (USD)</th>
                        <th>Timeline</th>
                        <th>Proposal PDF</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $application): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($application['proposal']); ?></td>
                            <td>$<?php echo htmlspecialchars($application['budget']); ?></td>
                            <td><?php echo htmlspecialchars($application['timeline']); ?></td>
                            <td>
                                <?php if (!empty($application['proposal_pdf'])): ?>
                                    <a href="<?php echo htmlspecialchars($application['proposal_pdf']); ?>" download>Download PDF</a>
                                <?php else: ?>
                                    No PDF uploaded
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($application['status']); ?></td>
                            <td>
                                <form action="" method="post" style="display: inline;">
                                    <input type="hidden" name="application_id" value="<?php echo htmlspecialchars($application['application_id']); ?>">
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="accept-button">Accept</button>
                                </form>
                                <form action="" method="post" style="display: inline;">
                                    <input type="hidden" name="application_id" value="<?php echo htmlspecialchars($application['application_id']); ?>">
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="reject-button">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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