<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require '../../php/db_connection.php';

// Get user ID from URL
if (!isset($_GET['user_id'])) {
    die("Invalid user ID.");
}

$user_id = intval($_GET['user_id']);

// Fetch user details
$user_query = "SELECT first_name, last_name FROM users WHERE id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

// Fetch resources uploaded by the user
$sql = "SELECT title, description, category, file_path, upload_date FROM resources WHERE uploaded_by = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources by <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></title>
    <link rel="stylesheet" href="../../css/dashboards/resources_dashboard.css">
</head>
<body>
    <header>
        <h1>Resources by <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
        <a href="resources_dashboard.php">Back to Resources Dashboard</a>
    </header>

    <h2>Uploaded Resources</h2>

    <table>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Category</th>
            <th>Upload Date</th>
            <th>Download</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['upload_date']); ?></td>
            <td><a href="../../uploads/<?php echo htmlspecialchars($row['file_path']); ?>" download>Download</a></td>
        </tr>
        <?php } ?>
    </table>
</body>
<footer>
    <p>&copy; 2025 University Research Collaboration Portal</p>
</footer>
</html>
