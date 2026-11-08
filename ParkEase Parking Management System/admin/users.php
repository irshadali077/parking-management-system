<?php
// =============================================
// Admin - View All Users
// Lists all registered users in a table
// Clean Minimalist Black & White Theme
// =============================================
require_once 'includes/admin_auth.php';
require_once '../config/db.php';

// Get all users
$users = $conn->query("SELECT id, fullname, email, phone, student_id, created_at FROM users ORDER BY created_at DESC");

include 'includes/admin_header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Registered Campus Drivers</h1>
        <p class="page-subtitle">Directory of students and staff authorized to book parking bays</p>
    </div>
</div>

<?php if ($users && $users->num_rows > 0): ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Driver Full Name</th>
                    <th>Campus Student / Staff ID</th>
                    <th>Email Address</th>
                    <th>Contact Phone</th>
                    <th>Account Created</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; ?>
                <?php while ($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td style="color:var(--text-muted); font-family:var(--font-mono);"><?php echo $count++; ?></td>
                        <td>
                            <strong style="color:var(--text-primary);"><?php echo htmlspecialchars($u['fullname']); ?></strong>
                        </td>
                        <td>
                            <span class="badge" style="background:var(--bg-elevated); font-family:var(--font-mono); color:var(--text-primary); border:1px solid var(--border-color);">
                                <?php echo htmlspecialchars($u['student_id']); ?>
                            </span>
                        </td>
                        <td>
                            <span style="color:var(--text-primary); font-size:0.9rem; font-weight:500;"><?php echo htmlspecialchars($u['email']); ?></span>
                        </td>
                        <td style="font-family:var(--font-mono); font-size:0.88rem;"><?php echo htmlspecialchars($u['phone']); ?></td>
                        <td style="color:var(--text-muted); font-size:0.85rem;"><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="card" style="text-align:center; padding:36px;">
        <p style="color:var(--text-muted); font-size:0.9rem;">No driver accounts found in the database.</p>
    </div>
<?php endif; ?>

<?php include 'includes/admin_footer.php'; ?>
