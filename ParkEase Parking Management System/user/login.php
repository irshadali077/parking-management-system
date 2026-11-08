<?php
// =============================================
// User Login Page
// Handles email/password authentication
// Uses PHP sessions to maintain login state
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

// Process login form when submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($email) || empty($password)) {
        $error = "Please enter both campus email and password!";
    } else {
        // Find user by email using prepared statement
        $stmt = $conn->prepare("SELECT id, fullname, email, password FROM users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows == 1) {
                $user = $result->fetch_assoc();

                // Verify password against the hashed version
                if (password_verify($password, $user['password'])) {
                    session_regenerate_id(true); // Prevent session fixation
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['fullname'];
                    $_SESSION['user_email'] = $user['email'];

                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Invalid password. Please verify credentials or reset password.";
                }
            } else {
                $error = "No driver account found with this email!";
            }
            $stmt->close();
        } else {
            $error = "Database query error. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Sign In - ParkEase Mobility</title>
    <link rel="icon" type="image/svg+xml" href="../assets/images/logo.svg">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <div class="form-container">
        <div class="form-card">
            <!-- Brand Badge -->
            <div class="form-header-badge">
                <div style="width:48px; height:48px; border-radius:12px; background:linear-gradient(135deg, var(--primary), #1D4ED8); display:flex; align-items:center; justify-content:center; color:#fff; margin:0 auto 14px; box-shadow:0 4px 16px var(--primary-glow);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="4"/><path d="M9 17V7h4a3 3 0 0 1 0 6H9"/></svg>
                </div>
                <h2>Driver Portal Sign In</h2>
                <p class="form-subtitle">Access your campus parking pass & live bay terminal</p>
            </div>

            <!-- Demo Quick Fill Pill -->
            <div class="demo-chip-bar">
                <span style="display:flex; align-items:center; gap:6px; color:var(--text-secondary);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                    Student Demo:
                </span>
                <button type="button" class="demo-chip" onclick="fillDemoUser()">Auto-Fill</button>
            </div>

            <!-- Error Notification -->
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <div><?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="loginForm">
                <!-- Email Input with SVG Icon -->
                <div class="form-group">
                    <label for="email">Campus Email Address</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        </span>
                        <input type="email" id="email" name="email" placeholder="alex@campus.edu" 
                               value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required autocomplete="email">
                    </div>
                </div>

                <!-- Password Input with SVG Icon & Eye Toggle -->
                <div class="form-group">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                        <label for="password" style="margin-bottom:0;">Password</label>
                        <a href="forgot_password.php" style="font-size:0.8rem; font-weight:600; color:var(--primary-hover); text-decoration:none;">Forgot?</a>
                    </div>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        </span>
                        <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="toggle-pwd" onclick="togglePasswordVisibility('password', this)" title="Show/Hide Password">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; margin-top:10px;">
                    Sign In to Portal →
                </button>
            </form>

            <div style="text-align:center; margin-top:24px; padding-top:16px; border-top:1px solid var(--border-subtle); font-size:0.88rem;">
                <p>New to campus parking? <a href="signup.php" style="color:var(--primary-hover); font-weight:700; text-decoration:none;">Create driver account</a></p>
                <p style="margin-top:10px;">
                    <a href="../admin/login.php" style="color:var(--text-muted); font-size:0.82rem; text-decoration:none;">
                        🔒 Staff / Security Gate Login
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
    function fillDemoUser() {
        document.getElementById('email').value = 'user@test.com';
        document.getElementById('password').value = 'user123';
    }
    function togglePasswordVisibility(fieldId, btn) {
        const input = document.getElementById(fieldId);
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }
    </script>
</body>
</html>
