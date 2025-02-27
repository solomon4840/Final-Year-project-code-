<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
include('../db.php');

// Check if a collaboration ID is provided in the URL
if (!isset($_GET['collab_id']) || empty($_GET['collab_id'])) {
    die("Invalid collaboration request.");
}

// Get collaboration ID from the URL
$collab_id = intval($_GET['collab_id']);

// Fetch collaboration details from the database
$sql = "
    SELECT pc.id, pc.project_id, pc.status, pc.invited_at, 
           p.name AS project_name, p.description, p.user_email AS owner_email
    FROM project_collaborators pc
    JOIN projects p ON pc.project_id = p.project_id
    WHERE pc.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $collab_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if a valid record is found
if ($result->num_rows == 0) {
    die("Collaboration request not found.");
}

$collaboration = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Collaboration - Research Portal</title>
    <link rel="stylesheet" href="../../css/common_functionalities/view_collaboration.css">
</head>
<body>
    <div class="wrapper">
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

        <main>
            <h2>Collaboration Details</h2>

            <!-- Collaboration Details -->
            <div class="collaboration-container">
                <p><strong>Project Name:</strong> <?php echo htmlspecialchars($collaboration['project_name']); ?></p>
                <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($collaboration['description'])); ?></p>
                <p><strong>Requested By:</strong> <?php echo htmlspecialchars($collaboration['owner_email']); ?></p>
                <p><strong>Requested On:</strong> <?php echo htmlspecialchars($collaboration['invited_at']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($collaboration['status']); ?></p>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="approve_collaboration.php?collab_id=<?php echo $collab_id; ?>" class="approve-btn">Approve</a>
                    <a href="reject_collaboration.php?collab_id=<?php echo $collab_id; ?>" class="reject-btn">Reject</a>
                    <a href="collaborations_dashboard.php" class="back-btn">Back to Collaborations</a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer>
            <p>&copy; 2025 University Research Collaboration Portal</p>
        </footer>
    </div>
</body>
</html>
