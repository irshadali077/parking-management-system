<?php
// =============================================
// Admin Common Header
// Left Sidebar Navigation + Admin Topbar
// Clean Minimalist Black & White Theme
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$admin_name = $_SESSION['admin_username'] ?? 'Admin';
$admin_initial = strtoupper(substr($admin_name, 0, 1));

// Page Titles for Topbar
$page_titles = [
    'dashboard.php' => 'Operations & Analytics',
    'users.php' => 'Registered Drivers',
    'bookings.php' => 'Master Bookings Log',
    'slots.php' => 'Parking Bay Inventory'
];
$topbar_title = $page_titles[$current_page] ?? 'Admin Console';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ParkEase Admin - <?php echo htmlspecialchars($topbar_title); ?></title>
    <link rel="icon" type="image/svg+xml" href="../assets/images/logo.svg">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="app-layout">
    <!-- Left Navigation Sidebar -->
    <aside class="app-sidebar" id="appSidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="sidebar-logo">
                <div class="logo-badge-icon" style="background:#09090B;">P</div>
                <span>ParkEase <small style="font-size:0.7rem; background:#F4F4F5; border:1px solid #E4E4E7; padding:2px 6px; border-radius:4px; font-weight:700;">ADMIN</small></span>
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Facility Management</div>
            <a href="dashboard.php" class="sidebar-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                <span>Analytics</span>
            </a>
            <a href="users.php" class="sidebar-link <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                <span>Drivers Directory</span>
            </a>
            <a href="bookings.php" class="sidebar-link <?php echo ($current_page == 'bookings.php') ? 'active' : ''; ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                <span>Bookings Ledger</span>
            </a>
            <a href="slots.php" class="sidebar-link <?php echo ($current_page == 'slots.php') ? 'active' : ''; ?>">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M9 17V7h4a3 3 0 0 1 0 6H9"></path></svg>
                <span>Bays Inventory</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="user-avatar-initial" style="background:#09090B;"><?php echo htmlspecialchars($admin_initial); ?></div>
                <div style="overflow:hidden;">
                    <div class="user-meta-name"><?php echo htmlspecialchars($admin_name); ?></div>
                    <div class="user-meta-role">Facility Admin</div>
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
                    <span>Admin Clearance Level 1</span>
                </div>
            </div>
        </header>

        <!-- Page Body Content Area -->
        <main class="page-content">
