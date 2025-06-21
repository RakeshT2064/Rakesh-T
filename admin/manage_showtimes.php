<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch movies and theaters for the dropdown
$movies = $pdo->query("SELECT * FROM movies")->fetchAll();
$theaters = $pdo->query("SELECT * FROM theaters")->fetchAll();

// Modify the movie query to only fetch released movies
$stmt = $pdo->query("SELECT * FROM movies WHERE release_date <= CURRENT_DATE() ORDER BY title");
$movies = $stmt->fetchAll();

// Process form submission
if(isset($_POST['add_showtime'])) {
    $movie_id = $_POST['movie_id'];
    $theater_id = $_POST['theater_id'];
    $show_date = $_POST['show_date'];
    $show_time = $_POST['show_time'];
    $price = $_POST['price'];
    
    // Validate date and time
    $current_datetime = new DateTime();
    $show_datetime = new DateTime($show_date . ' ' . $show_time);
    
    if($show_datetime < $current_datetime) {
        $error_message = '<div class="alert alert-danger">Cannot add showtime for past date and time!</div>';
    } else {
        // Check for overlapping showtimes in the same theater (within 3 hours before and after)
        $checkStmt = $pdo->prepare("
            SELECT COUNT(*) FROM showtimes 
            WHERE theater_id = ? 
            AND show_date = ? 
            AND ABS(TIME_TO_SEC(TIMEDIFF(show_time, ?))) < 10800
        ");
        $checkStmt->execute([$theater_id, $show_date, $show_time]);
        
        if ($checkStmt->fetchColumn() > 0) {
            $error_message = '<div class="alert alert-danger">This theater is occupied within 3 hours of the selected time. Please choose a different time or theater.</div>';
        } else {
            // Proceed with insertion
            $stmt = $pdo->prepare("INSERT INTO showtimes (movie_id, theater_id, show_date, show_time, price) VALUES (?, ?, ?, ?, ?)");
            if($stmt->execute([$movie_id, $theater_id, $show_date, $show_time, $price])) {
                $success_message = '<div class="alert alert-success">Showtime added successfully!</div>';
            } else {
                $error_message = '<div class="alert alert-danger">Failed to add showtime!</div>';
            }
        }
    }
}

// Fetch all showtimes with movie and theater names
$showtimes = $pdo->query("
    SELECT s.*, m.title as movie_title, t.name as theater_name 
    FROM showtimes s 
    JOIN movies m ON s.movie_id = m.movie_id 
    JOIN theaters t ON s.theater_id = t.theater_id 
    ORDER BY s.show_date, s.show_time
")->fetchAll();
require_once 'includes/admin_header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Showtimes - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add this after the existing CSS links in head section -->
    <script>
        window.addEventListener('load', function() {
            // Get today's date in YYYY-MM-DD format
            const today = new Date().toISOString().split('T')[0];
            
            // Set min attribute of date input to today
            const dateInput = document.querySelector('input[type="date"]');
            const timeInput = document.querySelector('input[type="time"]');
            dateInput.min = today;

            // Function to validate time if date is today
            function validateTime() {
                if(dateInput.value === today) {
                    const now = new Date();
                    const currentHour = String(now.getHours()).padStart(2, '0');
                    const currentMinute = String(now.getMinutes()).padStart(2, '0');
                    const currentTime = `${currentHour}:${currentMinute}`;
                    timeInput.min = currentTime;
                } else {
                    timeInput.min = '';
                }
            }

            // Validate on date change
            dateInput.addEventListener('change', validateTime);
            // Initial validation
            validateTime();
        });
    </script>
</head>
<body>
    <div class="container mt-4">
        <?php 
        if(isset($error_message)) echo $error_message;
        if(isset($success_message)) echo $success_message;
        ?>
        <h2>Manage Showtimes</h2>
        
        <!-- Add Showtime Form -->
        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-2">
                    <select name="movie_id" class="form-control" required>
                        <option value="">Select Movie</option>
                        <?php foreach($movies as $movie): ?>
                            <option value="<?php echo $movie['movie_id']; ?>">
                                <?php echo htmlspecialchars($movie['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="theater_id" class="form-control" required>
                        <option value="">Select Theater</option>
                        <?php foreach($theaters as $theater): ?>
                            <option value="<?php echo $theater['theater_id']; ?>">
                                <?php echo htmlspecialchars($theater['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                <input type="date" class="form-control" name="show_date" 
                min="<?php echo date('Y-m-d'); ?>" required> 
                </div>
                <div class="col-md-2">
                    <input type="time" class="form-control" name="show_time" required>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" class="form-control" name="price" placeholder="Price" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="add_showtime" class="btn btn-primary">Add Showtime</button>
                </div>
            </div>
        </form>

        <!-- Showtimes List -->
        <table class="table">
            <thead>
                <tr>
                    <th>Movie</th>
                    <th>Theater</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($showtimes as $showtime): ?>
                <tr>
                    <td><?php echo htmlspecialchars($showtime['movie_title']); ?></td>
                    <td><?php echo htmlspecialchars($showtime['theater_name']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($showtime['show_date'])); ?></td>
                    <td><?php echo date('H:i', strtotime($showtime['show_time'])); ?></td>
                    <td>₹<?php echo number_format($showtime['price'], 2); ?></td>
                    <td>
                        <a href="delete_showtime.php?id=<?php echo $showtime['showtime_id']; ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>