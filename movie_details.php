<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$movie_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM movies WHERE movie_id = ?");
$stmt->execute([$movie_id]);
$movie = $stmt->fetch();

if (!$movie) {
    header("Location: index.php");
    exit();
}

// Fetch available showtimes for this movie
// After fetching movie details, add this code for dates
$stmt = $pdo->prepare("
    SELECT DISTINCT show_date 
    FROM showtimes 
    WHERE movie_id = ? AND show_date >= CURDATE()
    ORDER BY show_date
    LIMIT 7
");
$stmt->execute([$movie_id]);
$available_dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Get selected date (default to today if not specified)
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Update showtime query to filter by date and prevent duplicates
// Update the showtime query
$stmt = $pdo->prepare("
    SELECT s.showtime_id, s.show_date, s.show_time, s.price, t.name as theater_name 
    FROM showtimes s
    JOIN theaters t ON s.theater_id = t.theater_id
    WHERE s.movie_id = ? 
    AND s.show_date = ?
    AND CONCAT(s.show_date, ' ', s.show_time) > NOW()
    ORDER BY s.show_time ASC
");
$stmt->execute([$movie_id, $selected_date]);
$showtimes = $stmt->fetchAll();
require_once 'includes/headers.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['title']); ?> - Movie Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/logo.png" href="images/logo.png">
<style>
    
    body {
    background-color:rgb(0, 0, 0);
    color:rgb(255, 255, 255);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
h2, h3 {
    color:rgb(255, 255, 255);
}

.container {
    background-color:rgb(0, 0, 0);
    padding: 0rem;
    border-radius: 15px;
}

img.img-fluid {
    border-radius: 12px;
    max-height: 600px;
    object-fit: cover;
}

.lead {
    font-size: 1.1rem;
    color: #ccc;
}

.card {
    background-color:rgb(38, 36, 36);
    border: none;
    border-radius: 15px;
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: scale(1.03);
}

.card-title {
    color:rgb(255, 255, 255);
    font-weight: 600;
}

.card-text {
    color: #ddd;
    font-size: 0.95rem;
}

.btn-primary {
    background-color:rgb(208, 40, 40);
    color: #000;
    border: none;
    font-weight: bold;
    border-radius: 15px;
}

.btn-primary:hover {
    background-color:rgb(199, 38, 38);
    color: #000;
}

.btn-secondary {
    background-color: #444;
    border: none;
    color: #fff;
    font-weight: bold;
    border-radius: 8px;
}

.btn-secondary:hover {
    background-color: #666;
    color: #fff;
}

.mt-4 {
    margin-top: 1.5rem !important;
}
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($movie['title']); ?>">
            </div>
            <div class="col-md-8">
                <h2><?php echo htmlspecialchars($movie['title']); ?></h2>
                <p class="lead"><b><?php echo htmlspecialchars($movie['description']); ?></b></p>
                <div class="movie-info">
                    <p><i class="bi bi-clock"></i> Duration: 
                        <?php 
                            $hours = floor($movie['duration'] / 60);
                            $minutes = $movie['duration'] % 60;
                            echo $hours . 'h ' . $minutes . 'm';
                        ?>
                    </p>
                    <p><i class="bi bi-calendar"></i> Release Date: <?php echo date('F d, Y', strtotime($movie['release_date'])); ?></p>

                    <h3 class="mt-4">Available Showtimes</h3>
                    <?php if(!empty($available_dates)): ?>
                        <div class="dates-wrapper mb-4">
                            <div class="btn-group">
                                <?php foreach($available_dates as $date): ?>
                                    <a href="?id=<?php echo $movie_id; ?>&date=<?php echo $date; ?>" 
                                       class="btn <?php echo $date === $selected_date ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                        <?php echo date('D, M d', strtotime($date)); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <?php if(empty($showtimes)): ?>
                            <p>No showtimes available for this date.</p>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach($showtimes as $showtime): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($showtime['theater_name']); ?></h5>
                                                <p class="card-text">
                                                    Time: <?php echo date('h:i A', strtotime($showtime['show_time'])); ?><br>
                                                    Price: ₹<?php echo number_format($showtime['price'], 2); ?>
                                                </p>
                                                <?php if(isset($_SESSION['user_id'])): ?>
                                                    <a href="book_tickets.php?showtime_id=<?php echo $showtime['showtime_id']; ?>" 
                                                       class="btn btn-primary">Book Now</a>
                                                <?php else: ?>
                                                    <a href="login.php" class="btn btn-secondary">Login to Book</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>No showtimes available at the moment.</p>
                    <?php endif; ?>

<!-- Add this CSS to your existing style section -->
<style>
    .dates-wrapper {
        overflow-x: auto;
        padding: 10px 0;
    }
    .btn-group {
        flex-wrap: nowrap;
    }
    .btn-group .btn {
        min-width: 120px;
        margin: 0 2px;
    }
    .btn-outline-primary {
        color: #fff;
        border-color: #dc3545;
    }
    .btn-outline-primary:hover {
        background-color: #dc3545;
        border-color: #dc3545;
    }
</style>
            </div>
        </div>
    </div>

   
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
 <?php require_once 'includes/footer.php'; ?>
</body>
</html>
