<?php
session_start();
require_once 'config/database.php';

// Debug session data
if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking_data'])) {
    echo "<pre>Debug: Session data missing\n";
    echo "user_id: " . (isset($_SESSION['user_id']) ? 'set' : 'not set') . "\n";
    echo "booking_data: " . (isset($_SESSION['booking_data']) ? 'set' : 'not set') . "</pre>";
    exit();
}


if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking_data'])) {
    header("Location: index.php");
    exit();
}

$booking_data = $_SESSION['booking_data'];

if (isset($_POST['confirm'])) {
    $payment_method = $_POST['payment_method'];
    $seat_numbers = implode(',', $booking_data['seats']);
    
    try {
        $pdo->beginTransaction();
        
        // Insert booking record
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, showtime_id, seats_booked, seat_numbers, total_amount, payment_method, payment_status, booking_date, num_tickets) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW(), ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $booking_data['showtime_id'],
            count($booking_data['seats']),
            $seat_numbers,
            $booking_data['total_amount'],
            $payment_method,
            count($booking_data['seats'])  // num_tickets is same as seats_booked
        ]);
        
        $booking_id = $pdo->lastInsertId();
        $_SESSION['booking_id'] = $booking_id;
        
        $pdo->commit();
        
        // Redirect to appropriate payment method
        switch($payment_method) {
            case 'upi':
                header("Location: payments/upi_payment.php");
                break;
            case 'card':
                header("Location: payments/card_payment.php");
                break;
            case 'netbanking':
                header("Location: payments/netbanking.php");
                break;
        }
        exit();
    } catch(PDOException $e) {
        $pdo->rollBack();
        $error = "Booking failed. Error: " . $e->getMessage(); // Add detailed error message
        error_log("Booking Error: " . $e->getMessage()); // Log the error
    }
}
require_once 'includes/headers.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Movie Booking System</title>
    <link rel="icon" type="image/logo.png" href="images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .payment-option {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-option:hover {
            border-color:rgb(253, 13, 13);
            background-color:rgb(255, 253, 253);
        }
        .payment-option.selected {
            border-color:rgba(0, 207, 239, 0.94);
            background-color: #f8f9fa;
        }
        .payment-icon {
            font-size: 2rem;
            margin-right: 15px;
        }
        .timer {
            font-size: 1.2rem;
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <center><img src="images/upis.png" width="250px" height="200px"/></center>
                <h2>Select Payment Method</h2>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" id="paymentForm">
                    <input type="hidden" name="payment_method" id="payment_method">
                    
                    <!-- UPI Payment -->
                    <div class="payment-option" data-payment="upi">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-phone payment-icon"></i>
                            <div>
                                <h5 class="mb-1">UPI Payment</h5>
                                <p class="mb-0 text-muted">Pay using Google Pay, PhonePe, or any UPI app</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card Payment -->
                    <div class="payment-option" data-payment="card">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-credit-card payment-icon"></i>
                            <div>
                                <h5 class="mb-1">Credit/Debit Card</h5>
                                <p class="mb-0 text-muted">Pay using Visa, MasterCard, or RuPay</p>
                            </div>
                        </div>
                    </div>

                    <!-- Net Banking -->
                    <div class="payment-option" data-payment="netbanking">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-bank payment-icon"></i>
                            <div>
                                <h5 class="mb-1">Net Banking</h5>
                                <p class="mb-0 text-muted">Pay using your bank account</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" name="confirm" class="btn btn-primary btn-lg" disabled>
                            Proceed to Pay ₹<?php echo number_format($booking_data['total_amount'], 2); ?>
                        </button>
                        <a href="book_tickets.php?showtime_id=<?php echo $booking_data['showtime_id']; ?>" 
                           class="btn btn-outline-secondary btn-lg">Back</a>
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Booking Summary</h4>
                        <div class="timer mb-3">
                            Time remaining: <span id="countdown">10:00</span>
                        </div>
                        <hr>
                        <h5><?php echo htmlspecialchars($booking_data['movie_title']); ?></h5>
                        <p class="mb-1">
                            <i class="bi bi-building"></i> 
                            <?php echo htmlspecialchars($booking_data['theater_name']); ?>
                        </p>
                        <p class="mb-1">
                            <i class="bi bi-calendar"></i>
                            <?php echo date('F d, Y', strtotime($booking_data['show_date'])); ?>
                        </p>
                        <p class="mb-1">
                            <i class="bi bi-clock"></i>
                            <?php echo date('h:i A', strtotime($booking_data['show_time'])); ?>
                        </p>
                        <p class="mb-1">
                            <i class="bi bi-chair"></i>
                            Seats: <?php echo implode(', ', $booking_data['seats']); ?>
                        </p>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total Amount:</strong>
                            <strong>₹<?php echo number_format($booking_data['total_amount'], 2); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Payment option selection
        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
                document.getElementById('payment_method').value = this.dataset.payment;
                document.querySelector('button[name="confirm"]').disabled = false;
            });
        });

        // Countdown timer
        let timeLeft = 600; // 10 minutes in seconds
        const countdownDisplay = document.getElementById('countdown');
        
        const countdownTimer = setInterval(function() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(countdownTimer);
                window.location.href = 'index.php?timeout=1';
            }
            timeLeft--;
        }, 1000);

    </script>
</body>
<?php require_once 'includes/footer.php'; ?>
</html>