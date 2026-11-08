<?php
// =============================================
// User Dashboard
// Shows parking statistics and active booking pass
// Minimalist Black & White Border-Driven Design
// =============================================
require_once '../includes/auth_check.php';
require_once '../config/db.php';

// Get total number of parking slots
$total_slots = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM slots");
if ($row = $result->fetch_assoc()) {
    $total_slots = $row['total'];
}

// Get number of available slots
$available_slots = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM slots WHERE status = 'available'");
if ($row = $result->fetch_assoc()) {
    $available_slots = $row['total'];
}

// Get number of occupied slots
$occupied_slots = $total_slots - $available_slots;
$occupancy_percentage = ($total_slots > 0) ? round(($occupied_slots / $total_slots) * 100) : 0;

// Check if current user has an active booking
$active_booking = null;
$stmt = $conn->prepare("SELECT b.id, b.booking_date, s.slot_number 
                         FROM bookings b 
                         JOIN slots s ON b.slot_id = s.id 
                         WHERE b.user_id = ? AND b.status = 'active' 
                         LIMIT 1");
if ($stmt) {
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $active_booking = $result->fetch_assoc();
    }
    $stmt->close();
}

// Get user profile details for pass display
$user_stmt = $conn->prepare("SELECT fullname, student_id, email FROM users WHERE id = ?");
$user_stmt->bind_param("i", $_SESSION['user_id']);
$user_stmt->execute();
$user_info = $user_stmt->get_result()->fetch_assoc();
$user_stmt->close();

include '../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Driver Terminal</h1>
        <p class="page-subtitle">Welcome back, <strong><?php echo htmlspecialchars($user_info['fullname'] ?? $_SESSION['user_name']); ?></strong> • ID: <?php echo htmlspecialchars($user_info['student_id'] ?? 'CAMPUS-PASS'); ?></p>
    </div>
    
    <div>
        <a href="book_slot.php" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Reserve Parking Bay
        </a>
    </div>
</div>

<!-- Clean Border Telemetry Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-label">Total Garage Bays</span>
            <div class="stat-icon-wrapper">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M9 17V7h4a3 3 0 0 1 0 6H9"></path></svg>
            </div>
        </div>
        <div class="stat-number"><?php echo $total_slots; ?></div>
        <div class="stat-subtext">Deck A & Ground Floor</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-label">Available Now</span>
            <div class="stat-icon-wrapper" style="color:var(--success);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
            </div>
        </div>
        <div class="stat-number" style="color:var(--success);"><?php echo $available_slots; ?></div>
        <div class="stat-subtext" style="color:var(--success); font-weight:600;">● Ready for reservation</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-label">Parked Vehicles</span>
            <div class="stat-icon-wrapper" style="color:var(--danger);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M9 17V7h4a3 3 0 0 1 0 6H9"></path></svg>
            </div>
        </div>
        <div class="stat-number"><?php echo $occupied_slots; ?></div>
        <div class="stat-subtext"><?php echo $occupancy_percentage; ?>% capacity utilized</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-label">Your Active Permit</span>
            <div class="stat-icon-wrapper">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            </div>
        </div>
        <div class="stat-number"><?php echo $active_booking ? '1' : '0'; ?></div>
        <div class="stat-subtext">
            <?php echo $active_booking ? 'Bay ' . htmlspecialchars($active_booking['slot_number']) . ' Locked' : 'No active pass'; ?>
        </div>
    </div>
</div>

<!-- Minimalist Capacity Meter -->
<div class="card" style="padding:18px 24px; margin-bottom:28px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <span style="font-size:0.84rem; font-weight:700; color:var(--text-primary); text-transform:uppercase; letter-spacing:0.04em;">
            Campus Garage Capacity Meter
        </span>
        <span style="font-size:0.84rem; font-weight:700; color:var(--text-primary); font-family:var(--font-mono);"><?php echo $occupancy_percentage; ?>% FILLED</span>
    </div>
    <div style="width:100%; height:8px; background:var(--bg-elevated); border:1px solid var(--border-color); border-radius:var(--radius-pill); overflow:hidden;">
        <div style="width:<?php echo $occupancy_percentage; ?>%; height:100%; background:var(--primary); border-radius:var(--radius-pill); transition:width 0.8s ease;"></div>
    </div>
</div>

<!-- ACTIVE BOOKING: MINIMALIST BORDER PASS TICKET -->
<?php if ($active_booking): ?>
    <div style="margin-bottom:28px;">
        <h3 class="card-title" style="margin-bottom:14px;">Active Parking Permit</h3>

        <div class="digital-pass-card">
            <!-- Left Main Ticket Section -->
            <div class="pass-main-section">
                <div class="pass-header-badge">
                    <span style="width:6px; height:6px; border-radius:50%; background:var(--success);"></span>
                    ACTIVE PERMIT • GATE PASS #PK-<?php echo str_pad($active_booking['id'], 4, '0', STR_PAD_LEFT); ?>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px;">
                    <div>
                        <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; font-weight:700; letter-spacing:0.06em;">ASSIGNED PARKING BAY</div>
                        <div class="pass-bay-display">
                            BAY <?php echo htmlspecialchars($active_booking['slot_number']); ?>
                        </div>
                    </div>

                    <div style="text-align:right;">
                        <span class="badge badge-active" style="padding:4px 10px;">VALIDATED</span>
                    </div>
                </div>

                <div class="pass-details-grid">
                    <div class="pass-detail-item">
                        <label>Driver Name</label>
                        <span><?php echo htmlspecialchars($user_info['fullname'] ?? 'Student Driver'); ?></span>
                    </div>
                    <div class="pass-detail-item">
                        <label>Campus ID</label>
                        <span style="font-family:var(--font-mono);"><?php echo htmlspecialchars($user_info['student_id'] ?? 'STU-001'); ?></span>
                    </div>
                    <div class="pass-detail-item">
                        <label>Reservation Time</label>
                        <span><?php echo date('d M Y, h:i A', strtotime($active_booking['booking_date'])); ?></span>
                    </div>
                    <div class="pass-detail-item">
                        <label>Zone Location</label>
                        <span>North Campus Deck</span>
                    </div>
                </div>
            </div>

            <!-- Right QR & Release Section -->
            <div class="pass-side-section">
                <div style="background:#fff; border:1px solid var(--border-color); padding:8px; border-radius:8px; margin-bottom:10px;">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="#09090B">
                        <path d="M2 2h8v8H2zM4 4v4h4V4zM14 2h8v8h-8zM16 4v4h4V4zM2 14h8v8H2zM4 16v4h4v-4zM14 14h2v2h-2zM18 14h4v2h-4zM14 18h4v4h-4zM20 18h2v4h-2z"/>
                    </svg>
                </div>
                <div style="font-size:0.68rem; color:var(--text-muted); font-family:var(--font-mono); letter-spacing:0.06em; margin-bottom:14px;">
                    SCAN AT GATE BOOTH
                </div>

                <form method="POST" action="cancel_booking.php" style="width:100%;">
                    <input type="hidden" name="booking_id" value="<?php echo $active_booking['id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm" style="width:100%;" onclick="return confirm('Check out and release parking bay <?php echo htmlspecialchars($active_booking['slot_number']); ?>?')">
                        Release & Exit Bay
                    </button>
                </form>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Empty State When No Booking -->
    <div class="card" style="text-align:center; padding:44px 24px; margin-bottom:28px;">
        <div style="width:52px; height:52px; border-radius:50%; background:var(--bg-elevated); border:1px solid var(--border-color); display:flex; align-items:center; justify-content:center; margin:0 auto 14px; color:var(--text-muted);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M9 17V7h4a3 3 0 0 1 0 6H9"></path></svg>
        </div>
        <h3 style="font-size:1.15rem; color:var(--text-primary); margin-bottom:4px;">No Vehicle Checked In</h3>
        <p style="color:var(--text-muted); max-width:400px; margin:0 auto 18px; font-size:0.88rem;">
            You currently do not have any active parking reservation. Select an open parking bay to generate your digital gate permit.
        </p>
        <a href="book_slot.php" class="btn btn-primary btn-sm">
            Browse & Reserve Open Bay →
        </a>
    </div>
<?php endif; ?>

<!-- Quick Actions Section (Border-Driven) -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:16px;">
    <div class="card" style="display:flex; gap:16px; align-items:center; margin-bottom:0;">
        <div style="width:44px; height:44px; border-radius:8px; background:var(--bg-elevated); border:1px solid var(--border-color); display:flex; align-items:center; justify-content:center; color:var(--text-primary); flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <div>
            <h4 style="color:var(--text-primary); font-size:0.95rem; margin-bottom:2px;">Reservation History</h4>
            <p style="color:var(--text-muted); font-size:0.82rem; margin-bottom:6px;">View completed permits and past parking logs</p>
            <a href="history.php" style="color:var(--text-primary); font-size:0.84rem; font-weight:700; text-decoration:none;">Open Trip Logs →</a>
        </div>
    </div>

    <div class="card" style="display:flex; gap:16px; align-items:center; margin-bottom:0;">
        <div style="width:44px; height:44px; border-radius:8px; background:var(--bg-elevated); border:1px solid var(--border-color); display:flex; align-items:center; justify-content:center; color:var(--text-primary); flex-shrink:0;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        </div>
        <div>
            <h4 style="color:var(--text-primary); font-size:0.95rem; margin-bottom:2px;">Driver Account</h4>
            <p style="color:var(--text-muted); font-size:0.82rem; margin-bottom:6px;">Update registered phone and authentication password</p>
            <a href="profile.php" style="color:var(--text-primary); font-size:0.84rem; font-weight:700; text-decoration:none;">Account Settings →</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
