<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['booking_id'])) {
    header('Location: my_bookings.php');
    exit();
}

try {
    $pdo = ensureConnection();
    
    // Get booking details first
    $stmt = $pdo->prepare("SELECT show_date, show_time FROM bookings b 
                          JOIN showtimes s ON b.showtime_id = s.showtime_id 
                          WHERE booking_id = ? AND user_id = ?");
    $stmt->execute([$_POST['booking_id'], $_SESSION['user_id']]);
    $booking = $stmt->fetch();
    
    if ($booking) {
        $show_time = strtotime($booking['show_date'] . ' ' . $booking['show_time']);
        $current_time = time();
        $time_difference = $show_time - $current_time;
        
        if ($time_difference > (3 * 60 * 60)) {
            // Update booking status to cancelled
            $stmt = $pdo->prepare("UPDATE bookings SET payment_status = 'cancelled' 
                                 WHERE booking_id = ? AND user_id = ?");
            $stmt->execute([$_POST['booking_id'], $_SESSION['user_id']]);
            
            $_SESSION['success_message'] = "Booking cancelled successfully.";
        } else {
            $_SESSION['error_message'] = "Cannot cancel booking less than 3 hours before show time.";
        }
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error cancelling booking.";
    error_log("Cancel Booking Error: " . $e->getMessage());
}

header('Location: my_bookings.php');
exit();
?>