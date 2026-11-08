<?php
// =============================================
// Admin Login Page
// Separate secure gateway for administrators
// =============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If admin is already logged in, redirect
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once '../config/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please fill in all administrator credentials!";
    } else {
        // Find admin by username
        $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows == 1) {
                $admin = $result->fetch_assoc();

                // Verify admin password
                if (password_verify($password, $admin['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];

                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Invalid administrator password credentials!";
                }
            } else {
                $error = "Administrator account not found in system!";
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
    <title>Admin Security Gateway - ParkEase</title>
    <link rel="icon" type="image/svg+xml" href="../assets/images/logo.svg">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <div class="form-container">
        <div class="form-card">
            <!-- Security Shield Badge -->
            <div class="form-header-badge">
                <div style="width:52px; height:52px; border-radius:14px; background:linear-gradient(135deg, #10B981, #059669); display:flex; align-items:center; justify-content:center; color:#fff; margin:0 auto 14px; box-shadow:0 4px 18px rgba(16, 185, 129, 0.3);">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h2>Staff & Security Portal</h2>
                <p class="form-subtitle">Elevated privilege management for parking operations</p>
            </div>

            <!-- Demo Quick Fill Pill -->
            <div class="demo-chip-bar">
                <span style="display:flex; align-items:center; gap:6px; color:var(--text-secondary);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                    Master Admin:
                </span>
                <button type="button" class="demo-chip" style="background:#10B981;" onclick="fillDemoAdmin()">Auto-Fill</button>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <div><?php echo htmlspecialchars($error); ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="adminForm">
                <div class="form-group">
                    <label for="username">Administrator Username</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </span>
                        <input type="text" id="username" name="username" placeholder="admin" 
                               value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Master Password</label>
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

                <button type="submit" class="btn btn-primary" style="width:100%; margin-top:12px; background:#10B981;">
                    Authenticate to Console →
                </button>
            </form>

            <div style="text-align:center; margin-top:24px; padding-top:16px; border-top:1px solid var(--border-subtle); font-size:0.88rem;">
                <p><a href="../user/login.php" style="color:var(--text-muted); text-decoration:none;">← Return to Driver Terminal</a></p>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script>
    function fillDemoAdmin() {
        document.getElementById('username').value = 'admin';
        document.getElementById('password').value = 'admin123';
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
