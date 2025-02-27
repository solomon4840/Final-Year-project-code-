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
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['project_id'], $_POST['collaborator_email'])) {
    $project_id = $_POST['project_id'];
    $collaborator_email = $_POST['collaborator_email'];

    // Check if the logged-in user is the project owner
    $check_owner = "SELECT user_email FROM projects WHERE project_id = ? AND user_email = ?";
    $stmt = $conn->prepare($check_owner);
    $stmt->bind_param("is", $project_id, $user_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $message = "<p style='color: red;'>Unauthorized action. Only project owners can remove collaborators.</p>";
    } else {
        // Remove collaborator
        $delete_sql = "DELETE FROM project_collaborators WHERE project_id = ? AND collaborator_email = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("is", $project_id, $collaborator_email);
        
        if ($stmt->execute()) {
            $message = "<p style='color: green;'>Collaborator removed successfully.</p>";
        } else {
            $message = "<p style='color: red;'>Error removing collaborator.</p>";
        }
    }
}

// Fetch projects owned by the logged-in user
$sql = "SELECT p.project_id, p.name, pc.collaborator_email 
        FROM projects p
        JOIN project_collaborators pc ON p.project_id = pc.project_id
        WHERE p.user_email = ?";
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
    <title>Remove Collaborator</title>
    <link rel="stylesheet" href="../../css/common_functionalities/remove_collaborator.css"> <!-- Link to external CSS -->
</head>
<body>
    <header>
        <div class="logo">
            <a href="../../php/dashboards/normal_dashboard.php">
                <img src="../../images/UI_LOGO.jpeg" alt="Research Collaboration">
            </a>
		</div>	
        <div class="logout">
            <a href="../../php/profiles/logout.php" class="logout-btn">Logout</a>
        </div>
    </header>
    
    <div class="container">
        <h2>Remove Collaborator</h2>
        <?= $message ?>

        <form method="POST">
            <label for="project">Select Project:</label>
            <select name="project_id" id="project" required onchange="updateCollaboratorEmail()">
                <option value="">-- Select a project --</option>
                <?php 
                $project_data = []; // Store project & collaborator mapping for JavaScript
                while ($row = $result->fetch_assoc()): 
                    $project_data[$row['project_id']] = $row['collaborator_email'];
                ?>
                    <option value="<?= $row['project_id'] ?>">
                        <?= htmlspecialchars($row['name']) ?> (<?= htmlspecialchars($row['collaborator_email']) ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="collaborator">Collaborator Email:</label>
            <input type="email" name="collaborator_email" id="collaborator" readonly required>

            <button type="submit">Remove Collaborator</button>
        </form>
    </div>

    <script>
        // JavaScript to auto-fill collaborator email based on selected project
        const projectData = <?= json_encode($project_data) ?>;
        
        function updateCollaboratorEmail() {
            const projectSelect = document.getElementById("project");
            const collaboratorInput = document.getElementById("collaborator");
            const selectedProject = projectSelect.value;

            if (selectedProject in projectData) {
                collaboratorInput.value = projectData[selectedProject];
            } else {
                collaboratorInput.value = "";
            }
        }
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
<footer>
    <p>&copy; <?php echo date("Y"); ?> University Research Collaboration Portal. All rights reserved.</p>
</footer>
