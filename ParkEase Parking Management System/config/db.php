<?php
// =============================================
// Database Connection Configuration (Auto-Healing & Multi-Port)
// Automatically connects to XAMPP MariaDB/MySQL on port 3306 or 3307
// Includes self-healing database initialization and friendly offline state
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Disable raw uncaught driver exceptions so we can recover gracefully
mysqli_report(MYSQLI_REPORT_OFF);

$candidate_ports = [3306, 3307];
$db_host = "127.0.0.1";
$db_user = "root";
$db_pass = "";
$db_name = "parking_db";

$conn = false;
$active_port = 3306;

// Attempt 1: Connect directly to parking_db on candidate ports
foreach ($candidate_ports as $p) {
    $conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, $p);
    if ($conn && !mysqli_connect_errno()) {
        $active_port = $p;
        break;
    }
}

// Attempt 2: If parking_db doesn't exist, connect to server and initialize it automatically
if (!$conn) {
    foreach ($candidate_ports as $p) {
        $server_conn = @mysqli_connect($db_host, $db_user, $db_pass, "", $p);
        if ($server_conn && !mysqli_connect_errno()) {
            @mysqli_query($server_conn, "CREATE DATABASE IF NOT EXISTS parking_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            mysqli_close($server_conn);
            
            // Connect to newly created database
            $conn = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, $p);
            if ($conn && !mysqli_connect_errno()) {
                $active_port = $p;
                // Auto-seed schema from parking_db.sql if empty
                $tbl_check = @mysqli_query($conn, "SHOW TABLES LIKE 'slots'");
                if ($tbl_check && mysqli_num_rows($tbl_check) == 0) {
                    $sql_file = __DIR__ . '/../database/parking_db.sql';
                    if (file_exists($sql_file)) {
                        $sql_content = file_get_contents($sql_file);
                        @mysqli_multi_query($conn, $sql_content);
                        do {
                            if ($res = @mysqli_store_result($conn)) {
                                mysqli_free_result($res);
                            }
                        } while (@mysqli_next_result($conn));
                    }
                }
                break;
            }
        }
    }
}

// If MySQL is completely stopped/offline, show a clean black & white guide
if (!$conn || mysqli_connect_errno()) {
    http_response_code(503);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Offline - ParkEase</title>
        <link rel="stylesheet" href="../assets/css/style.css">
        <style>
            body {
                background: #FAFAFA !important;
                color: #09090B !important;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                min-height: 100vh !important;
                margin: 0 !important;
                padding: 24px !important;
            }
            .db-card {
                background: #FFFFFF;
                border: 1px solid #E4E4E7;
                border-radius: 12px;
                padding: 36px;
                max-width: 440px;
                text-align: center;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            }
            .db-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #FEF2F2;
                color: #DC2626;
                padding: 4px 12px;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 700;
                letter-spacing: 0.04em;
                text-transform: uppercase;
                margin-bottom: 20px;
                border: 1px solid #FCA5A5;
            }
            .db-card h2 { margin: 0 0 10px 0; font-size: 1.35rem; color: #09090B; font-weight: 700; letter-spacing: -0.02em; }
            .db-card p { color: #71717A; font-size: 0.9rem; line-height: 1.5; margin-bottom: 24px; }
            .db-step {
                background: #F4F4F5;
                border: 1px solid #E4E4E7;
                border-radius: 8px;
                padding: 16px;
                text-align: left;
                margin-bottom: 24px;
                font-size: 0.85rem;
                color: #27272A;
                line-height: 1.6;
            }
            .db-step strong { color: #09090B; }
            .btn-retry {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                width: 100%;
                padding: 11px 20px;
                background: #09090B;
                color: #FFFFFF;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                font-size: 0.9rem;
                cursor: pointer;
                text-decoration: none;
                transition: background 0.15s ease;
            }
            .btn-retry:hover {
                background: #27272A;
            }
        </style>
    </head>
    <body>
        <div class="db-card">
            <div class="db-badge">● Database Offline</div>
            <h2>MySQL Service Paused</h2>
            <p>ParkEase is configured and ready, but MySQL is currently not running in XAMPP.</p>
            <div class="db-step">
                <strong>Easy 2-step setup:</strong><br>
                1. Open <strong>XAMPP Control Panel</strong><br>
                2. Click <strong>Start</strong> next to <strong>MySQL</strong><br>
                (Or double-click <strong>run_project.bat</strong>)
            </div>
            <a href="" class="btn-retry" onclick="location.reload(); return false;">Reconnect Database</a>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Enable UTF-8 encoding
mysqli_set_charset($conn, "utf8mb4");
?>