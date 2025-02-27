<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require '../../php/db.php';

// Get the logged-in user's email
$userEmail = $_SESSION['email'];

// Check if a file was uploaded
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    // Define upload directory and file name
    $uploadDir = '../../uploads/profile_pictures/';
    $fileName = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
    $uploadFilePath = $uploadDir . $fileName;

    // Move the uploaded file to the upload directory
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFilePath)) {
        // Update the user's profile picture in the database
        $updateQuery = "UPDATE users SET profile_picture = '$fileName' WHERE email = '$userEmail'";
        if ($conn->query($updateQuery)) {
            echo "Profile picture uploaded successfully!";
        } else {
            die("Failed to update profile picture: " . $conn->error);
        }
    } else {
        die("Failed to upload file.");
    }
} else {
    die("No file uploaded or an error occurred.");
}
?>