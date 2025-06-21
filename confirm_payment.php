<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking_data'])) {
    header("Location: index.php");
    exit();
}

$booking_data = $_SESSION['booking_data'];

if (isset($_POST['confirm_payment'])) {
    $payment_method = $_POST['payment_method'];
    $seat_numbers = implode(',', $booking_data['seats']);
    
    try {
        $pdo->beginTransaction();
        
        // Insert booking record
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, showtime_id, seats_booked, seat_numbers, total_amount, payment_method, booking_date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $_SESSION['user_id'],
            $booking_data['showtime_id'],
            count($booking_data['seats']),
            $seat_numbers,
            $booking_data['total_amount'],
            $payment_method
        ]);
        
        $pdo->commit();
        unset($_SESSION['booking_data']);
        
        $_SESSION['booking_success'] = true;
        header("Location: booking_success.php");
        exit();
    } catch(PDOException $e) {
        $pdo->rollBack();
        $error = "Payment failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Payment - Movie Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Confirm Payment</h3>
                    </div>
                    <div class="card-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <div class="booking-details mb-4">
                            <h4><?php echo htmlspecialchars($booking_data['movie_title']); ?></h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Theater:</strong> <?php echo htmlspecialchars($booking_data['theater_name']); ?></p>
                                    <p><strong>Date:</strong> <?php echo date('F d, Y', strtotime($booking_data['show_date'])); ?></p>
                                    <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($booking_data['show_time'])); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Selected Seats:</strong> <?php echo implode(', ', $booking_data['seats']); ?></p>
                                    <p><strong>Total Amount:</strong> $<?php echo number_format($booking_data['total_amount'], 2); ?></p>
                                </div>
                            </div>
                        </div>

                        <form method="POST" id="paymentForm">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Select Payment Method</label>
                                <select class="form-control" id="payment_method" name="payment_method" required>
                                    <option value="">Choose payment method</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="debit_card">Debit Card</option>
                                    <option value="e_wallet">E-Wallet</option>
                                </select>
                            </div>

                            <div id="card_details" class="mb-3" style="display: none;">
                                <div class="mb-3">
                                    <label for="card_number" class="form-label">Card Number</label>
                                    <input type="text" class="form-control" id="card_number" placeholder="1234 5678 9012 3456">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="expiry" class="form-label">Expiry Date</label>
                                        <input type="text" class="form-control" id="expiry" placeholder="MM/YY">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="cvv" placeholder="123">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" name="confirm_payment" class="btn btn-primary">Confirm and Pay</button>
                                <a href="book_tickets.php?showtime_id=<?php echo $booking_data['showtime_id']; ?>" class="btn btn-secondary">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('payment_method').addEventListener('change', function() {
            const cardDetails = document.getElementById('card_details');
            if (this.value === 'credit_card' || this.value === 'debit_card') {
                cardDetails.style.display = 'block';
            } else {
                cardDetails.style.display = 'none';
            }
        });
    </script>
</body>
</html>