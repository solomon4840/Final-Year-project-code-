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

// Fetch industry partner details using user_id
$query = "SELECT * FROM industry_partners WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$partner = $result->fetch_assoc();
$stmt->close();

if (!$partner) {
    echo "Industry partner profile not found.";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
    $industry_sector = mysqli_real_escape_string($conn, $_POST['industry_sector']);
    $website = mysqli_real_escape_string($conn, $_POST['website']);
    $areas_of_interest = mysqli_real_escape_string($conn, $_POST['areas_of_interest']);

    // Update industry partner details
    $query = "UPDATE industry_partners 
              SET company_name = ?, industry_sector = ?, website = ?, areas_of_interest = ? 
              WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $company_name, $industry_sector, $website, $areas_of_interest, $user_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Profile updated successfully.'); window.location.href = 'industry_partners_dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Industry Partner</title>
    <link rel="stylesheet" href="../../css/dashboards/industry_partners.css">
</head>
<body>
    <header>
        <h1>Edit Profile</h1>
        <div class="logout-menu">
            <form action="../../php/profiles/logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    <div class="container">
        <h2>Edit Your Profile</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="company-name">Company Name:</label>
                <input type="text" id="company-name" name="company_name" value="<?php echo htmlspecialchars($partner['company_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="industry-sector">Industry Sector:</label>
                <input type="text" id="industry-sector" name="industry_sector" value="<?php echo htmlspecialchars($partner['industry_sector']); ?>" required>
            </div>
            <div class="form-group">
                <label for="website">Website:</label>
                <input type="url" id="website" name="website" value="<?php echo htmlspecialchars($partner['website']); ?>">
            </div>
            <div class="form-group">
                <label for="areas-of-interest">Areas of Interest:</label>
                <textarea id="areas-of-interest" name="areas_of_interest" rows="4"><?php echo htmlspecialchars($partner['areas_of_interest']); ?></textarea>
            </div>
            <div class="form-group">
                <button type="submit">Update Profile</button>
            </div>
        </form>
    </div>
</body>
</html>