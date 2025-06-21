<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['booking_data'])) {
    header("Location: ../index.php");
    exit();
}

$booking_data = $_SESSION['booking_data'];

// Update the payment processing section at the top of the file
if(isset($_POST['confirm_payment'])) {
    $upi_id = $_POST['upi_transaction_id'];
    $transaction_id = uniqid('UPI');
    
    try {
        $stmt = $pdo->prepare("UPDATE bookings SET payment_status = 'completed', transaction_id = ? WHERE booking_id = ?");
        if($stmt->execute([$transaction_id, $_SESSION['booking_id']])) {
            // Store transaction details in session
            $_SESSION['transaction_id'] = $transaction_id;
            $_SESSION['payment_status'] = 'completed';
            
            // Clear booking session data after successful payment
            unset($_SESSION['booking_data']);
            
            // Redirect to confirmation page
            header("Location: ../booking_confirmation.php?success=1");
            exit();
        }
    } catch (PDOException $e) {
        $error = "Payment processing failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UPI Payment - My Cinema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <center><img src="../images/upis.png" width="300px" height="200px" alt="UPI Payment"/></center>
                        <h3 class="card-title text-center mb-4">UPI Payment</h3>
                        
                        <div class="text-center mb-4">
                            <h5>Amount to Pay: ₹<?php echo number_format($booking_data['total_amount'], 2); ?></h5>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6 text-center">
                                <h6>Scan QR Code</h6>
                                <img src="../images/qrr1.jpg" alt="UPI QR Code" class="img-fluid mb-2" style="max-width: 200px;">
                                <p class="small text-muted">Scan with any UPI app</p>
                            </div>
                            <div class="col-md-6 text-center">
                                <h6>Or Pay using UPI ID</h6>
                                <div class="upi-id-box p-2 mb-2 bg-light rounded">
                                    <span class="fw-bold">mycinema@upi</span>
                                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyUpiId()">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                                <p class="small text-muted">Pay using any UPI app</p>
                                <h6>Phonepay</h6>
                                <h6>Google pay</h6>
                                <h6>Paytm</h6>
                                <h6>Amazon pay</h6>
                                <h6>Paypal</h6>
                            </div>
                        </div>

                        <form method="POST" id="upiForm" class="mt-4">
                            <div class="mb-3">
                                <label class="form-label">Enter UTR Number</label>
                                <input type="text" class="form-control" name="upi_transaction_id" 
                                       required placeholder="xxxxxxxxxxxx">
                            </div>

                            <?php if(isset($error)): ?>
                                <div class="alert alert-danger mb-3">
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>

                            <div class="d-grid gap-2">
                                <button type="submit" name="confirm_payment" class="btn btn-primary">
                                    Confirm Payment
                                </button>
                                <a href="../confirm_booking.php" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function copyUpiId() {
        navigator.clipboard.writeText('mycinema@upi')
            .then(() => {
                alert('UPI ID copied to clipboard!');
            })
            .catch(err => {
                console.error('Failed to copy UPI ID:', err);
            });
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>