<?php
session_start();
require_once 'config/database.php';
require_once 'config/payment_config.php';

if(!isset($_SESSION['user_id']) || !isset($_POST['showtime_id'])) {
    header("Location: index.php");
    exit();
}

$showtime_id = $_POST['showtime_id'];
$num_tickets = $_POST['num_tickets'];

// Get showtime details
$stmt = $pdo->prepare("SELECT * FROM showtimes WHERE showtime_id = ?");
$stmt->execute([$showtime_id]);
$showtime = $stmt->fetch();

$total_amount = $showtime['price'] * $num_tickets;

// Generate unique transaction ID
$transaction_id = 'TXN' . time() . rand(100, 999);

// Insert booking record
$stmt = $pdo->prepare("
    INSERT INTO bookings (
        user_id, showtime_id, num_tickets, total_amount,
        transaction_id, payment_status, created_at
    ) VALUES (?, ?, ?, ?, ?, 'pending', NOW())
");

$stmt->execute([
    $_SESSION['user_id'],
    $showtime_id,
    $num_tickets,
    $total_amount,
    $transaction_id
]);

$booking_id = $pdo->lastInsertId();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - My Cinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Demo Payment Gateway</h3>
                    </div>
                    <div class="card-body">
                        <form id="paymentForm" action="complete_payment.php" method="POST">
                            <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                            <input type="hidden" name="amount" value="<?php echo $total_amount; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Card Number</label>
                                <input type="text" class="form-control" maxlength="16" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" placeholder="MM/YY" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CVV</label>
                                    <input type="text" class="form-control" maxlength="3" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h5>Amount to Pay: ₹<?php echo number_format($total_amount, 2); ?></h5>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Pay Now</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Simulate payment processing
            setTimeout(() => {
                this.submit();
            }, 1500);
        });
    </script>
</body>
</html>