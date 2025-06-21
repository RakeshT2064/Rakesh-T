<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['booking_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("
    SELECT b.*, m.title, m.poster_url, t.name as theater_name, s.show_date, s.show_time,
           GROUP_CONCAT(COALESCE(bs.seat_number, 'N/A')) as seats
    FROM bookings b
    JOIN showtimes s ON b.showtime_id = s.showtime_id
    JOIN movies m ON s.movie_id = m.movie_id
    JOIN theaters t ON s.theater_id = t.theater_id
    LEFT JOIN booking_seats bs ON b.booking_id = bs.booking_id
    WHERE b.booking_id = ? AND b.user_id = ?
    GROUP BY b.booking_id
");
$stmt->execute([$_GET['booking_id'], $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    header("Location: my_bookings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ticket - My Cinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/logo.png" href="images/logo.png">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .ticket {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .ticket-header {
            background: #dc3545;
            color: white;
            padding: 20px;
        }
        .movie-poster {
            max-height: 300px;
            object-fit: cover;
            border-radius: 10px;
        }
        .qr-code {
            text-align: center;
            padding: 20px;
        }
        .qr-code img {
            max-width: 150px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .ticket {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <?php require_once 'includes/headers.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="ticket">
                    <div class="ticket-header">
                        <h2 class="text-center mb-0">Movie Ticket</h2>
                    </div>
                    <div class="row p-4">
                        <div class="col-md-4">
                            <img src="<?php echo htmlspecialchars($booking['poster_url']); ?>" 
                                 class="movie-poster img-fluid" 
                                 alt="<?php echo htmlspecialchars($booking['title']); ?>">
                        </div>
                        <div class="col-md-8">
                            <h3><?php echo htmlspecialchars($booking['title']); ?></h3>
                            <hr>
                            <p><strong>Booking ID:</strong> <?php echo htmlspecialchars($booking['booking_id']); ?></p>
                            <p><strong>Theater:</strong> <?php echo htmlspecialchars($booking['theater_name']); ?></p>
                            <p><strong>Date:</strong> <?php echo date('F d, Y', strtotime($booking['show_date'])); ?></p>
                            <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($booking['show_time'])); ?></p>
                            <p><strong>Seats:</strong> <?php echo htmlspecialchars($booking['seat_numbers']); ?></p>
                            <p><strong>Amount Paid:</strong> ₹<?php echo number_format($booking['total_amount'], 2); ?></p>
                        </div>
                        <div class="qr-code">
                        <img src="images/qrrr.png" width="150px" height="125px"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4 mb-5 no-print">
            <button onclick="window.print()" class="btn btn-primary me-2">
                <i class="bi bi-printer"></i> Print Ticket
            </button>
            <a href="my_bookings.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to My Bookings
            </a>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>