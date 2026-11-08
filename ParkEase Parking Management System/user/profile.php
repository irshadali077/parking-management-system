<?php
// =============================================
// User Profile Page
// Allows users to update their name, phone, and password
// =============================================
require_once '../includes/auth_check.php';
require_once '../config/db.php';

$error = "";
$success = "";

// Get current user data
$stmt = $conn->prepare("SELECT fullname, email, phone, student_id FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($fullname) || empty($phone)) {
        $error = "Name and phone number are required!";
    } else {
        $stmt = $conn->prepare("UPDATE users SET fullname = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("ssi", $fullname, $phone, $_SESSION['user_id']);

        if ($stmt->execute()) {
            $success = "Profile details updated successfully!";
            $_SESSION['user_name'] = $fullname;
            $user['fullname'] = $fullname;
            $user['phone'] = $phone;
        } else {
            $error = "Failed to update profile details.";
        }
        $stmt->close();
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_new_password'] ?? '';

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All password fields are required!";
    } elseif (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters!";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match!";
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if (password_verify($current_password, $row['password'])) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed, $_SESSION['user_id']);

            if ($stmt->execute()) {
                $success = "Security password updated successfully!";
            } else {
                $error = "Failed to update password.";
            }
            $stmt->close();
        } else {
            $error = "Current password is incorrect!";
        }
    }
}

include '../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            Account & Security Settings
        </h1>
        <p class="page-subtitle">Manage your verified driver credentials and access tokens</p>
    </div>
</div>

<!-- Messages -->
<?php if ($error): ?>
    <div class="alert alert-error">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <div><?php echo htmlspecialchars($error); ?></div>
    </div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <div><?php echo htmlspecialchars($success); ?></div>
    </div>
<?php endif; ?>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:24px;">
    <!-- Profile Info Card -->
    <div class="card">
        <h3 class="card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
            Driver Information
        </h3>
        <p style="color:var(--text-muted); font-size:0.85rem; margin-bottom:20px;">Personal contact details for vehicle parking permits</p>

        <form method="POST" action="">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </span>
                    <input type="text" id="fullname" name="fullname" 
                           value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="student_id">Campus ID (Verified • Read-only)</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"></rect><line x1="7" y1="8" x2="17" y2="8"></line><line x1="7" y1="12" x2="17" y2="12"></line></svg>
                    </span>
                    <input type="text" id="student_id" value="<?php echo htmlspecialchars($user['student_id']); ?>" disabled style="opacity:0.65; cursor:not-allowed; font-family:var(--font-mono);">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address (Read-only)</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </span>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="opacity:0.65; cursor:not-allowed;">
                </div>
            </div>

            <div class="form-group">
                <label for="phone">Contact Phone Number</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </span>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>
            </div>

            <button type="submit" name="update_profile" class="btn btn-primary" style="margin-top:8px;">
                Save Profile Changes
            </button>
        </form>
    </div>

    <!-- Change Password Card -->
    <div class="card">
        <h3 class="card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            Security & Authentication
        </h3>
        <p style="color:var(--text-muted); font-size:0.85rem; margin-bottom:20px;">Update your password to keep your permits secure</p>

        <form method="POST" action="">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </span>
                    <input type="password" id="current_password" name="current_password" 
                           placeholder="Enter current password" required autocomplete="current-password">
                    <button type="button" class="toggle-pwd" onclick="togglePasswordVisibility('current_password', this)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="new_password">New Secure Password</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </span>
                    <input type="password" id="new_password" name="new_password" 
                           placeholder="Minimum 6 characters" required autocomplete="new-password">
                    <button type="button" class="toggle-pwd" onclick="togglePasswordVisibility('new_password', this)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_new_password">Confirm New Password</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </span>
                    <input type="password" id="confirm_new_password" name="confirm_new_password" 
                           placeholder="Re-enter new password" required autocomplete="new-password">
                    <button type="button" class="toggle-pwd" onclick="togglePasswordVisibility('confirm_new_password', this)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>
            </div>

            <button type="submit" name="change_password" class="btn btn-primary" style="margin-top:8px;">
                Update Security Password
            </button>
        </form>
    </div>
</div>

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

<?php include '../includes/footer.php'; ?>
