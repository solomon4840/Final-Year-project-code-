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

// Fetch user data
$userQuery = "SELECT * FROM users WHERE email = '$userEmail'";
$userResult = $conn->query($userQuery);
$user = $userResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Research Portal</title>
    <link rel="stylesheet" href="../../css/dashboards/profile.css">
</head>
<body>
    <header>
        <h1>University of Ibadan Research Portal - Profile</h1>
        <div class="logout-menu">
            <form action="../../php/profiles/logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </header>

    <h2>Profile</h2>

    <div class="profile-container">
        <!-- Profile Picture Section -->
        <div class="profile-picture">
            <h3>Profile Picture</h3>
            <?php if ($user['profile_picture']) : ?>
                <img src="../../uploads/profile_pictures/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
            <?php else : ?>
                <p>No profile picture uploaded.</p>
            <?php endif; ?>
            <form action="../../php/common_functionalities/upload_profile_picture.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_picture" accept="image/*" required>
                <button type="submit">Upload Picture</button>
            </form>
        </div>

        <!-- Personal Information Section -->
        <div class="personal-info">
            <h3>Personal Information</h3>
            <form action="../../php/common_functionalities/update_profile.php" method="POST">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

                <label for="middle_name">Middle Name:</label>
                <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($user['middle_name']); ?>">

                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required readonly>

                <label for="phone_number">Phone Number:</label>
                <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>

                <label for="role">Role:</label>
                <input type="text" id="role" name="role" value="<?php echo htmlspecialchars($user['role']); ?>" required readonly>

                <button type="submit">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>