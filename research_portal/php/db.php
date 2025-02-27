<?php
$host = "localhost";      // Database host
$user = "root";           // Default XAMPP user
$pass = "";               // Default XAMPP password is empty
$dbname = "research_portal"; // Your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
