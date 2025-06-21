<?php
session_start();

if (!isset($_SESSION['booking_success'])) {
    header("Location: index.php");
    exit();
}

unset($_SESSION['booking_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Successful - Movie Booking System</title>
    <link rel="icon" type="image/logo.png" href="images/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="card">
                    <div class="card-body">
                        <h1 class="text-success mb-4">✓</h1>
                        <h2>Booking Successful!</h2>
                        <p class="mb-4">Your tickets have been booked successfully.</p>
                        <div>
                            <a href="my_bookings.php" class="btn btn-primary">View My Bookings</a>
                            <a href="index.php" class="btn btn-secondary">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>