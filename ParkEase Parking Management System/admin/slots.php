<?php
// =============================================
// Admin - Manage Parking Slots
// Add, edit status, and delete slots
// Clean Minimalist Black & White Theme
// =============================================
require_once 'includes/admin_auth.php';
require_once '../config/db.php';

$error = "";
$success = "";

// Handle adding a new slot
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_slot'])) {
    $slot_number = trim(strtoupper($_POST['slot_number'] ?? ''));

    if (empty($slot_number)) {
        $error = "Bay designation code is required!";
    } else {
        // Check if slot number already exists
        $check = $conn->prepare("SELECT id FROM slots WHERE slot_number = ?");
        $check->bind_param("s", $slot_number);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Bay '$slot_number' already exists in the inventory!";
        } else {
            $stmt = $conn->prepare("INSERT INTO slots (slot_number, status) VALUES (?, 'available')");
            $stmt->bind_param("s", $slot_number);
            if ($stmt->execute()) {
                $success = "Parking Bay '$slot_number' registered successfully into campus inventory!";
            } else {
                $error = "Failed to add parking bay.";
            }
            $stmt->close();
        }
        $check->close();
    }
}

// Handle toggling slot status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_status'])) {
    $slot_id = intval($_POST['slot_id'] ?? 0);
    $new_status = $_POST['new_status'] ?? '';

    if (in_array($new_status, ['available', 'occupied'])) {
        $stmt = $conn->prepare("UPDATE slots SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $slot_id);
        if ($stmt->execute()) {
            if ($new_status === 'available') {
                $comp = $conn->prepare("UPDATE bookings SET status = 'completed' WHERE slot_id = ? AND status = 'active'");
                $comp->bind_param("i", $slot_id);
                $comp->execute();
                $comp->close();
            }
            $success = "Bay occupancy status updated successfully!";
        }
        $stmt->close();
    }
}

// Handle deleting a slot
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_slot'])) {
    $slot_id = intval($_POST['slot_id'] ?? 0);

    // Only delete if slot is available
    $check = $conn->prepare("SELECT status, slot_number FROM slots WHERE id = ?");
    $check->bind_param("i", $slot_id);
    $check->execute();
    $result = $check->get_result();

    if ($result && $result->num_rows == 1) {
        $slot = $result->fetch_assoc();
        if ($slot['status'] == 'available') {
            $del = $conn->prepare("DELETE FROM slots WHERE id = ?");
            $del->bind_param("i", $slot_id);
            if ($del->execute()) {
                $success = "Bay " . htmlspecialchars($slot['slot_number']) . " removed from inventory!";
            }
            $del->close();
        } else {
            $error = "Cannot remove an occupied bay! Release the active booking first.";
        }
    }
    $check->close();
}

// Get all slots
$slots = $conn->query("SELECT * FROM slots ORDER BY slot_number ASC");

include 'includes/admin_header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Parking Bay Inventory</h1>
        <p class="page-subtitle">Configure physical bays, manual overrides, and infrastructure telemetry</p>
    </div>
</div>

<!-- Messages -->
<?php if ($error): ?>
    <div class="alert alert-error">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <div><?php echo htmlspecialchars($error); ?></div>
    </div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <div><?php echo htmlspecialchars($success); ?></div>
    </div>
<?php endif; ?>

<!-- Register New Bay Card -->
<div class="card" style="margin-bottom:28px;">
    <h3 class="card-title">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Register New Parking Bay
    </h3>
    <form method="POST" action="" style="display:flex; gap:14px; align-items:flex-end; flex-wrap:wrap; margin-top:14px;">
        <div class="form-group" style="margin-bottom:0; flex:1; min-width:240px;">
            <label for="slot_number">Bay Designation Code</label>
            <div class="input-wrap">
                <span class="input-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><path d="M9 17V7h4a3 3 0 0 1 0 6H9"></path></svg>
                </span>
                <input type="text" id="slot_number" name="slot_number" placeholder="e.g. A11, C1, VIP-01" required style="text-transform:uppercase; font-family:var(--font-mono);">
            </div>
        </div>
        <button type="submit" name="add_slot" class="btn btn-primary" style="height:44px;">
            + Add to Inventory
        </button>
    </form>
</div>

<!-- Slots Table -->
<?php if ($slots && $slots->num_rows > 0): ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Bay Code</th>
                    <th>Zone</th>
                    <th>Sensor Status</th>
                    <th>Manual Control</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; ?>
                <?php while ($s = $slots->fetch_assoc()): 
                    $is_a = (strpos(strtoupper($s['slot_number']), 'A') === 0);
                ?>
                    <tr>
                        <td style="color:var(--text-muted); font-family:var(--font-mono);"><?php echo $count++; ?></td>
                        <td>
                            <strong style="color:var(--text-primary); font-family:var(--font-mono); font-size:1.15rem;">
                                <?php echo htmlspecialchars($s['slot_number']); ?>
                            </strong>
                        </td>
                        <td style="color:var(--text-secondary); font-size:0.88rem;">
                            <?php echo $is_a ? 'Zone A (North Gate)' : 'Zone B (East Wing)'; ?>
                        </td>
                        <td>
                            <span class="badge <?php echo $s['status'] == 'available' ? 'badge-active' : 'badge-cancelled'; ?>">
                                <?php echo $s['status'] == 'available' ? '● AVAILABLE' : '● OCCUPIED'; ?>
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:8px; align-items:center;">
                                <!-- Toggle Status -->
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="slot_id" value="<?php echo $s['id']; ?>">
                                    <input type="hidden" name="new_status" 
                                           value="<?php echo $s['status'] == 'available' ? 'occupied' : 'available'; ?>">
                                    <button type="submit" name="toggle_status" 
                                            class="btn btn-sm <?php echo $s['status'] == 'available' ? 'btn-danger' : 'btn-success'; ?>">
                                        <?php echo $s['status'] == 'available' ? 'Force Occupied' : 'Force Available'; ?>
                                    </button>
                                </form>

                                <!-- Delete Slot -->
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="slot_id" value="<?php echo $s['id']; ?>">
                                    <button type="submit" name="delete_slot" class="btn btn-outline btn-sm" 
                                            onclick="return confirm('Remove bay <?php echo htmlspecialchars($s['slot_number']); ?> from inventory?')"
                                            <?php echo $s['status'] == 'occupied' ? 'disabled style="opacity:0.35; cursor:not-allowed;" title="Cannot remove occupied bay"' : ''; ?>>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="card" style="text-align:center; padding:36px;">
        <p style="color:var(--text-muted); font-size:0.9rem;">No parking bays found in inventory.</p>
    </div>
<?php endif; ?>

<?php include 'includes/admin_footer.php'; ?>
