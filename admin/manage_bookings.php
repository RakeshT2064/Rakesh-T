<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
$page_title = "Admin Dashboard";

// Fetch all bookings with user and movie details
$stmt = $pdo->query("
    SELECT b.*, u.username, u.email, m.title as movie_title, t.name as theater_name,
           s.show_date, s.show_time
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    JOIN showtimes s ON b.showtime_id = s.showtime_id
    JOIN movies m ON s.movie_id = m.movie_id
    JOIN theaters t ON s.theater_id = t.theater_id
    ORDER BY b.booking_date DESC
");
$bookings = $stmt->fetchAll();
require_once 'includes/admin_header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>


    <div class="container mt-4">
        <h2>Manage Bookings</h2>
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>User</th>
                        <th>Movie</th>
                        <th>Theater</th>
                        <th>Show Date/Time</th>
                        <th>Seats</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Booking Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($bookings as $booking): ?>
                    <tr>
                        <td><?php echo $booking['booking_id']; ?></td>
                        <td>
                            <?php echo htmlspecialchars($booking['username']); ?><br>
                            <small><?php echo htmlspecialchars($booking['email']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($booking['movie_title']); ?></td>
                        <td><?php echo htmlspecialchars($booking['theater_name']); ?></td>
                        <td>
                            <?php echo date('Y-m-d', strtotime($booking['show_date'])); ?><br>
                            <?php echo date('h:i A', strtotime($booking['show_time'])); ?>
                        </td>
                        <td>
                            Seats: <?php echo $booking['seats_booked']; ?><br>
                            Numbers: <?php echo $booking['seat_numbers']; ?>
                        </td>
                        <td>₹<?php echo number_format($booking['total_amount'], 2); ?></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $booking['payment_method'])); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($booking['booking_date'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>