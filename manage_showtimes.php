<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_showtime'])) {
    $check_stmt = $pdo->prepare("
        SELECT COUNT(*) FROM showtimes 
        WHERE movie_id = ? AND theater_id = ? AND show_date = ? AND show_time = ?
    ");
    
    $check_stmt->execute([
        $_POST['movie_id'],
        $_POST['theater_id'],
        $_POST['show_date'],
        $_POST['show_time']
    ]);
    
    if ($check_stmt->fetchColumn() > 0) {
        $_SESSION['error'] = "This showtime already exists.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO showtimes (movie_id, theater_id, show_date, show_time, price) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([
            $_POST['movie_id'],
            $_POST['theater_id'],
            $_POST['show_date'],
            $_POST['show_time'],
            $_POST['price']
        ])) {
            $_SESSION['success'] = "Showtime added successfully.";
        } else {
            $_SESSION['error'] = "Failed to add showtime.";
        }
    }
    
    header("Location: /mycinema/admin/manage_showtimes.php");  // Updated redirect path
    exit();
}

// Fetch movies for dropdown
$movies_stmt = $pdo->query("SELECT movie_id, title FROM movies ORDER BY title");
$movies = $movies_stmt->fetchAll();

// Fetch theaters for dropdown
$theaters_stmt = $pdo->query("SELECT theater_id, name FROM theaters ORDER BY name");
$theaters = $theaters_stmt->fetchAll();

// Fetch showtimes with movie and theater information
$display_stmt = $pdo->prepare("SELECT s.*, m.title, t.name as theater_name 
                             FROM showtimes s
                             JOIN movies m ON s.movie_id = m.movie_id
                             JOIN theaters t ON s.theater_id = t.theater_id
                             ORDER BY s.show_date, s.show_time");
$display_stmt->execute();
$showtimes = $display_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Showtimes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Add New Showtime</h2>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form method="POST" onsubmit="return validateDateTime()">
            <div class="mb-3">
                <label for="movie_id" class="form-label">Movie</label>
                <select class="form-control" id="movie_id" name="movie_id" required>
                    <option value="">Select Movie</option>
                    <?php foreach($movies as $movie): ?>
                        <option value="<?php echo $movie['movie_id']; ?>">
                            <?php echo htmlspecialchars($movie['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="theater_id" class="form-label">Theater</label>
                <select class="form-control" id="theater_id" name="theater_id" required>
                    <option value="">Select Theater</option>
                    <?php foreach($theaters as $theater): ?>
                        <option value="<?php echo $theater['theater_id']; ?>">
                            <?php echo htmlspecialchars($theater['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="show_date" class="form-label">Show Date</label>
                <input type="date" class="form-control" id="show_date" name="show_date" 
                       min="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="mb-3">
                <label for="show_time" class="form-label">Show Time</label>
                <input type="time" class="form-control" id="show_time" name="show_time" required>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" class="form-control" id="price" name="price" 
                       min="0" step="0.01" required>
            </div>

            <button type="submit" name="add_showtime" class="btn btn-primary">Add Showtime</button>
        </form>

        <h2 class="mt-4">Existing Showtimes</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Movie</th>
                    <th>Theater</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($showtimes as $showtime): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($showtime['title']); ?></td>
                        <td><?php echo htmlspecialchars($showtime['theater_name']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($showtime['show_date'])); ?></td>
                        <td><?php echo date('H:i', strtotime($showtime['show_time'])); ?></td>
                        <td>₹<?php echo number_format($showtime['price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>


<script>
    function validateDateTime() {
        var showDate = document.getElementById('show_date').value;
        var showTime = document.getElementById('show_time').value;
        
        var selectedDateTime = new Date(showDate + 'T' + showTime);
        var now = new Date();
        
        if (showDate === now.toISOString().split('T')[0]) {
            var currentTime = now.getHours() * 60 + now.getMinutes();
            var selectedTime = parseInt(showTime.split(':')[0]) * 60 + parseInt(showTime.split(':')[1]);
            
            if (selectedTime <= currentTime) {
                alert('For today\'s shows, please select a time later than the current time.');
                return false;
            }
        }
        
        if (selectedDateTime < now) {
            alert('Cannot set showtime in the past. Please select current or future date and time.');
            return false;
        }
        return true;
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

