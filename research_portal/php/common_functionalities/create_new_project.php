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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $branch = $_POST['branch'];
    $start_date = $_POST['start_date'];
    $progress = $_POST['progress'];
    $collaborators = $_POST['collaborators'];
    $user_email = $_SESSION['email'];

    $sql = "INSERT INTO projects (name, description, branch, start_date, progress, collaborators, user_email) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiss", $name, $description, $branch, $start_date, $progress, $collaborators, $user_email);

    if ($stmt->execute()) {
        header("Location: my_projects.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Project</title>
    <link rel="stylesheet" href="../../css/common_functionalities/create_new_project.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <a href="../../php/dashboards/normal_dashboard.php">
                <img src="../../images/UI_LOGO.jpeg" alt="Research Collaboration">
            </a>
        </div>
        <h1>Create New Project</h1>
        <div class="logout-menu">
            <form action="../../php/profiles/logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <form action="create_new_project.php" method="POST">
            <label for="name">Project Name:</label>
            <input type="text" name="name" id="name" required>
            
            <label for="description">Description:</label>
            <textarea name="description" id="description" required></textarea>
            
            <label for="branch">Branch:</label>
            <input type="text" name="branch" id="branch" required>
            
            <label for="start_date">Start Date:</label>
            <input type="datetime-local" name="start_date" id="start_date" required>
            
            <label for="progress">Progress (%):</label>
            <input type="number" name="progress" id="progress" min="0" max="100" value="0">
            
            <label for="collaborators">Collaborators (comma-separated emails):</label>
            <textarea name="collaborators" id="collaborators"></textarea>
            
            <button type="submit">Create Project</button>
        </form>
    </main>
</body>
</html>
