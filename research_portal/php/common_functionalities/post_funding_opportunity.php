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
$query = "SELECT id FROM users WHERE email = ?";
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

// Fetch partner_id from industry_partners
$query = "SELECT partner_id FROM industry_partners WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$partner = $result->fetch_assoc();
$stmt->close();

if (!$partner) {
    echo "Error: No industry partner profile found for this user.";
    exit();
}

$partner_id = $partner['partner_id']; // Correct foreign key value

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $funding_amount = mysqli_real_escape_string($conn, $_POST['funding_amount']);
    $eligibility_criteria = mysqli_real_escape_string($conn, $_POST['eligibility_criteria']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);

    // Insert into funding_opportunities table with correct partner_id
    $query = "INSERT INTO funding_opportunities (partner_id, title, description, funding_amount, eligibility_criteria, deadline) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $partner_id, $title, $description, $funding_amount, $eligibility_criteria, $deadline);

    if ($stmt->execute()) {
        echo "<script>alert('Funding opportunity posted successfully.'); window.location.href = 'industry_partners_dashboard.php';</script>";
    } else {
        echo "Error posting funding opportunity: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Funding Opportunity</title>
    <link rel="stylesheet" href="../../css/common_functionalities/funding_opportunities.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="normal_dashboard.php">
                <img src="../../images/UI_LOGO.jpeg" alt="Research Collaboration">
            </a>
        </div>
        <h1>Post Funding Opportunity</h1>
        <div class="logout-menu">
            <form action="../../php/profiles/logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    <div class="container">
        <h2>Post a New Funding Opportunity</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="funding-amount">Funding Amount:</label>
                <input type="number" id="funding-amount" name="funding_amount" required>
            </div>
            <div class="form-group">
                <label for="eligibility-criteria">Eligibility Criteria:</label>
                <textarea id="eligibility-criteria" name="eligibility_criteria" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="deadline">Deadline:</label>
                <input type="date" id="deadline" name="deadline" required>
            </div>
            <div class="form-group">
                <button type="submit">Post Opportunity</button>
            </div>
        </form>
    </div>

    <footer>
        <p>&copy; 2025 University Research Collaboration. All rights reserved.</p>
    </footer>
</body>
</html>
