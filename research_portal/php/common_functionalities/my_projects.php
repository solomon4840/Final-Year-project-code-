<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "research_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch projects with collaborators for the logged-in user
$user_email = $_SESSION['email'];
$sql = "SELECT p.project_id, p.name, 
        GROUP_CONCAT(CONCAT(pc.collaborator_email, ' (', pc.status, ')') SEPARATOR ', ') AS collaborators
        FROM projects p
        LEFT JOIN project_collaborators pc ON p.project_id = pc.project_id
        WHERE p.user_email = ?
        GROUP BY p.project_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Projects</title>
    <link rel="stylesheet" href="../../css/common_functionalities/my_projects.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <a href="../../php/dashboards/normal_dashboard.php">
                <img src="../../images/UI_LOGO.jpeg" alt="Research Collaboration">
            </a>
        </div>
        <h1>My Projects</h1>
        <div class="logout-menu">
            <form action="../../php/profiles/logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    <main>
        <form action="create_new_project.php" method="GET">
            <button type="submit" class="add-project-button">+ Create New Project</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Collaborators</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['collaborators'] ?: "No collaborators" ?></td>
                    <td>
                        <form action="view_project.php" method="GET" style="display:inline;">
                            <input type="hidden" name="project_id" value="<?= $row['project_id'] ?>">
                            <button type="submit" class="action-button view-button">View</button>
                        </form>
                        <form action="edit_project.php" method="GET" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $row['project_id'] ?>">
                            <button type="submit" class="action-button edit-button">Edit</button>
                        </form>
                        <form action="delete_project.php" method="GET" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this project?')">
                            <input type="hidden" name="id" value="<?= $row['project_id'] ?>">
                            <button type="submit" class="action-button delete-button">Delete</button>
                        </form>
                        <form action="add_collaborator.php" method="GET" style="display:inline;">
                            <input type="hidden" name="project_id" value="<?= $row['project_id'] ?>">
                            <button type="submit" class="action-button add-button">Add Collaborators</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
