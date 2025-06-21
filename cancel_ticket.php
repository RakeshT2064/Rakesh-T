<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Get booking details first
        $stmt = $pdo->prepare("SELECT b.showtime_id, b.seat_numbers, b.status, s.show_date, s.show_time 
                              FROM bookings b 
                              JOIN showtimes s ON b.showtime_id = s.showtime_id 
                              WHERE b.booking_id = ? AND b.user_id = ?");
        $stmt->execute([$booking_id, $_SESSION['user_id']]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$booking) {
            throw new PDOException("Booking not found");
        }
        
        // Check if show time hasn't passed
        $show_datetime = strtotime($booking['show_date'] . ' ' . $booking['show_time']);
        if (time() > $show_datetime) {
            throw new PDOException("Cannot cancel past bookings");
        }
        
        // Delete from booking_seats table first (due to foreign key constraint)
        $delete_seats = $pdo->prepare("DELETE FROM booking_seats WHERE booking_id = ?");
        $delete_seats->execute([$booking_id]);
        
        // Delete the booking record
        $delete_booking = $pdo->prepare("DELETE FROM bookings WHERE booking_id = ? AND user_id = ?");
        $delete_booking->execute([$booking_id, $_SESSION['user_id']]);
        
        // Commit transaction
        $pdo->commit();
        
        $_SESSION['success_message'] = "Booking cancelled successfully and seats released!";
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Cancel booking error: " . $e->getMessage());
        $_SESSION['error_message'] = "Failed to cancel booking: " . $e->getMessage();
    }
    
    header("Location: my_bookings.php");
    exit();
}

// If no POST request, redirect back
header("Location: my_bookings.php");
exit();
?>