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
// Update the query to include new fields
// Fetch all movies with genres (if they exist)
// Update the query to correctly fetch language name
$stmt = $pdo->query("
    SELECT m.*, 
           GROUP_CONCAT(DISTINCT g.name) as genres,
           l.name as language_name
    FROM movies m 
    LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id 
    LEFT JOIN genres g ON mg.genre_id = g.genre_id 
    LEFT JOIN languages l ON m.language_id = l.language_id
    GROUP BY m.movie_id 
    ORDER BY m.created_at DESC
");
$movies = $stmt->fetchAll();

require_once 'includes/admin_header.php';
?>
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Manage Movies</h3>
            <a href="add_movie.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Movie
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
                        <th>Title</th>
                        <th>Movie Type</th>
                        <th>Genres</th>
                        <th>Language</th>
                        <th>Duration</th>
                        <th>Release Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($movies as $movie): ?>
                    <tr>
                        <td><?php echo $movie['movie_id']; ?></td>
                        <td>
                            <?php if($movie['poster_url']): ?>
                                <img src="../<?php echo htmlspecialchars($movie['poster_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($movie['title']); ?>" 
                                     style="width: 50px; height: 75px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-secondary text-white" style="width: 50px; height: 75px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-image"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($movie['title']); ?></td>
                        <td><?php echo htmlspecialchars($movie['movie_type']); ?></td>
                        <td><?php echo htmlspecialchars($movie['genres']); ?></td>
                        <td><?php echo $movie['language_name'] ? htmlspecialchars($movie['language_name']) : 'Not set'; ?></td>
                        <td>
                            <?php 
                                $hours = floor($movie['duration'] / 60);
                                $minutes = $movie['duration'] % 60;
                                echo $hours . 'h ' . $minutes . 'm';
                            ?>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($movie['release_date'])); ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="edit_movie.php?id=<?php echo $movie['movie_id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="delete_movie.php?id=<?php echo $movie['movie_id']; ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Are you sure you want to delete this movie?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
