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

// Check if collaboration ID is provided
if (!isset($_GET['collab_id']) || empty($_GET['collab_id'])) {
    die("Invalid collaboration request.");
}

// Get collaboration ID
$collab_id = intval($_GET['collab_id']);

// Fetch collaboration details
$sql = "SELECT id, status FROM project_collaborators WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $collab_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Collaboration request not found.");
}

$collaboration = $result->fetch_assoc();

// Check if collaboration is already rejected
if ($collaboration['status'] === 'Rejected') {
    echo "<script>alert('This collaboration request has already been rejected.'); window.location.href='collaborations_dashboard.php';</script>";
    exit();
}

// Reject collaboration request
$update_sql = "UPDATE project_collaborators SET status = 'Rejected' WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("i", $collab_id);

if ($update_stmt->execute()) {
    echo "<script>alert('Collaboration request rejected successfully.'); window.location.href='collaborations_dashboard.php';</script>";
} else {
    echo "<script>alert('Error rejecting collaboration request. Please try again.'); window.location.href='collaborations_dashboard.php';</script>";
}

// Close database connection
$stmt->close();
$update_stmt->close();
$conn->close();
?>
