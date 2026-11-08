<?php
// =============================================
// Admin Logout
// Destroys admin session and redirects
// =============================================
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit();
?>
