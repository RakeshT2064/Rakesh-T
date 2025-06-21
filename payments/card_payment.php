<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['booking_id']) || !isset($_SESSION['booking_data'])) {
    header("Location: ../index.php");
    exit();
}

$booking_data = $_SESSION['booking_data'];

// Add this at the top after $booking_data declaration
if (isset($_POST['process_payment'])) {
    $errors = [];
    $card_number = preg_replace('/\s+/', '', $_POST['card_number']);
    $expiry_date = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];
    $card_holder = $_POST['card_holder'];

    // Validate card details
    if (!preg_match('/^[0-9]{16}$/', $card_number)) {
        $errors[] = "Invalid card number";
    }

    // Validate expiry date (must be after 2025)
    $expiry_parts = explode('/', $expiry_date);
    if (count($expiry_parts) == 2) {
        $exp_year = '20' . $expiry_parts[1];
        $exp_month = $expiry_parts[0];
        if ($exp_year < 2025) {
            $errors[] = "Card expired. Please use a card valid after 2025";
        }
    } else {
        $errors[] = "Invalid expiry date format";
    }

    if (!preg_match('/^[0-9]{3}$/', $cvv)) {
        $errors[] = "Invalid CVV";
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Update booking status
            $stmt = $pdo->prepare("UPDATE bookings SET payment_status = 'completed', payment_date = NOW() WHERE booking_id = ?");
            $stmt->execute([$_SESSION['booking_id']]);
            
            $pdo->commit();
            
            $_SESSION['success_message'] = "Payment successful!";
            header("Location: ../booking_confirmation.php");
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Payment failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
    body {
        background: linear-gradient(to right,rgb(243, 243, 243),rgb(243, 243, 243));
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .container {
        max-width: 1500px;
    }

    .card {
        background-color: #ffffff;
        border-radius: 16px;
        border: none;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.78);
        overflow: hidden;
    }

    .card-body {
        padding: 2rem;
    }

    h3.card-title {
        color: #2c3e50;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
    }

    .form-control {
        border-radius: 10px;
        border: 1px solid #ced4da;
        padding: 10px;
        transition: border-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0,123,255,0.4);
    }

    .alert-info {
        background-color: #e8f4fd;
        color: #0275d8;
        border: none;
        border-radius: 10px;
        font-weight: 600;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
        border-radius: 10px;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-outline-secondary {
        border-radius: 10px;
        font-weight: bold;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: #fff;
    }

    img {
        margin-bottom: 1rem;
        border-radius: 10px;
    }

    @media (max-width: 576px) {
        .card-body {
            padding: 1.5rem;
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
                        <center><img src="../images/card.png" width="650px" height="150px" alt="Card Payment"/></center>
                        <h3 class="card-title text-center">Card Payment</h3>
                        <div class="alert alert-info text-center">
                            Amount to Payable: ₹<?php echo number_format($booking_data['total_amount'], 2); ?>
                        </div>

                            <form method="POST" id="cardForm" action="">
                            <div class="mb-3">
                                <label class="form-label">Card Number</label>
                                <input type="text" class="form-control" required 
                                       name="card_number"
                                       placeholder="1234 5678 9012 3456" maxlength="19"
                                       id="cardNumber" onkeypress="return isNumberKey(event)"
                                       onkeyup="formatCardNumber(this)">
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" required 
                                           name="expiry_date"
                                           placeholder="MM/YY" maxlength="5"
                                           id="expiryDate" onkeypress="return isNumberKey(event)"
                                           onkeyup="formatExpiryDate(this)">
                                </div>
                                <div class="col">
                                    <label class="form-label">CVV</label>
                                    <input type="password" class="form-control" required 
                                           name="cvv"
                                           placeholder="123" maxlength="3"
                                           onkeypress="return isNumberKey(event)">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Card Holder Name</label>
                                <input type="text" class="form-control" required 
                                       name="card_holder"
                                       placeholder="Name on card"
                                       pattern="[A-Za-z ]+"
                                       title="Please enter letters only"
                                       onkeypress="return isLetterKey(event)"
                                       onpaste="return false">
                            </div>

                            <!-- Add error message display -->
                            <?php if(isset($error)): ?>
                                <div class="alert alert-danger mb-3">
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>

                            <div class="d-grid gap-2">
                            <button type="submit" name="process_payment" class="btn btn-primary">
                    Process Payment
                </button>
                                <a href="booking_confirmation.php" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }

    function isLetterKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if ((charCode > 64 && charCode < 91) || (charCode > 96 && charCode < 123) || charCode == 32) {
            return true;
        }
        return false;
    }

    function formatCardNumber(input) {
        var value = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        var matches = value.match(/\d{4,16}/g);
        var match = matches && matches[0] || '';
        var parts = [];

        for (let i = 0, len = match.length; i < len; i += 4) {
            parts.push(match.substring(i, i + 4));
        }

        if (parts.length) {
            input.value = parts.join(' ');
        }
    }

    function formatExpiryDate(input) {
        var value = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        if (value.length > 2) {
            input.value = value.substring(0, 2) + '/' + value.substring(2);
        }
    }

    document.getElementById('cardForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic validation
        var cardNumber = document.getElementById('cardNumber').value.replace(/\s+/g, '');
        var expiryDate = document.getElementById('expiryDate').value;
        
        if (cardNumber.length !== 16) {
            alert('Please enter a valid 16-digit card number');
            return;
        }
        
        if (!expiryDate.match(/^(0[1-9]|1[0-2])\/([0-9]{2})$/)) {
            alert('Please enter a valid expiry date (MM/YY)');
            return;
        }

        // If validation passes, submit the form
        this.submit();
    });
    </script>
</body>
</html>