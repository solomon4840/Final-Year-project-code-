<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "research_portal");

// Check connection
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

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_POST['project_id']) || empty($_POST['collaborator_email'])) {
        $message = "<p class='error'>Invalid request. Missing required fields.</p>";
    } else {
        $project_id = intval($_POST['project_id']);
        $collaborator_email = filter_var($_POST['collaborator_email'], FILTER_SANITIZE_EMAIL);

        if (!filter_var($collaborator_email, FILTER_VALIDATE_EMAIL)) {
            $message = "<p class='error'>Invalid email format.</p>";
        } else {
            // Check if the project exists
            $sql_check_project = "SELECT * FROM projects WHERE project_id = ?";
            $stmt_project = $conn->prepare($sql_check_project);
            $stmt_project->bind_param("i", $project_id);
            $stmt_project->execute();
            $result_project = $stmt_project->get_result();

            if ($result_project->num_rows === 0) {
                $message = "<p class='error'>Error: Project does not exist.</p>";
            } else {
                // Check if collaborator already exists
                $sql_check = "SELECT * FROM project_collaborators WHERE project_id = ? AND collaborator_email = ?";
                $stmt_check = $conn->prepare($sql_check);
                $stmt_check->bind_param("is", $project_id, $collaborator_email);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();

                if ($result_check->num_rows > 0) {
                    $message = "<p class='error'>Error: This collaborator is already added to the project.</p>";
                } else {
                    // Insert new collaborator
                    $sql = "INSERT INTO project_collaborators (project_id, collaborator_email, status) VALUES (?, ?, 'pending')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $project_id, $collaborator_email);

                    if ($stmt->execute()) {
                        $message = "<p class='success'>Collaborator added successfully!</p>";
                    } else {
                        $message = "<p class='error'>Error adding collaborator: " . $stmt->error . "</p>";
                    }
                    $stmt->close();
                }
                $stmt_check->close();
            }
            $stmt_project->close();
        }
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Collaborator - Research Portal</title>
    <link rel="stylesheet" href="../../css/common_functionalities/add_collaborator.css">
</head>
<body>
    <div class="wrapper">
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

        <main>
            <h2>Add Collaborator</h2>
            <?php echo $message; ?>

            <form action="add_collaborator.php" method="POST">
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

            <div class="back">
                <a href="collaborations_dashboard.php">Back to Collaborations</a>
            </div>
        </main>

        <!-- Footer -->
        <footer>
            <p>&copy; 2025 University Research Collaboration Portal</p>
        </footer>
    </div>
</body>
</html>
