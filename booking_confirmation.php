<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['booking_id'])) {
    header("Location: index.php");
    exit();
}

$booking_id = $_SESSION['booking_id'];

// Fetch booking details
$stmt = $pdo->prepare("
    SELECT b.*, m.title, t.name as theater_name, s.show_date, s.show_time 
    FROM bookings b
    JOIN showtimes s ON b.showtime_id = s.showtime_id
    JOIN movies m ON s.movie_id = m.movie_id
    JOIN theaters t ON s.theater_id = t.theater_id
    WHERE b.booking_id = ? AND b.payment_status = 'completed'
");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();

if (!$booking) {
    header("Location: index.php");
    exit();
}

// Clear booking session data
unset($_SESSION['booking_data']);
unset($_SESSION['booking_id']);
require_once 'includes/headers.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        <h2 class="card-title mt-3">Booking Confirmed!</h2>
                        <p class="text-muted">Booking ID: <?php echo $booking['booking_id']; ?></p>
                        
                        <div class="card mt-4">
                            <div class="card-body">
                                <h4><?php echo htmlspecialchars($booking['title']); ?></h4>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6 text-start">
                                        <p><strong>Theater:</strong> <?php echo htmlspecialchars($booking['theater_name']); ?></p>
                                        <p><strong>Date:</strong> <?php echo date('F d, Y', strtotime($booking['show_date'])); ?></p>
                                        <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($booking['show_time'])); ?></p>
                                    </div>
                                    <div class="col-md-6 text-start">
                                        <p><strong>Seats:</strong> <?php echo $booking['seat_numbers']; ?></p>
                                        <p><strong>Amount Paid:</strong> ₹<?php echo number_format($booking['total_amount'], 2); ?></p>
                                        <p><strong>Transaction ID:</strong> <?php echo $booking['transaction_id']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="my_bookings.php" class="btn btn-primary">View My Bookings</a>
                            <a href="index.php" class="btn btn-outline-secondary">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>
