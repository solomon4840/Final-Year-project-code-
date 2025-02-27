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

// Fetch user projects
$user_email = $_SESSION['email'];
$sql = "SELECT project_id, name FROM projects WHERE user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$projects = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Collaborators</title>
</head>
<body>
    <h2>Add Collaborators to a Project</h2>
    <form action="send_invite.php" method="POST">
        <label for="project">Select Project:</label>
        <select id="project" name="project_id" required>
            <?php while ($row = $projects->fetch_assoc()): ?>
                <option value="<?= $row['project_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endwhile; ?>
        </select><br><br>

        <label for="collaborator_email">Collaborator Email:</label>
        <input type="email" id="collaborator_email" name="collaborator_email" required><br><br>

        <button type="submit">Send Invitation</button>
    </form>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
