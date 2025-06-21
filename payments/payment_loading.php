<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['booking_id']) || !isset($_SESSION['booking_data'])) {
    header("Location: ../index.php");
    exit();
}

$booking_data = $_SESSION['booking_data'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, rgb(243, 243, 243), rgb(243, 243, 243));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .loading-container {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
        .spinner-border {
            width: 4rem;
            height: 4rem;
            margin-bottom: 1rem;
        }
        .amount {
            font-size: 1.5rem;
            font-weight: bold;
            color: #0275d8;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="loading-container">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h3>Processing Your Payment</h3>
            <p>Please do not close or refresh this page</p>
            <div class="amount">
                Amount: ₹<?php echo number_format($booking_data['total_amount'], 2); ?>
            </div>
            <div class="progress" style="height: 10px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <script>
        // Simulate payment processing
        let progress = 0;
        const progressBar = document.querySelector('.progress-bar');
        
        const interval = setInterval(() => {
            progress += 5;
            progressBar.style.width = progress + '%';
            
            if (progress >= 100) {
                clearInterval(interval);
                window.location.href = '../booking_confirmation.php?success=1';
            }
        }, 150);
    </script>
</body>
</html>