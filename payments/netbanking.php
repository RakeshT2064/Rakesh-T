<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['booking_id']) || !isset($_SESSION['booking_data'])) {
    header("Location: ../index.php");
    exit();
}

$booking_data = $_SESSION['booking_data'];

$banks = [
    'sbi' => 'State Bank of India',
    'hdfc' => 'HDFC Bank',
    'icici' => 'ICICI Bank',
    'axis' => 'Axis Bank',
    'pnb' => 'Punjab National Bank',
    'bob' => 'Bank of Baroda'
];

if(isset($_POST['process_payment'])) {
    $bank = $_POST['bank'];
    
    $stmt = $pdo->prepare("UPDATE bookings SET payment_status = 'completed', transaction_id = ? WHERE booking_id = ?");
    $stmt->execute([uniqid('NB'), $_SESSION['booking_id']]);
    
    header("Location: ../booking_confirmation.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Net Banking Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
    body {
        background: #f1f3f6;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .card {
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-radius: 15px;
    }

    .card-body {
        padding: 2rem;
    }

    .card-title {
        font-weight: 600;
        font-size: 1.8rem;
        margin-bottom: 1rem;
        color: #333;
    }

    .alert-info {
        background-color: #e7f3fe;
        color: #31708f;
        font-size: 1.1rem;
        font-weight: 500;
    }

    .form-label {
        font-weight: 500;
        color: #444;
    }

    .form-select {
        border-radius: 8px;
        height: 45px;
        font-size: 1rem;
    }

    .alert-warning {
        background-color: #fff3cd;
        color: #856404;
        font-size: 0.95rem;
        border-radius: 8px;
    }

    .btn-primary {
        background-color: #0056b3;
        border-color: #0056b3;
        border-radius: 8px;
        font-size: 1rem;
        padding: 0.6rem;
    }

    .btn-primary:hover {
        background-color: #004494;
    }

    .btn-outline-secondary {
        border-radius: 8px;
        font-size: 1rem;
        padding: 0.6rem;
    }

    img {
        max-width: 100%;
        height: auto;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            font-size: 1.5rem;
        }
    }
</style>

</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <center><img src="../images/net.jpeg" width="600px" height="150px"/></center>
                        <h3 class="card-title text-center">Net Banking</h3>
                        <div class="alert alert-info text-center">
                            Amount: ₹<?php echo number_format($booking_data['total_amount'], 2); ?>
                        </div>

                        <form method="POST">
                            <div class="mb-4">
                                <label class="form-label">Select Your Bank</label>
                                <select name="bank" class="form-select" required>
                                    <option value="">Choose your bank</option>
                                    <?php foreach($banks as $code => $name): ?>
                                        <option value="<?php echo $code; ?>"><?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="alert alert-warning">
                                <small>
                                    <i class="bi bi-info-circle"></i>
                                    You will be redirected to your bank's secure payment page.
                                </small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="process_payment" class="btn btn-primary">
                                    Continue to Pay
                                </button>
                                <a href="../confirm_booking.php" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>