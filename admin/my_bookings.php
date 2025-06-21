<?php
session_start();
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("
    SELECT b.*, m.title, t.name as theater_name, s.show_date, s.show_time, s.price
    FROM bookings b
    JOIN showtimes s ON b.showtime_id = s.showtime_id
    JOIN movies m ON s.movie_id = m.movie_id
    JOIN theaters t ON s.theater_id = t.theater_id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Movie Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Movie Booking</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="my_bookings.php">My Bookings</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>My Bookings</h2>
        
        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">Booking completed successfully!</div>
        <?php endif; ?>

        <?php if(empty($bookings)): ?>
            <p>You haven't made any bookings yet.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach($bookings as $booking): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($booking['title']); ?></h5>
                                <p class="card-text">
                                    <strong>Theater:</strong> <?php echo htmlspecialchars($booking['theater_name']); ?><br>
                                    <strong>Date:</strong> <?php echo date('F d, Y', strtotime($booking['show_date'])); ?><br>
                                    <strong>Time:</strong> <?php echo date('h:i A', strtotime($booking['show_time'])); ?><br>
                                    <strong>Seats:</strong> <?php echo $booking['seats_booked']; ?><br>
                                    <strong>Total Amount:</strong> $<?php echo number_format($booking['total_amount'], 2); ?><br>
                                    <strong>Booking Date:</strong> <?php echo date('F d, Y h:i A', strtotime($booking['booking_date'])); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>