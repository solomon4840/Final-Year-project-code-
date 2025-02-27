<?php
// Include database connection file
include '../db.php';

// Initialize variables for error and success messages
$error = "";
$success = "";

// Start session to manage login state
session_start();

// Redirect logged-in users to the dashboard
if (isset($_SESSION['email'])) {
    header("Location: ../../php/dashboards/normal_dashboard.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Validate email and password
    if (!empty($email) && !empty($password)) {
        // Query to fetch user details
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($user_id, $hashed_password, $role);
        $stmt->fetch();

        // Verify password
        if ($hashed_password && password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;

            $success = "Login successful! Redirecting to dashboard...";

            // Redirect to dashboard after a short delay
            echo "<script>
                    setTimeout(function(){
                        window.location.href = '../../php/dashboards/normal_dashboard.php';
                    }, 2000); // 2 seconds delay
                </script>";
        } else {
            $error = "Invalid email or password.";
        }

        $stmt->close();
    } else {
        $error = "Please fill in all fields.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Research Portal</title>
    <link rel="stylesheet" href="../../css/profiles/login.css">
    <style>
        /* Additional CSS for the login form */
        .form-section {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-section h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #003366;
        }

        .form-group {
            position: relative;
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            padding-right: 40px; /* Add padding to the right for the eye icon */
        }

        .form-group input:focus {
            border-color: #003366;
            outline: none;
        }

        .form-group .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #003366;
            font-size: 18px;
            background: none;
            border: none;
            padding: 0;
        }

        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #003366;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #002244;
        }

        .form-section p {
            text-align: center;
            margin-top: 15px;
        }

        .form-section a {
            color: #003366;
            text-decoration: none;
        }

        .form-section a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <a href="../../html/profiles/index.html">
                <img src="../../images/UI_LOGO.jpeg" alt="Research Collaboration">
            </a>
        </div>
        <h1>University of Ibadan Research Portal</h1>
    </header>

    <!-- Login Form -->
    <main>
        <section class="form-section">
            <h2>Login</h2>
            
            <!-- Display error or success messages -->
            <?php if (!empty($error)) : ?>
                <div class="message error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (!empty($success)) : ?>
                <div class="message success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <span class="toggle-password" onclick="togglePasswordVisibility('password')">👁️</span>
                </div>
                <div class="form-group">
                    <button type="submit">Login</button>
                </div>
                <p>Don't have an account? <a href="../../php/profiles/register.php">Register here</a></p>
            </form>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 University Research Collaboration Portal</p>
    </footer>

    <script>
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>