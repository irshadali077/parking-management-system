<?php
// =============================================
// Admin - View All Bookings
// Lists all bookings with user and slot info
// Clean Minimalist Black & White Theme
// =============================================
require_once 'includes/admin_auth.php';
require_once '../config/db.php';

// Get all bookings with user and slot details
$bookings = $conn->query("SELECT b.id, b.booking_date, b.status, 
                                  s.slot_number, u.fullname, u.student_id
                           FROM bookings b 
                           JOIN slots s ON b.slot_id = s.id 
                           JOIN users u ON b.user_id = u.id 
                           ORDER BY b.booking_date DESC");

include 'includes/admin_header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Master Bookings Ledger</h1>
        <p class="page-subtitle">Comprehensive audit trail of all vehicle reservations and gate clearances</p>
    </div>
</div>

<?php if ($bookings && $bookings->num_rows > 0): ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Permit #</th>
                    <th>Driver Name</th>
                    <th>Student / Staff ID</th>
                    <th>Assigned Bay</th>
                    <th>Reservation Timestamp</th>
                    <th>Session Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($b = $bookings->fetch_assoc()): ?>
                    <tr>
                        <td style="font-family:var(--font-mono); color:var(--text-muted); font-weight:600;">
                            #PK-<?php echo str_pad($b['id'], 4, '0', STR_PAD_LEFT); ?>
                        </td>
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
                            <span class="badge <?php echo $badge; ?>">
                                <?php if ($b['status'] == 'active'): ?>
                                    ● ACTIVE
                                <?php elseif ($b['status'] == 'cancelled'): ?>
                                    CANCELLED
                                <?php else: ?>
                                    COMPLETED
                                <?php endif; ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="card" style="text-align:center; padding:36px;">
        <p style="color:var(--text-muted); font-size:0.9rem;">No parking sessions have been logged yet.</p>
    </div>
<?php endif; ?>

<?php include 'includes/admin_footer.php'; ?>
