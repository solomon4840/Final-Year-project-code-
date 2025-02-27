<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Connect to the database
$conn = new mysqli("localhost", "root", "", "research_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate project ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $project_id = intval($_GET['id']); // Sanitize project ID

    // Check if the project exists
    $sql = "SELECT * FROM projects WHERE project_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $project = $result->fetch_assoc();

    if ($project) {
        // Proceed to delete the project
        $delete_sql = "DELETE FROM projects WHERE project_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $project_id);

        if ($delete_stmt->execute()) {
            // Redirect with a success message
            header("Location: my_projects.php?msg=Project successfully deleted");
            exit();
        } else {
            echo "Error deleting project: " . $conn->error;
        }

        $delete_stmt->close();
    } else {
        echo "Project not found.";
    }

    $stmt->close();
} else {
    echo "Invalid project ID.";
}

$conn->close();
?>
