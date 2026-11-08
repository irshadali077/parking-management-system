<?php
// =============================================
// Booking History
// Displays all past and current bookings
// =============================================
require_once '../includes/auth_check.php';
require_once '../config/db.php';

// Get all bookings for the current user, newest first
$stmt = $conn->prepare("SELECT b.id, b.booking_date, b.status, s.slot_number 
                         FROM bookings b 
                         JOIN slots s ON b.slot_id = s.id 
                         WHERE b.user_id = ? 
                         ORDER BY b.booking_date DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$bookings = $stmt->get_result();

include '../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Permit & Activity Ledger
        </h1>
        <p class="page-subtitle">Historical records of your campus parking sessions and check-outs</p>
    </div>
    <a href="book_slot.php" class="btn btn-primary btn-sm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        New Reservation
    </a>
</div>

<?php if ($bookings && $bookings->num_rows > 0): ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Permit #</th>
                    <th>Designated Bay</th>
                    <th>Zone Location</th>
                    <th>Issue Timestamp</th>
                    <th>Permit Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($booking = $bookings->fetch_assoc()): 
                    $is_a = (strpos(strtoupper($booking['slot_number']), 'A') === 0);
                ?>
                    <tr>
                        <td style="font-family:var(--font-mono); color:var(--text-muted); font-weight:600;">
                            #PK-<?php echo str_pad($booking['id'], 4, '0', STR_PAD_LEFT); ?>
                        </td>
                        <td>
                            <strong style="font-family:var(--font-mono); font-size:1.1rem; color:var(--primary-hover);">
                                <?php echo htmlspecialchars($booking['slot_number']); ?>
                            </strong>
                        </td>
                        <td style="color:var(--text-secondary); font-size:0.88rem;">
                            <?php echo $is_a ? 'Zone A (North Gate)' : 'Zone B (East Wing)'; ?>
                        </td>
                        <td><?php echo date('d M Y, h:i A', strtotime($booking['booking_date'])); ?></td>
                        <td>
                            <?php
                            $badge_class = '';
                            if ($booking['status'] == 'active') $badge_class = 'badge-active';
                            elseif ($booking['status'] == 'cancelled') $badge_class = 'badge-cancelled';
                            else $badge_class = 'badge-completed';
                            ?>
                            <span class="badge <?php echo $badge_class; ?>">
                                <?php if ($booking['status'] == 'active'): ?>
                                    ● ACTIVE PASS
                                <?php elseif ($booking['status'] == 'cancelled'): ?>
                                    RELEASED
                                <?php else: ?>
                                    COMPLETED
                                <?php endif; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($booking['status'] == 'active'): ?>
                                <form method="POST" action="cancel_booking.php" style="display:inline;">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" 
                                            onclick="return confirm('Release and cancel your active reservation for bay <?php echo htmlspecialchars($booking['slot_number']); ?>?')">
                                        Release Bay
                                    </button>
                                </form>
                            <?php else: ?>
                                <span style="color:var(--text-muted); font-size:0.85rem;">Archived</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="card" style="text-align:center; padding:48px 24px;">
        <div style="width:60px; height:60px; border-radius:50%; background:var(--bg-surface-elevated); border:1px solid var(--border-medium); display:flex; align-items:center; justify-content:center; margin:0 auto 16px; color:var(--text-muted);">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
        <h3 style="color:var(--text-primary); font-size:1.2rem; margin-bottom:6px;">No Parking Trips Recorded</h3>
        <p style="color:var(--text-muted); max-width:400px; margin:0 auto 20px; font-size:0.9rem;">
            You have not booked any parking bays yet. Once you check in to a bay, your records will be archived here.
        </p>
        <a href="book_slot.php" class="btn btn-primary btn-sm">Reserve Your First Bay →</a>
    </div>
<?php endif; ?>

<?php
$stmt->close();
include '../includes/footer.php';
?>
