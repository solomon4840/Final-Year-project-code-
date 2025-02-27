<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
require '../../php/db.php';

// Get user email from session
$userEmail = $_SESSION['email'];

// Get search and filter parameters
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
$projectId = isset($_GET['project_id']) ? $_GET['project_id'] : '';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch projects where user is a collaborator or owner
$projects = $conn->query("
    SELECT p.project_id, p.name 
    FROM projects p
    LEFT JOIN project_collaborators pc ON p.project_id = pc.project_id
    WHERE p.user_email = '$userEmail' OR pc.collaborator_email = '$userEmail'
");

// Fetch resources uploaded by user and also those available to collaborators
$sql = "SELECT r.id, r.title, r.description, r.file_path, r.upload_date, r.category, 
               r.uploaded_by, u.first_name, u.last_name, r.project_id, p.name AS project_title
        FROM resources r
        JOIN users u ON r.uploaded_by = u.email
        JOIN projects p ON r.project_id = p.project_id
        LEFT JOIN project_collaborators pc ON p.project_id = pc.project_id
        WHERE r.uploaded_by = '$userEmail' OR pc.collaborator_email = '$userEmail'";

if (!empty($searchQuery)) {
    $sql .= " AND (r.title LIKE '%$searchQuery%' OR r.description LIKE '%$searchQuery%')";
}

if (!empty($categoryFilter)) {
    $sql .= " AND r.category = '$categoryFilter'";
}

if (!empty($projectId)) {
    $sql .= " AND r.project_id = '$projectId'";
}

$sql .= " ORDER BY r.upload_date DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources Dashboard - Research Portal</title>
    <link rel="stylesheet" href="../../css/dashboards/resources_dashboard.css">
</head>
<body>
    <header>   
        <div class="logo">
            <a href="normal_dashboard.php">
                <img src="../../images/UI_LOGO.jpeg" alt="Research Collaboration">
            </a>
        </div>
        <h1>University of Ibadan Research Portal - Resources</h1>
        <div class="logout-menu">
            <form action="../../php/profiles/logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    <h2>Resources Dashboard</h2>

    <!-- Search, Filter, and Project Selection Form -->
    <form method="GET">
        <input type="text" name="search" placeholder="Search resources..." value="<?php echo htmlspecialchars($searchQuery); ?>">
        <select name="category">
            <option value="">All Categories</option>
            <option value="AI" <?php echo ($categoryFilter == 'AI') ? 'selected' : ''; ?>>AI</option>
            <option value="Data Science" <?php echo ($categoryFilter == 'Data Science') ? 'selected' : ''; ?>>Data Science</option>
            <option value="Machine Learning" <?php echo ($categoryFilter == 'Machine Learning') ? 'selected' : ''; ?>>Machine Learning</option>
        </select>
        <select name="project_id">
            <option value="">All Projects</option>
            <?php while ($project = $projects->fetch_assoc()) { ?>
                <option value="<?php echo $project['project_id']; ?>" <?php echo ($projectId == $project['project_id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($project['name']); ?>
                </option>
            <?php } ?>
        </select>
        <button type="submit">Search</button>
    </form>

    <!-- Upload Resource Form -->
    <div class="container">
        <h3>Upload a New Resource</h3>
        <form action="upload_resources.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Resource Title" required>
            <textarea name="description" placeholder="Resource Description" required></textarea>
            <input type="text" name="category" placeholder="Category (e.g., AI, Data Science)" required>
            <select name="project_id" required>
                <option value="">Select Project</option>
                <?php
                $projects->data_seek(0); // Reset project pointer
                while ($project = $projects->fetch_assoc()) { ?>
                    <option value="<?php echo $project['project_id']; ?>"><?php echo htmlspecialchars($project['name']); ?></option>
                <?php } ?>
            </select>
            <input type="file" name="file" required>
            <button type="submit">Upload Resource</button>
        </form>
    </div>

    <!-- Display Resources -->
    <h3>Available Resources</h3>
    <table>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Category</th>
            <th>Project</th>
            <th>Uploaded By</th>
            <th>Upload Date</th>
            <th>Download</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['project_title']); ?></td>
            <td>
                <a href="user_resources.php?user_id=<?php echo $row['uploaded_by']; ?>">
                    <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                </a>
            </td>
            <td><?php echo htmlspecialchars($row['upload_date']); ?></td>
            <td><a href="../../uploads/<?php echo htmlspecialchars($row['file_path']); ?>" download>Download</a></td>
            <td>
                <?php if ($row['uploaded_by'] == $userEmail) { ?>
                    <a href="edit_resource.php?id=<?php echo $row['id']; ?>">Edit</a>
                    <a href="delete_resource.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
<footer>
    <p>&copy; 2025 University Research Collaboration Portal</p>
</footer>
</html>
