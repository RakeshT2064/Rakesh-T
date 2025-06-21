<?php
session_start();
require_once 'config/database.php';

if(!isset($_SESSION['user_id']) || !isset($_POST['booking_id'])) {
    header("Location: index.php");
    exit();
}

$booking_id = $_POST['booking_id'];

// Update booking status
$stmt = $pdo->prepare("UPDATE bookings SET payment_status = 'completed' WHERE booking_id = ?");
$stmt->execute([$booking_id]);

$_SESSION['success'] = "Payment successful! Your booking is confirmed.";
header("Location: my_bookings.php");
exit();