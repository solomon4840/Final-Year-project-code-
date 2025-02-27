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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = $_POST['project_id'];
    $collaborator_email = $_POST['collaborator_email'];

    // Check if the collaborator is already invited
    $check_sql = "SELECT * FROM project_collaborators WHERE project_id = ? AND collaborator_email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("is", $project_id, $collaborator_email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "This collaborator has already been invited.";
    } else {
        // Insert new invitation
        $insert_sql = "INSERT INTO project_collaborators (project_id, collaborator_email, status) VALUES (?, ?, 'pending')";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("is", $project_id, $collaborator_email);
        
        if ($insert_stmt->execute()) {
            echo "Invitation sent successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>
