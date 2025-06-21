// After successful booking, before redirect
require_once 'includes/email_helper.php';

// Fetch complete booking details
$stmt = $pdo->prepare("
    SELECT b.*, m.title, t.name as theater_name, s.show_date, s.show_time,
           GROUP_CONCAT(bs.seat_number) as seats
    FROM bookings b
    JOIN showtimes s ON b.showtime_id = s.showtime_id
    JOIN movies m ON s.movie_id = m.movie_id
    JOIN theaters t ON s.theater_id = t.theater_id
    JOIN booking_seats bs ON b.booking_id = bs.booking_id
    WHERE b.booking_id = ?
    GROUP BY b.booking_id
");
$stmt->execute([$booking_id]);
$bookingDetails = $stmt->fetch();

// Send email
sendTicketEmail($_SESSION['email'], $bookingDetails);

// After successful booking
if($success) {
    require_once 'includes/email_helper.php';
    $emailSent = sendTicketEmail($userEmail, $bookingDetails);
    if(!$emailSent) {
        error_log("Failed to send email for booking ID: " . $booking_id);
    }
}