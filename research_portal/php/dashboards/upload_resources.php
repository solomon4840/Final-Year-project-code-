<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['email']) || !isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require '../../php/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $uploaded_by = $_SESSION['user_id']; // Get user ID from session

    // File upload handling
    $target_dir = "../../uploads/resources/";
    $file_name = basename($_FILES["file"]["name"]);
    $target_file = $target_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Allowed file types
    $allowed_types = array("pdf", "doc", "docx", "png", "jpg", "jpeg", "ppt", "pptx", "xlsx");

    if (!in_array($file_type, $allowed_types)) {
        die("Error: Only PDF, DOC, DOCX, PPT, PPTX, XLSX, PNG, JPG, JPEG files are allowed.");
    }

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        // Insert data into database
        $stmt = $conn->prepare("INSERT INTO resources (title, description, category, file_path, uploaded_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $title, $description, $category, $file_name, $uploaded_by);

        if ($stmt->execute()) {
            header("Location: resources_dashboard.php?upload_success=true");
            exit();
        } else {
            die("Database Error: " . $stmt->error);
        }
    } else {
        die("File upload failed. Please try again.");
    }
}
?>
