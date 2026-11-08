<?php
// =============================================
// Authentication Check
// Include this file at the top of any page
// that requires the user to be logged in.
// If not logged in, redirects to login page.
// =============================================

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in by checking session variable
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to login page
    header("Location: login.php");
    exit();
}
?>
