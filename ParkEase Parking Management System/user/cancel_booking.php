<?php
// =============================================
// Cancel Booking
// Handles booking cancellation via POST request
// Updates both booking status and slot status
// =============================================
require_once '../includes/auth_check.php';
require_once '../config/db.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);
    $user_id = $_SESSION['user_id'];

    // Get the booking and verify it belongs to the current user
    $stmt = $conn->prepare("SELECT b.id, b.slot_id FROM bookings b WHERE b.id = ? AND b.user_id = ? AND b.status = 'active'");
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $booking = $result->fetch_assoc();

        // Update booking status to 'cancelled'
        $cancel = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        $cancel->bind_param("i", $booking_id);
        $cancel->execute();
        $cancel->close();

        // Set the slot back to 'available'
        $free_slot = $conn->prepare("UPDATE slots SET status = 'available' WHERE id = ?");
        $free_slot->bind_param("i", $booking['slot_id']);
        $free_slot->execute();
        $free_slot->close();
    }
    $stmt->close();
}

// Redirect back to dashboard
header("Location: dashboard.php");
exit();
?>
