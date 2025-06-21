<?php
session_start();
require_once 'config/database.php';

// Get selected date or default to today
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Get available dates with showtimes (next 7 days)
$dates = [];
for ($i = 0; $i < 7; $i++) {
    $date = date('Y-m-d', strtotime("+$i days"));
    $dates[] = $date;
}

// Fetch showtimes for selected date
$stmt = $pdo->prepare("
    SELECT s.*, m.title, m.poster_url, m.duration, m.movie_type, t.name as theater_name, t.seating_capacity
    FROM showtimes s
    JOIN movies m ON s.movie_id = m.movie_id
    JOIN theaters t ON s.theater_id = t.theater_id
    WHERE DATE(s.show_date) = ?
    ORDER BY m.title, s.show_time
");
$stmt->execute([$selected_date]);
$showtimes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group showtimes by movie
$movies_showtimes = [];
foreach ($showtimes as $showtime) {
    $movies_showtimes[$showtime['movie_id']][] = $showtime;
}

require_once 'includes/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">Movie Showtimes</h2>
    
    <!-- Date Navigation -->
    <div class="date-nav mb-4">
        <div class="btn-group">
            <?php foreach ($dates as $date): ?>
                <a href="?date=<?php echo $date; ?>" 
                   class="btn btn-outline-primary <?php echo $date === $selected_date ? 'active' : ''; ?>">
                    <?php echo date('D, M d', strtotime($date)); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (empty($movies_showtimes)): ?>
        <div class="alert alert-info">
            No showtimes available for this date.
        </div>
    <?php else: ?>
        <?php foreach ($movies_showtimes as $movie_showtimes): 
            $movie = $movie_showtimes[0]; // Get movie details from first showtime
        ?>
            <div class="card mb-4">
                <div class="row g-0">
                    <div class="col-md-2">
                        <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" 
                             class="img-fluid rounded-start" 
                             alt="<?php echo htmlspecialchars($movie['title']); ?>"
                             style="height: 300px; object-fit: cover;">
                    </div>
                    <div class="col-md-10">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <?php echo $movie['duration']; ?> mins | <?php echo $movie['movie_type']; ?>
                                </small>
                            </p>
                            
                            <div class="showtimes mt-3">
                                <?php foreach ($movie_showtimes as $showtime): ?>
                                    <div class="showtime-item mb-3">
                                        <h6 class="theater-name"><?php echo htmlspecialchars($showtime['theater_name']); ?></h6>
                                        <a href="booking.php?showtime_id=<?php echo $showtime['showtime_id']; ?>" 
                                           class="btn btn-outline-primary">
                                            <?php echo date('h:i A', strtotime($showtime['show_time'])); ?>
                                            - ₹<?php echo number_format($showtime['price'], 2); ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.date-nav {
    overflow-x: auto;
    white-space: nowrap;
    padding-bottom: 10px;
}
.showtime-item {
    display: inline-block;
    margin-right: 20px;
}
.theater-name {
    font-size: 0.9rem;
    margin-bottom: 5px;
}
</style>

<?php require_once 'includes/footer.php'; ?>