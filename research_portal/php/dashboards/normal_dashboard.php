<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Research Portal</title>
    <link rel="stylesheet" href="../../css/dashboards/normal_dashboard.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <a href="normal_dashboard.php">
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

    <h2>USER DASHBOARD</h2>

    <!-- Menu -->
    <div class="menu">
        <a href="profile.php">Profile</a>
        <a href="../../php/common_functionalities/my_projects.php">My Projects</a> <!-- Link to Project Management Module -->
        <a href="../../php/common_functionalities/collaborations_dashboard.php">Collaborations</a>
        <a href="../../php/dashboards/resources_dashboard.php">Resources</a>
        <a href="../../php/dashboards/messages_dashboard.php">Messaging</a>
        <a href="../../php/common_functionalities/create_new_project.php">Create New Project</a>
        <a href="../../php/dashboards/industry_partners_dashboard.php">Industry Partners</a>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h2>Dashboard Overview</h2>
        <div class="card">
            <h3>Active Projects</h3>
            <p>View and manage your active research projects.</p>
            <a href="../../php/common_functionalities/my_projects.php">Go to Projects</a> <!-- Link to Project Management Module -->
        </div>
        <div class="card">
            <h3>Collaborations</h3>
            <p>Connect and collaborate with other researchers.</p>
            <a href="../../php/common_functionalities/collaborations_dashboard.php">Explore Collaborations</a>
        </div>
        <div class="card">
            <h3>Profile</h3>
            <p>View and update your profile information.</p>
            <a href="profile.php">Edit Profile</a>
        </div>
        <div class="card">
            <h3>Resource Sharing</h3>
            <p>Access datasets, research tools, and funding information.</p>
            <a href="../../php/dashboards/resources_dashboard.php">Explore Resources</a>
        </div>
        <div class="card">
            <h3>Messaging</h3>
            <p>Communicate with researchers and collaborators.</p>
            <a href="messaging.php">Go to Messaging</a>
        </div>
        <div class="card">
            <h3>Create New Project</h3>
            <p>Initiate a new research project and invite collaborators.</p>
            <a href="../../php/common_functionalities/create_new_project.php">Start a New Project</a>
        </div>
        <div class="card">
            <h3>Industry Partners</h3>
            <p>Find industry partners to fund and support your projects.</p>
            <a href="industry_partners_dashboard.php">Explore Industry Partners</a>
        </div>
        <div class="card">
            <h3>View Funding Opportunities</h3>
            <p>Explore available funding opportunities for your research.</p>
            <a href="../../php/common_functionalities/view_funding_opportunities.php">View Opportunities</a>
        </div>
    </div>
</body>

<footer>
    <p>&copy; 2025 University Research Collaboration Portal</p>
</footer>
</html>
