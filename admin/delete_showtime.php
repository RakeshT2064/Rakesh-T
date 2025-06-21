<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

try {
    $showtime_id = $_GET['id'];
    
    // First delete related records from bookings table
    $deleteBookings = $pdo->prepare("DELETE FROM bookings WHERE showtime_id = ?");
    $deleteBookings->execute([$showtime_id]);
    
    // Then delete the showtime
    $stmt = $pdo->prepare("DELETE FROM showtimes WHERE showtime_id = ?");
    if (!$stmt->execute([$showtime_id])) {
        throw new PDOException("Failed to delete showtime");
    }
    
    $_SESSION['success'] = "Showtime and related bookings deleted successfully";
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Error: " . $e->getMessage();
}

header("Location: manage_showtimes.php");
exit();
?>