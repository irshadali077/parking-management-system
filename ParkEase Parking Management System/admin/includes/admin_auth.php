<?php
// =============================================
// Admin Session Guard
// Include at top of admin pages to ensure
// only admins can access them
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
?>
