<?php
// =============================================
// User Common Header
// Left Sidebar Navigation + App Topbar
// Clean Minimalist Black & White Theme
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$user_name = $_SESSION['user_name'] ?? 'Driver';
$user_initial = strtoupper(substr($user_name, 0, 1));

// Page Titles for Topbar
$page_titles = [
    'dashboard.php' => 'Dashboard Overview',
    'book_slot.php' => 'Reserve Parking Bay',
    'history.php' => 'Permit & Trip History',
    'profile.php' => 'Account Settings'
];
$topbar_title = $page_titles[$current_page] ?? 'Driver Portal';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ParkEase - Smart Campus Parking Management Platform">
    <title>ParkEase - <?php echo htmlspecialchars($topbar_title); ?></title>
    <link rel="icon" type="image/svg+xml" href="../assets/images/logo.svg">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="app-layout">
    <!-- Left Navigation Sidebar -->
    <aside class="app-sidebar" id="appSidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="sidebar-logo">
                <div class="logo-badge-icon">P</div>
                <span>ParkEase</span>
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Terminal Menu</div>
            <a href="dashboard.php" class="sidebar-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <span>Dashboard</span>
            </a>
            <a href="book_slot.php" class="sidebar-link <?php echo ($current_page == 'book_slot.php') ? 'active' : ''; ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M9 17V7h4a3 3 0 0 1 0 6H9"></path></svg>
                <span>Reserve Bay</span>
            </a>
            <a href="history.php" class="sidebar-link <?php echo ($current_page == 'history.php') ? 'active' : ''; ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                <span>Permit History</span>
            </a>

            <div class="nav-section-title">Preferences</div>
            <a href="profile.php" class="sidebar-link <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <span>Account & Security</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="user-avatar-initial"><?php echo htmlspecialchars($user_initial); ?></div>
                <div style="overflow:hidden;">
                    <div class="user-meta-name"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="user-meta-role">Campus Driver</div>
                </div>
            </div>
            <a href="logout.php" class="btn-sidebar-logout" title="Sign Out">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            </a>
        </div>
    </aside>

    <!-- Main Viewport -->
    <div class="app-main">
        <!-- Topbar -->
        <header class="app-topbar">
            <div class="topbar-left">
                <button class="sidebar-mobile-toggle" onclick="toggleSidebar()" aria-label="Toggle Navigation">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
                <div class="topbar-title"><?php echo htmlspecialchars($topbar_title); ?></div>
            </div>

            <div class="topbar-right">
                <div class="status-pill-minimal">
                    <span class="status-indicator-dot"></span>
                    <span>Live Telemetry Online</span>
                </div>
            </div>
        </header>

        <!-- Page Body Content Area -->
        <main class="page-content">
