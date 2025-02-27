<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
include('../db.php');

// Get the logged-in user's email
$user_email = $_SESSION['email'];

// Fetch pending collaborations from the database
$sql = "SELECT * FROM project_collaborators WHERE status = 'Pending' AND collaborator_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are pending collaborations
$pending_collaborations = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pending_collaborations[] = $row;
    }
} else {
    $pending_collaborations = [];
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Collaborations - Research Portal</title>
    <link rel="stylesheet" href="../../css/common_functionalities/collaborations_dashboard.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <a href="../../php/dashboards/normal_dashboard.php">
                <img src="../../images/UI_LOGO.jpeg" alt="Research Collaboration">
            </a>
		</div>
        <h1>University of Ibadan Research Portal</h1>
        <div class="logout-menu">
            <form action="../../php/profiles/logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    <h2>Pending Collaborations</h2>

    <!-- Menu -->
    <div class="menu">
        <a href="profile.php">Profile</a>
        <a href="../../php/common_functionalities/my_projects.php">My Projects</a> <!-- Link to Project Management Module -->
        <a href="collaborations_dashboard.php">Collaborations</a>
        <a href="resources.php">Resources</a>
        <a href="messaging.php">Messaging</a>
        <a href="inter_institutional.php">Inter-Institutional Collaboration</a>
        <a href="../../php/common_functionalities/create_new_project.php">Create New Project</a>
        <a href="industry_partners.php">Industry Partners</a>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h3>Pending Collaboration Requests</h3>

        <!-- Collaboration Table -->
        <div class="collaboration-table">
            <h4>Pending Collaboration Requests</h4>
            <table>
                <thead>
                    <tr>
                        <th>Collaboration Name</th>
                        <th>Research Area</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Loop through the pending collaborations and display them
                    foreach ($pending_collaborations as $collab) {
                        echo "<tr>
                                <td>{$collab['project_id']}</td>
                                <td>{$collab['status']}</td>
                                <td>{$collab['status']}</td>
                                <td>
                                    <a href='view_collaboration.php?collab_id={$collab['id']}'>View</a> | 
                                    <a href='approve_collaboration.php?collab_id={$collab['id']}'>Approve</a> | 
                                    <a href='reject_collaboration.php?collab_id={$collab['id']}'>Reject</a>
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 University Research Collaboration Portal</p>
    </footer>
</body>
</html>
