<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "research_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the project details
if (isset($_GET['id'])) {
    $project_id = $_GET['id'];

    $sql = "SELECT * FROM projects WHERE project_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();

    if (!$project) {
        echo "Project not found.";
        exit();
    }
} else {
    echo "Invalid project ID.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $branch = $_POST['branch'];
    $start_date = $_POST['start_date'];
    $progress = $_POST['progress'];
    $collaborators = $_POST['collaborators'];

    $update_sql = "UPDATE projects SET name = ?, description = ?, branch = ?, start_date = ?, progress = ?, collaborators = ? WHERE project_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssisi", $name, $description, $branch, $start_date, $progress, $collaborators, $project_id);

    if ($update_stmt->execute()) {
        header("Location: my_projects.php");
        exit();
    } else {
        echo "Error updating project: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>
    <link rel="stylesheet" href="../../css/common_functionalities/edit_project.css">
</head>
<body>
    <header>
        <div>
            <a href="../../php/dashboards/normal_dashboard.php">
                <img src="../../images/UI_LOGO.jpeg" alt="Research Collaboration" style="height: 50px; vertical-align: middle;">
            </a>
            <span>Edit Project</span>
        </div>
        <div class="logout-menu">
            <form action="../../php/profiles/logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>
    <main>
        <form action="edit_project.php?id=<?= $project_id ?>" method="POST">
            <label for="name">Project Name:</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($project['name']) ?>" required>
            
            <label for="description">Description:</label>
            <textarea name="description" id="description" required><?= htmlspecialchars($project['description']) ?></textarea>
            
            <label for="branch">Branch:</label>
            <input type="text" name="branch" id="branch" value="<?= htmlspecialchars($project['branch']) ?>" required>
            
            <label for="start_date">Start Date:</label>
            <input type="datetime-local" name="start_date" id="start_date" value="<?= htmlspecialchars($project['start_date']) ?>" required>
            
            <label for="progress">Progress (%):</label>
            <input type="number" name="progress" id="progress" min="0" max="100" value="<?= htmlspecialchars($project['progress']) ?>">

            <label for="collaborators">Collaborators (comma-separated emails):</label>
            <textarea name="collaborators" id="collaborators"><?= htmlspecialchars($project['collaborators']) ?></textarea>
            
            <button type="submit">Update Project</button>
        </form>
    </main>
    <footer>
        <p>&copy; 2025 University of Ibadan. All rights reserved.</p>
    </footer>
</body>
</html>
