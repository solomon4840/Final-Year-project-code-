<?php
// Include the database connection
include '../../php/db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $middle_name = mysqli_real_escape_string($conn, $_POST['middle_name']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = mysqli_real_escape_string($conn, $_POST['role']);  // Get the selected role

    // Industry partner-specific fields
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name'] ?? '');
    $industry_sector = mysqli_real_escape_string($conn, $_POST['industry_sector'] ?? '');
    $website = mysqli_real_escape_string($conn, $_POST['website'] ?? '');
    $areas_of_interest = mysqli_real_escape_string($conn, $_POST['areas_of_interest'] ?? '');

    // Password validation
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit();
    }

    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        echo "<script>alert('Password must be at least 8 characters, contain one uppercase letter, one lowercase letter, one number, and one special character.'); window.history.back();</script>";
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists
    $check_email_query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($check_email_query);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered. Please use a different email.'); window.history.back();</script>";
        exit();
    }

    // Insert into the users table
    $insert_query = "INSERT INTO users (last_name, middle_name, first_name, email, phone_number, password, role) 
                     VALUES ('$last_name', '$middle_name', '$first_name', '$email', '$phone_number', '$hashed_password', '$role')";

    if ($conn->query($insert_query)) {
        $user_id = $conn->insert_id; // Get the ID of the newly inserted user

        // If the user is an industry partner, insert into the industry_partners table
        if ($role === 'Industry Partner') {
            $insert_partner_query = "INSERT INTO industry_partners (user_id, company_name, industry_sector, website, areas_of_interest) 
                                     VALUES ('$user_id', '$company_name', '$industry_sector', '$website', '$areas_of_interest')";
            if (!$conn->query($insert_partner_query)) {
                echo "<script>alert('Error saving industry partner details.'); window.history.back();</script>";
                exit();
            }
        }

        echo "<script>alert('Registration successful. You can now login.'); window.location.href = 'login.php';</script>";
    } else {
        echo "<script>alert('Error during registration.'); window.history.back();</script>";
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Research Portal</title>
    <link rel="stylesheet" href="../../css/profiles/index.css">
    <style>
        /* Additional CSS for the registration form */
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
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
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
        }

        .form-group small {
            display: block;
            margin-top: 5px;
            font-size: 12px;
            color: #666;
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

        #industry-partner-fields {
            display: none;
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

    <!-- Register Form -->
    <main>
        <section class="form-section">
            <h2>Register</h2>
            <form action="" method="post">
                <!-- General Fields -->
                <div class="form-group">
                    <label for="last-name">Last Name:</label>
                    <input type="text" id="last-name" name="last_name" placeholder="Enter your last name" required>
                </div>
                <div class="form-group">
                    <label for="middle-name">Middle Name:</label>
                    <input type="text" id="middle-name" name="middle_name" placeholder="Enter your middle name">
                </div>
                <div class="form-group">
                    <label for="first-name">First Name:</label>
                    <input type="text" id="first-name" name="first_name" placeholder="Enter your first name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="phone-number">Phone Number:</label>
                    <input type="tel" id="phone-number" name="phone_number" placeholder="Enter your phone number" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Create a password" required pattern="(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}">
                    <span class="toggle-password" onclick="togglePasswordVisibility('password')">👁️</span>
                    <small>Password must be at least 8 characters, contain one uppercase letter, one lowercase letter, one number, and one special character.</small>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirm Password:</label>
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm your password" required>
                    <span class="toggle-password" onclick="togglePasswordVisibility('confirm-password')">👁️</span>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select id="role" name="role" required onchange="toggleIndustryPartnerFields()">
                        <option value="Student">Student</option>
                        <option value="Researcher">Researcher</option>
                        <option value="Admin">Admin/Faculty</option>
                        <option value="Industry Partner">Industry Partner</option>
                    </select>
                </div>

                <!-- Industry Partner Specific Fields -->
                <div id="industry-partner-fields">
                    <div class="form-group">
                        <label for="company-name">Company Name:</label>
                        <input type="text" id="company-name" name="company_name" placeholder="Enter your company name">
                    </div>
                    <div class="form-group">
                        <label for="industry-sector">Industry Sector:</label>
                        <input type="text" id="industry-sector" name="industry_sector" placeholder="Enter your industry sector">
                    </div>
                    <div class="form-group">
                        <label for="website">Website:</label>
                        <input type="url" id="website" name="website" placeholder="Enter your company website">
                    </div>
                    <div class="form-group">
                        <label for="areas-of-interest">Areas of Interest:</label>
                        <textarea id="areas-of-interest" name="areas_of_interest" rows="4" placeholder="Describe your areas of interest"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit">Register</button>
                </div>
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </form>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 University Research Collaboration Portal</p>
    </footer>

    <script>
        // Toggle password visibility
        function togglePasswordVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === "password" ? "text" : "password";
        }

        // Toggle industry partner fields based on role selection
        function toggleIndustryPartnerFields() {
            const role = document.getElementById('role').value;
            const industryPartnerFields = document.getElementById('industry-partner-fields');
            if (role === 'Industry Partner') {
                industryPartnerFields.style.display = 'block';
            } else {
                industryPartnerFields.style.display = 'none';
            }
        }
    </script>
</body>
</html>