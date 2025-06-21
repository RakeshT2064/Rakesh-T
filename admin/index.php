<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$page_title = "Admin Dashboard";

// Get dashboard statistics
$stats = [
    'movies' => $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn(),
    'theaters' => $pdo->query("SELECT COUNT(*) FROM theaters")->fetchColumn(),
    'showtimes' => $pdo->query("SELECT COUNT(*) FROM showtimes")->fetchColumn(),
    'total_income' => $pdo->query("SELECT SUM(total_amount) FROM bookings")->fetchColumn(),
    'today_income' => $pdo->query("SELECT SUM(total_amount) FROM bookings WHERE DATE(booking_date) = CURDATE()")->fetchColumn(),
    'monthly_income' => $pdo->query("SELECT SUM(total_amount) FROM bookings WHERE MONTH(booking_date) = MONTH(CURDATE()) AND YEAR(booking_date) = YEAR(CURDATE())")->fetchColumn()
];

// Fetch all movies
$stmt = $pdo->query("SELECT * FROM movies ORDER BY movie_id DESC");
$movies = $stmt->fetchAll();

require_once 'includes/admin_header.php';
?>
<html>
    <head>
        <title>Admin </title>
        <link rel="icon" type="image/logo.png" href="images/logo.png">
</head>
<!-- Dashboard Summary Cards -->
<div class="row mb-4">
    <!-- ... existing dashboard cards ... -->
</div>

<!-- Income Summary Cards -->
<div class="row mb-4">
    <!-- ... existing income cards ... -->
</div>
<div class="container mt-4">
        <!-- Dashboard Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Movies</h5>
                        <h2 class="card-text"><?php echo $stats['movies']; ?></h2>
                        <i class="bi bi-film position-absolute top-50 end-0 me-3 opacity-50" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Theaters</h5>
                        <h2 class="card-text"><?php echo $stats['theaters']; ?></h2>
                        <i class="bi bi-building position-absolute top-50 end-0 me-3 opacity-50" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Showtimes</h5>
                        <h2 class="card-text"><?php echo $stats['showtimes']; ?></h2>
                        <i class="bi bi-clock position-absolute top-50 end-0 me-3 opacity-50" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Income</h5>
                        <h2 class="card-text">₹<?php echo number_format($stats['total_income'], 2); ?></h2>
                        <i class="bi bi-cash-stack position-absolute top-50 end-0 me-3 opacity-50" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Income Summary -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Today's Income</h5>
                        <h3 class="text-success">₹<?php echo number_format($stats['today_income'], 2); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">This Month's Income</h5>
                        <h3 class="text-success">₹<?php echo number_format($stats['monthly_income'], 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>

<!-- Movies Management Section -->


<?php require_once 'includes/admin_footer.php'; ?>
</html>