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
    <title>Collaborations - Research Portal</title>
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

    <h2>Collaborations Dashboard</h2>

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
        <h3>Explore and Manage Collaborations</h3>

        <!-- Collaboration Search and Filter -->
        <div class="collaboration-search">
            <form action="collaborations_dashboard.php" method="GET">
                <input type="text" name="search" placeholder="Search Collaborations..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="card">
            <h4>Active Collaborations</h4>
            <p>View and manage your active collaborations with other researchers.</p>
            <a href="../../php/common_functionalities/active_collaborations.php">View Active Collaborations</a>
        </div>

        <div class="card">
            <h4>New Collaboration Request</h4>
            <p>Initiate a new collaboration with other researchers.</p>
            <a href="../../php/common_functionalities/add_collaborator.php">Create New Collaboration</a>
        </div>

        <div class="card">
            <h4>Pending Collaborations</h4>
            <p>Review and approve pending collaboration requests.</p>
            <a href="../../php/common_functionalities/pending_collaborations.php">View Pending Requests</a>
        </div>

        <!-- Collaboration Table (if search query exists, it will filter results) -->
        <div class="collaboration-table">
            <h4>Collaboration Results</h4>
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
                    // Assume $collaborations is an array fetched from the database based on the search
                    $search = isset($_GET['search']) ? $_GET['search'] : '';

                    // Example database query (replace with actual query)
                    $collaborations = []; // Placeholder for actual collaboration data
                    // Mock data for demonstration
                    $collaborations[] = ['name' => 'AI in Healthcare', 'research_area' => 'Artificial Intelligence', 'status' => 'Active'];
                    $collaborations[] = ['name' => 'Blockchain for Supply Chain', 'research_area' => 'Blockchain Technology', 'status' => 'Pending'];

                    // Filter results based on search term
                    foreach ($collaborations as $collab) {
                        if (stripos($collab['name'], $search) !== false || stripos($collab['research_area'], $search) !== false) {
                            echo "<tr>
                                    <td>{$collab['name']}</td>
                                    <td>{$collab['research_area']}</td>
                                    <td>{$collab['status']}</td>
                                    <td>
                                        <a href='view_collaboration.php?collab_name={$collab['name']}'>View</a>
                                        <a href='manage_collaboration.php?collab_name={$collab['name']}'>Manage</a>
                                    </td>
                                  </tr>";
                        }
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
