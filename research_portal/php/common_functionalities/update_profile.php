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

// Get form data
$firstName = $_POST['first_name'];
$middleName = $_POST['middle_name'];
$lastName = $_POST['last_name'];
$phoneNumber = $_POST['phone_number'];

// Update user data
$updateQuery = "UPDATE users 
                SET first_name = '$firstName', 
                    middle_name = '$middleName', 
                    last_name = '$lastName', 
                    phone_number = '$phoneNumber' 
                WHERE email = '$userEmail'";
if ($conn->query($updateQuery)) {
    echo "Profile updated successfully!";
} else {
    die("Failed to update profile: " . $conn->error);
}
?>