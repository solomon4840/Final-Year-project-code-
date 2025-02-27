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

$user_email = $_SESSION['email'];

// Fetch active collaborations where the user is either the project owner or an accepted collaborator
$sql = "SELECT p.project_id, p.name, p.user_email AS owner_email, 
        GROUP_CONCAT(CONCAT(pc.collaborator_email, ' (', pc.status, ')') SEPARATOR ', ') AS collaborators
        FROM projects p
        LEFT JOIN project_collaborators pc ON p.project_id = pc.project_id
        WHERE p.user_email = ? OR (pc.collaborator_email = ? AND pc.status = 'Accepted')
        GROUP BY p.project_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $user_email, $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Collaborations</title>
    <link rel="stylesheet" href="../../css/common_functionalities/collaborations.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="../../php/dashboards/normal_dashboard.php">
                <img src="../../images/UI_LOGO.jpeg" alt="Research Collaboration">
            </a>
        </div>
        <h1>Active Collaborations</h1>
        <div class="logout-menu">
            <form action="../../php/profiles/logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>
    
    <main>
        <table>
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Owner</th>
                    <th>Collaborators</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['owner_email']) ?></td>
                    <td><?= $row['collaborators'] ?: "No collaborators" ?></td>
                    <td>
                        <form action="view_project.php" method="GET" style="display:inline;">
                            <input type="hidden" name="project_id" value="<?= $row['project_id'] ?>">
                            <button type="submit" class="action-button view-button">View</button>
                        </form>
                        <?php if ($row['owner_email'] === $user_email): ?>
                            <form action="remove_collaborator.php" method="POST" style="display:inline;">
                                <input type="hidden" name="project_id" value="<?= $row['project_id'] ?>">
                                <button type="submit" class="action-button remove-button">Remove Collaborators</button>
                            </form>
                        <?php else: ?>
                            <form action="leave_collaboration.php" method="POST" style="display:inline;">
                                <input type="hidden" name="project_id" value="<?= $row['project_id'] ?>">
                                <button type="submit" class="action-button leave-button">Leave Collaboration</button>
                            </form>
                        <?php endif; ?>
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
