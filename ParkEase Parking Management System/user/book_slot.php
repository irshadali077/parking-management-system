<?php
// =============================================
// Book Parking Slot
// Displays available slots and handles booking
// Minimalist Black & White Border Grid
// =============================================
require_once '../includes/auth_check.php';
require_once '../config/db.php';

$error = "";
$success = "";

// Handle slot booking when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['slot_id'])) {
    $slot_id = intval($_POST['slot_id']);
    $user_id = $_SESSION['user_id'];

    // Check if user already has an active booking
    $check_active = $conn->prepare("SELECT id FROM bookings WHERE user_id = ? AND status = 'active'");
    if ($check_active) {
        $check_active->bind_param("i", $user_id);
        $check_active->execute();
        $check_active->store_result();

        if ($check_active->num_rows > 0) {
            $error = "You already have an active reservation! Please release your current bay before reserving another.";
        } else {
            // Check if the slot is still available
            $check_slot = $conn->prepare("SELECT id, slot_number FROM slots WHERE id = ? AND status = 'available'");
            if ($check_slot) {
                $check_slot->bind_param("i", $slot_id);
                $check_slot->execute();
                $result = $check_slot->get_result();

                if ($result && $result->num_rows == 1) {
                    $slot = $result->fetch_assoc();

                    // Book the slot - insert booking record
                    $book = $conn->prepare("INSERT INTO bookings (user_id, slot_id, booking_date, status) VALUES (?, ?, NOW(), 'active')");
                    $book->bind_param("ii", $user_id, $slot_id);

                    // Update slot status to occupied
                    $update_slot = $conn->prepare("UPDATE slots SET status = 'occupied' WHERE id = ?");
                    $update_slot->bind_param("i", $slot_id);

                    if ($book->execute() && $update_slot->execute()) {
                        $success = "Parking Bay " . htmlspecialchars($slot['slot_number']) . " confirmed and assigned to your vehicle!";
                    } else {
                        $error = "Unable to process reservation. Please try again.";
                    }
                    $book->close();
                    $update_slot->close();
                } else {
                    $error = "This bay was just reserved by another driver. Please choose another open bay.";
                }
                $check_slot->close();
            }
        }
        $check_active->close();
    }
}

// Get all slots to display
$slots_query = $conn->query("SELECT * FROM slots ORDER BY slot_number ASC");
$all_slots = [];
$available_count = 0;
$occupied_count = 0;

if ($slots_query) {
    while ($row = $slots_query->fetch_assoc()) {
        $all_slots[] = $row;
        if ($row['status'] === 'available') $available_count++;
        else $occupied_count++;
    }
}

include '../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Live Parking Terminal</h1>
        <p class="page-subtitle">North Campus Garage • Ground Deck • Real-time bay occupancy</p>
    </div>

    <!-- Quick Status Counter -->
    <div style="display:flex; align-items:center; gap:8px;">
        <span class="badge badge-active" style="padding:6px 12px;">
            ● <?php echo $available_count; ?> BAYS OPEN
        </span>
        <span class="badge badge-cancelled" style="padding:6px 12px;">
            ● <?php echo $occupied_count; ?> OCCUPIED
        </span>
    </div>
</div>

<!-- Alert Notifications -->
<?php if ($error): ?>
    <div class="alert alert-error">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        <div><?php echo htmlspecialchars($error); ?></div>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <div>
            <?php echo htmlspecialchars($success); ?> 
            <a href="dashboard.php" style="color:var(--success); font-weight:700; text-decoration:underline; margin-left:8px;">View Active Pass →</a>
        </div>
    </div>
<?php endif; ?>

<!-- Minimalist White & Black Garage Canvas -->
<div class="garage-canvas">
    <!-- Toolbar: Zone Filters and Live Search -->
    <div class="garage-toolbar">
        <div class="filter-pills">
            <button class="filter-btn active" onclick="filterBays('all', this)">All Bays (<?php echo count($all_slots); ?>)</button>
            <button class="filter-btn" onclick="filterBays('zone-a', this)">Zone A (North)</button>
            <button class="filter-btn" onclick="filterBays('zone-b', this)">Zone B (East)</button>
            <button class="filter-btn" onclick="filterBays('available', this)">Available Only (<?php echo $available_count; ?>)</button>
        </div>

        <div class="search-input-wrap">
            <svg class="search-icon-svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <input type="text" id="baySearch" placeholder="Filter bay code e.g. A1, B3..." onkeyup="searchBays()">
        </div>
    </div>

    <!-- Direction / Garage Lane Legend -->
    <div class="garage-lane-indicator">
        <span>← Entry Gate 1</span>
        <span>• &nbsp; One-Way Garage Driveway • Speed 10 km/h &nbsp; •</span>
        <span>Exit Ramp 2 →</span>
    </div>

    <!-- Parking Bays Grid -->
    <div class="slots-grid" id="slotsGrid">
        <?php foreach ($all_slots as $slot): 
            $is_zone_a = (strpos(strtoupper($slot['slot_number']), 'A') === 0);
            $zone_class = $is_zone_a ? 'zone-a' : 'zone-b';
            $slot_clean = htmlspecialchars($slot['slot_number']);
        ?>
            <div class="slot-bay <?php echo $slot['status']; ?> <?php echo $zone_class; ?>"
                 data-bay="<?php echo strtolower($slot_clean); ?>"
                 data-status="<?php echo $slot['status']; ?>"
                 data-zone="<?php echo $zone_class; ?>"
                 <?php if ($slot['status'] === 'available'): ?>
                     onclick="confirmBooking(<?php echo $slot['id']; ?>, '<?php echo $slot_clean; ?>', '<?php echo $is_zone_a ? 'Zone A (North Gate)' : 'Zone B (East Wing)'; ?>')"
                     role="button"
                     tabindex="0"
                     title="Click to reserve bay <?php echo $slot_clean; ?>"
                 <?php else: ?>
                     title="Bay <?php echo $slot_clean; ?> is currently occupied"
                 <?php endif; ?>
            >
                <div>
                    <div class="bay-number"><?php echo $slot_clean; ?></div>
                    <div class="bay-type"><?php echo $is_zone_a ? 'Sedan / SUV' : 'EV / Compact'; ?></div>
                </div>

                <div>
                    <span class="bay-status-badge">
                        <?php echo $slot['status'] === 'available' ? 'AVAILABLE' : 'OCCUPIED'; ?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Booking Confirmation Modal Sheet -->
<div class="modal-overlay" id="bookingModal">
    <div class="modal-box">
        <h3 style="font-size:1.25rem; font-weight:700; color:var(--text-primary); margin-bottom:6px;">Confirm Bay Reservation</h3>
        <p style="color:var(--text-muted); font-size:0.86rem; margin-bottom:18px;">Lock in your assigned parking bay before arrival.</p>

        <div style="background:var(--bg-elevated); border:1px solid var(--border-color); border-radius:var(--radius-sm); padding:14px; text-align:left; margin-bottom:20px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <span style="color:var(--text-muted); font-size:0.82rem;">Assigned Bay:</span>
                <strong id="modalSlotName" style="color:var(--text-primary); font-size:1.1rem; font-family:var(--font-mono);"></strong>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <span style="color:var(--text-muted); font-size:0.82rem;">Location:</span>
                <span id="modalSlotZone" style="color:var(--text-primary); font-size:0.86rem; font-weight:600;"></span>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span style="color:var(--text-muted); font-size:0.82rem;">Permit Access:</span>
                <span style="color:var(--success); font-size:0.86rem; font-weight:700;">Student Validated</span>
            </div>
        </div>

        <form method="POST" action="">
            <input type="hidden" name="slot_id" id="modalSlotId">
            <div class="modal-actions">
                <button type="button" class="btn btn-outline btn-sm" onclick="closeModal()">Dismiss</button>
                <button type="submit" class="btn btn-primary btn-sm">Confirm & Issue Pass</button>
            </div>
        </form>
    </div>
</div>

<script>
function confirmBooking(slotId, slotName, zoneName) {
    document.getElementById('modalSlotId').value = slotId;
    document.getElementById('modalSlotName').textContent = slotName;
    document.getElementById('modalSlotZone').textContent = zoneName;
    document.getElementById('bookingModal').classList.add('active');
}

function closeModal() {
    document.getElementById('bookingModal').classList.remove('active');
}

document.getElementById('bookingModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

function filterBays(criteria, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const bays = document.querySelectorAll('.slot-bay');
    bays.forEach(bay => {
        if (criteria === 'all') {
            bay.style.display = 'flex';
        } else if (criteria === 'available') {
            bay.style.display = (bay.dataset.status === 'available') ? 'flex' : 'none';
        } else if (criteria === 'zone-a') {
            bay.style.display = (bay.dataset.zone === 'zone-a') ? 'flex' : 'none';
        } else if (criteria === 'zone-b') {
            bay.style.display = (bay.dataset.zone === 'zone-b') ? 'flex' : 'none';
        }
    });
}

function searchBays() {
    const query = document.getElementById('baySearch').value.toLowerCase().trim();
    const bays = document.querySelectorAll('.slot-bay');
    bays.forEach(bay => {
        const code = bay.dataset.bay;
        if (!query || code.includes(query)) {
            bay.style.display = 'flex';
        } else {
            bay.style.display = 'none';
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>
