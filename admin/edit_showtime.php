<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$showtime_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM showtimes WHERE showtime_id = ?");
$stmt->execute([$showtime_id]);
$showtime = $stmt->fetch();

if(!$showtime) {
    header("Location: manage_showtimes.php");
    exit();
}

// Fetch movies and theaters for dropdowns
$movies = $pdo->query("SELECT * FROM movies ORDER BY title")->fetchAll();
$theaters = $pdo->query("SELECT * FROM theaters ORDER BY name")->fetchAll();

if(isset($_POST['submit'])) {
    $movie_id = $_POST['movie_id'];
    $theater_id = $_POST['theater_id'];
    $show_date = $_POST['show_date'];
    $show_time = $_POST['show_time'];
    $price = $_POST['price'];

    $stmt = $pdo->prepare("UPDATE showtimes SET movie_id = ?, theater_id = ?, show_date = ?, show_time = ?, price = ? WHERE showtime_id = ?");
    $stmt->execute([$movie_id, $theater_id, $show_date, $show_time, $price, $showtime_id]);
    
    header("Location: manage_showtimes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Showtime - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Showtime</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="movie_id" class="form-label">Movie</label>
                <select class="form-control" id="movie_id" name="movie_id" required>
                    <?php foreach($movies as $movie): ?>
                        <option value="<?php echo $movie['movie_id']; ?>" 
                                <?php echo $movie['movie_id'] === $showtime['movie_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($movie['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="theater_id" class="form-label">Theater</label>
                <select class="form-control" id="theater_id" name="theater_id" required>
                    <?php foreach($theaters as $theater): ?>
                        <option value="<?php echo $theater['theater_id']; ?>"
                                <?php echo $theater['theater_id'] === $showtime['theater_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($theater['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="show_date" class="form-label">Show Date</label>
                <input type="date" class="form-control" id="show_date" name="show_date" 
                       value="<?php echo $showtime['show_date']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="show_time" class="form-label">Show Time</label>
                <input type="time" class="form-control" id="show_time" name="show_time" 
                       value="<?php echo date('H:i', strtotime($showtime['show_time'])); ?>" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" 
                       value="<?php echo $showtime['price']; ?>" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Update Showtime</button>
            <a href="manage_showtimes.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>