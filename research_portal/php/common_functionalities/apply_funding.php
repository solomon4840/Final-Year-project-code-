<?php
session_start();
require '../../php/db.php';

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch user details from the users table using email
$email = $_SESSION['email'];
$query = "SELECT id, role FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "User not found.";
    exit();
}

$user_id = $user['id'];
$role = strtolower($user['role']); // Convert to lowercase for consistency

// Check if the user is a student or researcher
if ($role !== 'student' && $role !== 'researcher') {
    echo "You are not authorized to apply for funding.";
    exit();
}

// Get the opportunity ID from the URL
if (!isset($_GET['opportunity_id'])) {
    echo "Invalid request. No opportunity ID provided.";
    exit();
}

$opportunity_id = $_GET['opportunity_id'];

// Fetch the funding opportunity details with company_name
$query = "SELECT fo.*, ip.company_name 
          FROM funding_opportunities fo
          JOIN industry_partners ip ON fo.partner_id = ip.partner_id
          WHERE fo.opportunity_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $opportunity_id);
$stmt->execute();
$result = $stmt->get_result();
$opportunity = $result->fetch_assoc();
$stmt->close();

if (!$opportunity) {
    echo "Funding opportunity not found. Opportunity ID: " . htmlspecialchars($opportunity_id);
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $proposal = mysqli_real_escape_string($conn, $_POST['proposal']);
    $budget = mysqli_real_escape_string($conn, $_POST['budget']);
    $timeline = mysqli_real_escape_string($conn, $_POST['timeline']);

    // Handle file upload
    if (isset($_FILES['proposal_pdf']) && $_FILES['proposal_pdf']['error'] == 0) {
        $file = $_FILES['proposal_pdf'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_type = $file['type'];

        // Validate file type (only allow PDF)
        $allowed_types = ['application/pdf'];
        if (!in_array($file_type, $allowed_types)) {
            echo "<script>alert('Only PDF files are allowed.'); window.history.back();</script>";
            exit();
        }

        // Validate file size (max 5MB)
        $max_size = 5 * 1024 * 1024; // 5MB
        if ($file_size > $max_size) {
            echo "<script>alert('File size must be less than 5MB.'); window.history.back();</script>";
            exit();
        }

        // Save the file to a directory
        $upload_dir = '../../uploads/proposals/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
        }

        $file_path = $upload_dir . uniqid() . '_' . basename($file_name);
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Insert into funding_applications table with file path
            $query = "INSERT INTO funding_applications (opportunity_id, user_id, proposal, budget, timeline, proposal_pdf, status) 
                      VALUES (?, ?, ?, ?, ?, ?, 'pending')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iissss", $opportunity_id, $user_id, $proposal, $budget, $timeline, $file_path);
            $stmt->execute();
            $stmt->close();

            echo "<script>alert('Application submitted successfully.'); window.location.href = 'view_funding_opportunities.php';</script>";
        } else {
            echo "<script>alert('Failed to upload file.'); window.history.back();</script>";
            exit();
        }
    } else {
        // Insert into funding_applications table without file path
        $query = "INSERT INTO funding_applications (opportunity_id, user_id, proposal, budget, timeline, status) 
                  VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iisss", $opportunity_id, $user_id, $proposal, $budget, $timeline);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Application submitted successfully.'); window.location.href = 'view_funding_opportunities.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Funding</title>
    <link rel="stylesheet" href="../../css/common_functionalities/apply_funding.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="../../php/dashboards/normal_dashboard.php">
                <img src="../../images/UI_LOGO.jpeg" alt="Research Collaboration">
            </a>
        </div>
        <h1>Apply for Funding</h1>
        <div class="logout-menu">
            <form action="../../php/profiles/logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    <div class="container">
        <h2>Apply for: <?php echo htmlspecialchars($opportunity['title']); ?></h2>
        <p><strong>Posted by:</strong> <?php echo htmlspecialchars($opportunity['company_name']); ?></p>
        <p><strong>Funding Amount:</strong> $<?php echo htmlspecialchars($opportunity['funding_amount']); ?></p>
        <p><strong>Deadline:</strong> <?php echo htmlspecialchars($opportunity['deadline']); ?></p>

        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="proposal">Proposal (Text):</label>
                <textarea id="proposal" name="proposal" rows="6"></textarea>
            </div>
            <div class="form-group">
                <label for="proposal_pdf">Proposal (PDF):</label>
                <input type="file" id="proposal_pdf" name="proposal_pdf" accept="application/pdf">
                <small>Upload a PDF file (max 5MB).</small>
            </div>
            <div class="form-group">
                <label for="budget">Budget (in USD):</label>
                <input type="number" id="budget" name="budget" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="timeline">Timeline:</label>
                <textarea id="timeline" name="timeline" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="apply-button">Submit Application</button>
            </div>
        </form>
    </div>

    <footer>
        <div class="footer-container">
            <p>&copy; <?php echo date("Y"); ?> University Research Collaboration Portal. All rights reserved.</p>
            <nav>
                <a href="../../php/common_functionalities/about.php">About</a>
                <a href="../../php/common_functionalities/contact.php">Contact</a>
                <a href="../../php/common_functionalities/privacy.php">Privacy Policy</a>
            </nav>
        </div>
    </footer>
</body>
</html>