<?php
// =============================================
// User Signup Page
// Handles registration with validation
// =============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Include database connection
require_once '../config/db.php';

$error = "";
$success = "";

// Process signup form when submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $security_question = trim($_POST['security_question'] ?? '');
    $security_answer = trim(strtolower($_POST['security_answer'] ?? ''));

    // --- Validation ---
    if (empty($fullname) || empty($email) || empty($phone) || empty($student_id) || empty($password) || empty($confirm_password) || empty($security_question) || empty($security_answer)) {
        $error = "All fields are required to issue your driver credentials!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid campus email address!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email already exists
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();

        if ($check_email->num_rows > 0) {
            $error = "This email is already registered in the campus portal!";
        } else {
            // Check if student ID already exists
            $check_sid = $conn->prepare("SELECT id FROM users WHERE student_id = ?");
            $check_sid->bind_param("s", $student_id);
            $check_sid->execute();
            $check_sid->store_result();

            if ($check_sid->num_rows > 0) {
                $error = "This Campus ID is already associated with an account!";
            } else {
                // Hash the password and security answer
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $hashed_answer = password_hash($security_answer, PASSWORD_DEFAULT);

                // Insert new user into database
                $stmt = $conn->prepare("INSERT INTO users (fullname, email, phone, student_id, password, security_question, security_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssss", $fullname, $email, $phone, $student_id, $hashed_password, $security_question, $hashed_answer);

                if ($stmt->execute()) {
                    $success = "Driver account registered successfully! You can now sign in.";
                    $fullname = $email = $phone = $student_id = "";
                } else {
                    $error = "Registration failed! Please try again.";
                }
                $stmt->close();
            }
            $check_sid->close();
        }
        $check_email->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Registration - ParkEase</title>
    <link rel="icon" type="image/svg+xml" href="../assets/images/logo.svg">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }
        @media (min-width: 640px) {
            .form-grid-2 { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>

    <div class="form-container">
        <div class="form-card form-card-wide">
            <!-- Brand Badge -->
            <div class="form-header-badge">
                <div style="width:48px; height:48px; border-radius:12px; background:linear-gradient(135deg, var(--primary), #1D4ED8); display:flex; align-items:center; justify-content:center; color:#fff; margin:0 auto 14px; box-shadow:0 4px 16px var(--primary-glow);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                </div>
                <h2>Register for Campus Parking</h2>
                <p class="form-subtitle">Create your driver account to unlock digital passes and smart bays</p>
            </div>

            <!-- Messages -->
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <div><?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    <div>
                        <?php echo htmlspecialchars($success); ?> 
                        <a href="login.php" style="color:#ffffff; font-weight:700; text-decoration:underline; margin-left:8px;">Sign In Now →</a>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-grid-2">
                    <!-- Left Column: Personal Information -->
                    <div>
                        <div class="form-group">
                            <label for="fullname">Driver Full Name</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                </span>
                                <input type="text" id="fullname" name="fullname" placeholder="Alex Morgan" 
                                       value="<?php echo isset($fullname) ? htmlspecialchars($fullname) : ''; ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="student_id">Student / Staff ID</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"></rect><line x1="7" y1="8" x2="17" y2="8"></line><line x1="7" y1="12" x2="17" y2="12"></line></svg>
                                </span>
                                <input type="text" id="student_id" name="student_id" placeholder="STU-2026-081" 
                                       value="<?php echo isset($student_id) ? htmlspecialchars($student_id) : ''; ?>" required style="font-family:var(--font-mono);">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Campus Email</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                </span>
                                <input type="email" id="email" name="email" placeholder="alex@campus.edu" 
                                       value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                </span>
                                <input type="tel" id="phone" name="phone" placeholder="+91 98765 43210" 
                                       value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" required>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Security Credentials -->
                    <div>
                        <div class="form-group">
                            <label for="password">Create Password</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                </span>
                                <input type="password" id="password" name="password" placeholder="Min. 6 characters" required oninput="checkStrength(this.value)">
                                <button type="button" class="toggle-pwd" onclick="togglePasswordVisibility('password', this)">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                            </div>
                            <div id="strengthMeter" style="height:3px; background:var(--border-subtle); border-radius:4px; margin-top:6px; overflow:hidden;">
                                <div id="strengthBar" style="width:0; height:100%; transition:0.3s ease;"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                </span>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
                                <button type="button" class="toggle-pwd" onclick="togglePasswordVisibility('confirm_password', this)">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="security_question">Account Recovery Question</label>
                            <select id="security_question" name="security_question" required>
                                <option value="">-- Choose recovery question --</option>
                                <option value="What is your pet's name?">What is your pet's name?</option>
                                <option value="What is your favorite color?">What is your favorite color?</option>
                                <option value="What city were you born in?">What city were you born in?</option>
                                <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                                <option value="What is your first car model?">What is your first car model?</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="security_answer">Confidential Recovery Answer</label>
                            <div class="input-wrap">
                                <span class="input-icon">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                </span>
                                <input type="text" id="security_answer" name="security_answer" placeholder="Your confidential answer" required>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; margin-top:16px;">
                    Register Driver Permit Account →
                </button>
            </form>

            <div style="text-align:center; margin-top:24px; padding-top:16px; border-top:1px solid var(--border-subtle); font-size:0.88rem;">
                <p>Already registered? <a href="login.php" style="color:var(--primary-hover); font-weight:700; text-decoration:none;">Sign in to your account</a></p>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
    function togglePasswordVisibility(fieldId, btn) {
        const input = document.getElementById(fieldId);
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }
    function checkStrength(pwd) {
        const bar = document.getElementById('strengthBar');
        if (!pwd) {
            bar.style.width = '0%';
            return;
        }
        let score = 0;
        if (pwd.length >= 6) score += 33;
        if (pwd.length >= 10 || /[A-Z]/.test(pwd)) score += 33;
        if (/[0-9]/.test(pwd) && /[^A-Za-z0-9]/.test(pwd)) score += 34;

        bar.style.width = score + '%';
        if (score <= 33) bar.style.backgroundColor = 'var(--danger)';
        else if (score <= 66) bar.style.backgroundColor = 'var(--warning)';
        else bar.style.backgroundColor = 'var(--success)';
    }
    </script>
</body>
</html>
