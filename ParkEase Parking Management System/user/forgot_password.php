<?php
// =============================================
// Forgot Password Page
// Resets password using security question
// =============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php';

$error = "";
$success = "";
$step = 1; // Step 1: Enter email, Step 2: Answer question, Step 3: New password
$security_question = "";
$user_email = "";

// Step 1: User enters email to find their account
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['find_account'])) {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $error = "Please enter your registered campus email address!";
    } else {
        $stmt = $conn->prepare("SELECT email, security_question FROM users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows == 1) {
                $user = $result->fetch_assoc();
                $security_question = $user['security_question'];
                $user_email = $user['email'];
                $step = 2;
            } else {
                $error = "No registered driver account found with that email address!";
            }
            $stmt->close();
        }
    }
}

// Step 2: User answers the security question
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_answer'])) {
    $email = trim($_POST['email'] ?? '');
    $answer = trim(strtolower($_POST['security_answer'] ?? ''));

    $stmt = $conn->prepare("SELECT security_question, security_answer FROM users WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (password_verify($answer, $user['security_answer'])) {
                $_SESSION['reset_allowed_email'] = $email;
                $user_email = $email;
                $step = 3;
            } else {
                $error = "Incorrect security answer! Please try again.";
                $security_question = $user['security_question'];
                $user_email = $email;
                $step = 2;
            }
        }
        $stmt->close();
    }
}

// Step 3: User sets a new password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Verify session guard
    if (empty($_SESSION['reset_allowed_email']) || $_SESSION['reset_allowed_email'] !== $email) {
        $error = "Unauthorized reset attempt. Please start from Step 1.";
        $step = 1;
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters!";
        $user_email = $email;
        $step = 3;
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match!";
        $user_email = $email;
        $step = 3;
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $hashed, $email);

            if ($stmt->execute()) {
                unset($_SESSION['reset_allowed_email']);
                $success = "Password reset successfully! You can now sign in with your new password.";
                $step = 1;
            } else {
                $error = "Something went wrong. Please try again.";
                $step = 3;
                $user_email = $email;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Recovery - ParkEase</title>
    <link rel="icon" type="image/svg+xml" href="../assets/images/logo.svg">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .stepper-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            background: var(--bg-surface-elevated);
            padding: 8px 16px;
            border-radius: var(--radius-pill);
            border: 1px solid var(--border-subtle);
        }
        .step-node {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 600;
        }
        .step-node.active {
            color: var(--primary-hover);
        }
        .step-num {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }
        .step-node.active .step-num {
            background: var(--primary);
            color: #fff;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <div class="form-card">
            <div class="form-header-badge">
                <div style="width:48px; height:48px; border-radius:12px; background:linear-gradient(135deg, var(--primary), #1D4ED8); display:flex; align-items:center; justify-content:center; color:#fff; margin:0 auto 14px; box-shadow:0 4px 16px var(--primary-glow);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                </div>
                <h2>Driver Account Recovery</h2>
                <p class="form-subtitle">Reset your parking pass password via security verification</p>
            </div>

            <!-- Stepper Progress Indicator -->
            <div class="stepper-nav">
                <div class="step-node <?php echo ($step >= 1) ? 'active' : ''; ?>">
                    <span class="step-num">1</span> Identify
                </div>
                <div class="step-node <?php echo ($step >= 2) ? 'active' : ''; ?>">
                    <span class="step-num">2</span> Verify
                </div>
                <div class="step-node <?php echo ($step >= 3) ? 'active' : ''; ?>">
                    <span class="step-num">3</span> Reset
                </div>
            </div>

            <!-- Alerts -->
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
                        <a href="login.php" style="color:#fff; font-weight:700; text-decoration:underline; margin-left:8px;">Sign In Now →</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Step 1: Find Account -->
            <?php if ($step == 1): ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Registered Campus Email Address</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </span>
                            <input type="email" id="email" name="email" placeholder="alex@campus.edu" required autocomplete="email">
                        </div>
                    </div>
                    <button type="submit" name="find_account" class="btn btn-primary" style="width:100%; margin-top:12px;">
                        Find Account →
                    </button>
                </form>
            <?php endif; ?>

            <!-- Step 2: Answer Security Question -->
            <?php if ($step == 2): ?>
                <form method="POST" action="">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($user_email); ?>">
                    <div class="form-group">
                        <label>Your Security Question</label>
                        <input type="text" value="<?php echo htmlspecialchars($security_question); ?>" disabled style="opacity:0.85; font-weight:600; background:var(--bg-surface-elevated); border:1px solid var(--border-medium); color:var(--text-primary); padding:10px 14px; border-radius:var(--radius-sm); width:100%;">
                    </div>
                    <div class="form-group">
                        <label for="security_answer">Your Confidential Answer</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            </span>
                            <input type="text" id="security_answer" name="security_answer" placeholder="Enter answer given during signup" required autofocus>
                        </div>
                    </div>
                    <button type="submit" name="verify_answer" class="btn btn-primary" style="width:100%; margin-top:12px;">
                        Verify Identity →
                    </button>
                </form>
            <?php endif; ?>

            <!-- Step 3: Set New Password -->
            <?php if ($step == 3): ?>
                <form method="POST" action="">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($user_email); ?>">
                    <div class="form-group">
                        <label for="new_password">New Secure Password</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </span>
                            <input type="password" id="new_password" name="new_password" placeholder="Min. 6 characters" required autocomplete="new-password">
                            <button type="button" class="toggle-pwd" onclick="togglePasswordVisibility('new_password', this)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </span>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required autocomplete="new-password">
                            <button type="button" class="toggle-pwd" onclick="togglePasswordVisibility('confirm_password', this)">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            </button>
                        </div>
                    </div>
                    <button type="submit" name="reset_password" class="btn btn-primary" style="width:100%; margin-top:12px;">
                        Set New Password →
                    </button>
                </form>
            <?php endif; ?>

            <div style="text-align:center; margin-top:24px; padding-top:16px; border-top:1px solid var(--border-subtle); font-size:0.88rem;">
                <p><a href="login.php" style="color:var(--text-muted); text-decoration:none;">← Return to Sign In</a></p>
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
    </script>
</body>
</html>
