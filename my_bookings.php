<?php
session_start();
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Test database connection
    if (!$pdo) {
        throw new PDOException("Database connection failed");
    }

    // Updated query with all required columns
    // Update the SQL query to use poster_url instead of poster
    $stmt = $pdo->prepare("
        SELECT b.*, m.title, m.poster_url as poster, t.name as theater_name, 
               s.show_date, s.show_time
        FROM bookings b
        JOIN showtimes s ON b.showtime_id = s.showtime_id
        JOIN movies m ON s.movie_id = m.movie_id
        JOIN theaters t ON s.theater_id = t.theater_id
        WHERE b.user_id = ?
    ");
    
    if (!$stmt->execute([$_SESSION['user_id']])) {
        throw new PDOException("Failed to execute query");
    }
    
    // Print actual error if any
    if ($stmt->errorInfo()[2]) {
        throw new PDOException($stmt->errorInfo()[2]);
    }
    
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Log the actual error message
    error_log("Database Error in my_bookings.php: " . $e->getMessage());
    $_SESSION['error'] = "Database Error: " . $e->getMessage();
    $bookings = [];
}

// Add this temporary debug code at the top of the HTML section
if (isset($_SESSION['error'])) {
    echo "<pre>";
    error_log("Error in my_bookings.php: " . $_SESSION['error']);
    echo "</pre>";
}
require_once 'includes/headers.php';

// Add this function at the top of the file after database connection
function isShowtimePassed($showtime) {
    $showtime_datetime = new DateTime($showtime);
    $current_datetime = new DateTime();
    return $current_datetime > $showtime_datetime;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="icon" type="image/logo.png" href="images/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .booking-card {
            transition: transform 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .movie-poster {
            width: 120px;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
        }
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .booking-date {
            color: #666;
            font-size: 0.9rem;
        }
        .ticket-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        .icon-text {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .icon-text i {
            font-size: 1.2rem;
            color: #0d6efd;
        }
    </style>
</head>
<body class="bg-light">

    <div class="container py-4">
        <h2>My Bookings</h2>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php 
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php 
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (empty($bookings)): ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-ticket-perforated" style="font-size: 4rem; color: #6c757d;"></i>
                </div>
                <h3>No Bookings Found</h3>
                <p class="text-muted mb-4">You haven't made any bookings yet. Start exploring movies and book your tickets now!</p>
                <a href="movies.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-film"></i> Browse Movies
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
            <div class="card booking-card mb-3">
                <div class="row g-0">
                    <div class="col-md-2">
                        <img src="<?php echo htmlspecialchars($booking['poster']); ?>" class="movie-poster" alt="Movie Poster">
                    </div>
                    <div class="col-md-10">
                        <div class="card-body">
                            <div class="status-badge">
                                <?php if ($booking['status'] == 'cancelled'): ?>
                                    <span class="badge bg-danger">Cancelled</span>
                                <?php elseif ($booking['payment_status'] == 'pending'): ?>
                                    <span class="badge bg-warning">Payment Pending</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Confirmed</span>
                                <?php endif; ?>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($booking['title']); ?></h5>
                            <div class="ticket-details">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="icon-text"><i class="bi bi-building"></i> <?php echo htmlspecialchars($booking['theater_name']); ?></p>
                                        <p class="icon-text"><i class="bi bi-calendar"></i> <?php echo date('F d, Y', strtotime($booking['show_date'])); ?></p>
                                        <p class="icon-text"><i class="bi bi-clock"></i> <?php echo date('h:i A', strtotime($booking['show_time'])); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="icon-text"><i class="bi bi-ticket-perforated"></i> Seats: <?php echo htmlspecialchars($booking['seat_numbers']); ?></p>
                                        <p class="icon-text"><i class="bi bi-currency-rupee"></i> Amount: ₹<?php echo number_format($booking['total_amount'], 2); ?></p>
                                        <?php 
                                            $show_datetime = strtotime($booking['show_date'] . ' ' . $booking['show_time']);
                                            $current_time = time();
                                            $time_difference = $show_datetime - $current_time;
                                            $three_hours = 3 * 60 * 60; // 3 hours in seconds
                                            
                                            if ($booking['status'] != 'cancelled' && $time_difference > $three_hours && $current_time < $show_datetime): 
                                        ?>
                                            <form method="POST" action="cancel_ticket.php" onsubmit="return confirm('Are you sure you want to cancel this booking?');" class="d-inline">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                                <button type="submit" name="cancel_booking" class="btn btn-danger btn-sm me-2">
                                                    <i class="bi bi-x-circle"></i> Cancel Booking
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <a href="view_ticket.php?booking_id=<?php echo $booking['booking_id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="bi bi-ticket-detailed"></i> View Ticket
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>