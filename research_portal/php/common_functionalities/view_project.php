<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "research_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the project ID from the URL
if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];

    // Fetch the project details from the database
    $sql = "SELECT * FROM projects WHERE project_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the project exists
    if ($result->num_rows > 0) {
        $project = $result->fetch_assoc();
    } else {
        echo "Project not found!";
        exit();
    }
} else {
    echo "No project selected!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Project</title>
    <link rel="stylesheet" href="../../css/common_functionalities/view_project.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <a href="../../php/dashboards/normal_dashboard.php">
                <img src="../../images/UI_LOGO.jpeg" alt="Research Collaboration">
            </a>
        </div>
        <h1>Project Details</h1>
        <div class="logout-menu">
            <form action="../../php/profiles/logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    <main>
        <div class="project-details">
            <h2><?= htmlspecialchars($project['name']) ?></h2>
            <p><strong>Description:</strong> <?= htmlspecialchars($project['description']) ?></p>
            <p><strong>Branch:</strong> <?= htmlspecialchars($project['branch']) ?></p>
            <p><strong>Start Date:</strong> <?= htmlspecialchars($project['start_date']) ?></p>
            <p><strong>Progress:</strong> <?= htmlspecialchars($project['progress']) ?>%</p>
            <p><strong>Collaborators:</strong> <?= htmlspecialchars($project['collaborators']) ?></p>
        </div>

        <div class="project-actions">
            <form action="edit_project.php" method="GET">
                <input type="hidden" name="id" value="<?= $project['project_id'] ?>">
                <button type="submit" class="action-button edit-button">Edit Project</button>
            </form>

            <form action="delete_project.php" method="GET" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this project?')">
                <input type="hidden" name="project_id" value="<?= $project['project_id'] ?>">
                <button type="submit" class="action-button delete-button">Delete Project</button>
            </form>
        </div>

        <a href="my_projects.php" class="back-button">Back to Projects</a>
    </main>

    <footer>
        <p>&copy; <?= date("Y") ?> University Research Collaboration Portal</p>
    </footer>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
