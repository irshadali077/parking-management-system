<?php
// =============================================
// Admin Dashboard
// Shows overall system statistics & real-time monitoring
// Clean Minimalist Black & White Theme
// =============================================
require_once 'includes/admin_auth.php';
require_once '../config/db.php';

// Get total users
$total_users = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM users");
if ($row = $result->fetch_assoc()) $total_users = $row['total'];

// Get total slots
$total_slots = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM slots");
if ($row = $result->fetch_assoc()) $total_slots = $row['total'];

// Get available slots
$available_slots = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM slots WHERE status = 'available'");
if ($row = $result->fetch_assoc()) $available_slots = $row['total'];

// Get occupied slots
$occupied_slots = $total_slots - $available_slots;
$utilization_rate = ($total_slots > 0) ? round(($occupied_slots / $total_slots) * 100) : 0;

// Get total bookings
$total_bookings = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM bookings");
if ($row = $result->fetch_assoc()) $total_bookings = $row['total'];

// Get active bookings
$active_bookings = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM bookings WHERE status = 'active'");
if ($row = $result->fetch_assoc()) $active_bookings = $row['total'];

// Get recent bookings (last 5)
$recent_bookings = $conn->query("SELECT b.id, b.booking_date, b.status, s.slot_number, u.fullname, u.student_id 
                                  FROM bookings b 
                                  JOIN slots s ON b.slot_id = s.id 
                                  JOIN users u ON b.user_id = u.id 
                                  ORDER BY b.booking_date DESC LIMIT 5");

include 'includes/admin_header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Operations & Analytics</h1>
        <p class="page-subtitle">Real-time campus telemetry • Inventory management • Driver permits</p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="slots.php" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Manage Bays
        </a>
        <a href="bookings.php" class="btn btn-outline btn-sm">
            All Reservations →
        </a>
    </div>
</div>

<!-- Statistics Cards (Border-Driven) -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-label">Registered Drivers</span>
            <div class="stat-icon-wrapper">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            </div>
        </div>
        <div class="stat-number"><?php echo $total_users; ?></div>
        <div class="stat-subtext">Students & authorized faculty</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-label">Vacant Parking Bays</span>
            <div class="stat-icon-wrapper" style="color:var(--success);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
            </div>
        </div>
        <div class="stat-number" style="color:var(--success);"><?php echo $available_slots; ?></div>
        <div class="stat-subtext">Ready for immediate check-in</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-label">Occupied Bays</span>
            <div class="stat-icon-wrapper" style="color:var(--danger);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M9 17V7h4a3 3 0 0 1 0 6H9"></path></svg>
            </div>
        </div>
        <div class="stat-number"><?php echo $occupied_slots; ?></div>
        <div class="stat-subtext"><?php echo $utilization_rate; ?>% capacity utilized</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-label">Active Permits</span>
            <div class="stat-icon-wrapper">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
            </div>
        </div>
        <div class="stat-number"><?php echo $active_bookings; ?></div>
        <div class="stat-subtext">Currently parked vehicles</div>
    </div>
</div>

<!-- Utilization Progress Meter -->
<div class="card" style="padding:18px 24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
        <span style="font-size:0.86rem; font-weight:700; color:var(--text-primary); text-transform:uppercase; letter-spacing:0.04em;">
            Campus Garage Capacity Meter
        </span>
        <span style="font-size:0.86rem; font-weight:700; color:var(--text-primary); font-family:var(--font-mono);">
            <?php echo $occupied_slots; ?> / <?php echo $total_slots; ?> BAYS (<?php echo $utilization_rate; ?>%)
        </span>
    </div>
    <div style="width:100%; height:8px; background:var(--bg-elevated); border:1px solid var(--border-color); border-radius:var(--radius-pill); overflow:hidden;">
        <div style="width:<?php echo $utilization_rate; ?>%; height:100%; background:var(--primary); border-radius:var(--radius-pill); transition:width 0.8s ease;"></div>
    </div>
</div>

<!-- Recent Bookings Table -->
<div style="margin-top:28px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <h2 style="font-size:1.15rem; color:var(--text-primary); font-weight:700;">Recent Vehicle Check-Ins</h2>
        <a href="bookings.php" style="font-size:0.84rem; color:var(--text-primary); font-weight:600; text-decoration:none;">View Full Ledger →</a>
    </div>

    <?php if ($recent_bookings && $recent_bookings->num_rows > 0): ?>
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Driver Details</th>
                        <th>Student / Staff ID</th>
                        <th>Bay Code</th>
                        <th>Check-in Timestamp</th>
                        <th>Session Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($b = $recent_bookings->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong style="color:var(--text-primary);"><?php echo htmlspecialchars($b['fullname']); ?></strong>
                            </td>
                            <td>
                                <span class="badge" style="background:var(--bg-elevated); font-family:var(--font-mono); color:var(--text-primary); border:1px solid var(--border-color);">
                                    <?php echo htmlspecialchars($b['student_id']); ?>
                                </span>
                            </td>
                            <td>
                                <strong style="color:var(--text-primary); font-family:var(--font-mono); font-size:1.05rem;">
                                    <?php echo htmlspecialchars($b['slot_number']); ?>
                                </strong>
                            </td>
                            <td><?php echo date('d M Y, h:i A', strtotime($b['booking_date'])); ?></td>
                            <td>
                                <?php
                                $badge = '';
                                if ($b['status'] == 'active') $badge = 'badge-active';
                                elseif ($b['status'] == 'cancelled') $badge = 'badge-cancelled';
                                else $badge = 'badge-completed';
                                ?>
                                <span class="badge <?php echo $badge; ?>"><?php echo strtoupper($b['status']); ?></span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="card" style="text-align:center; padding:36px;">
            <p style="color:var(--text-muted); font-size:0.9rem;">No vehicle sessions recorded yet.</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/admin_footer.php'; ?>
