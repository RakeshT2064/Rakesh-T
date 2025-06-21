<?php
session_start();
require_once 'config/database.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    $stmt = $pdo->prepare("
        SELECT m.*, GROUP_CONCAT(g.name) as genres
        FROM movies m
        LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
        LEFT JOIN genres g ON mg.genre_id = g.genre_id
        WHERE m.title LIKE ? OR m.description LIKE ?
        GROUP BY m.movie_id
        ORDER BY 
            CASE WHEN m.status = 'now_showing' THEN 1
                 WHEN m.status = 'coming_soon' THEN 2
                 ELSE 3 END,
            m.release_date DESC
    ");
    
    $searchTerm = "%{$search}%";
    $stmt->execute([$searchTerm, $searchTerm]);
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Search error: " . $e->getMessage();
    $movies = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - <?php echo htmlspecialchars($search); ?></title>
    <link rel="icon" type="image/logo.png" href="images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .movie-card {
            transition: transform 0.2s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 15px;
        }
        .movie-card:hover {
            transform: translateY(-5px);
        }
        .movie-poster {
            height: 400px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <?php require_once 'includes/headers.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Search Results for "<?php echo htmlspecialchars($search); ?>"</h2>
            <span class="text-muted"><?php echo count($movies); ?> movies found</span>
        </div>
        
        <?php if (empty($movies)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No movies found matching your search.
                <a href="index.php" class="alert-link">Return to homepage</a>
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-4 g-4">
                <?php foreach ($movies as $movie): ?>
                    <div class="col">
                        <div class="card h-100 movie-card">
                            <div class="position-relative">
                                <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" 
                                     class="card-img-top movie-poster" 
                                     alt="<?php echo htmlspecialchars($movie['title']); ?>"
                                     onerror="this.src='images/default-movie.jpg'">
                                <span class="status-badge badge <?php echo $movie['status'] === 'now_showing' ? 'bg-success' : 'bg-primary'; ?>">
                                    <?php echo ucwords(str_replace('_', ' ', $movie['status'])); ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h5>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> <?php echo $movie['duration']; ?> mins
                                    </small>
                                </p>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <?php echo $movie['genres'] ? htmlspecialchars($movie['genres']) : 'No genres listed'; ?>
                                    </small>
                                </p>
                                <div class="d-grid">
                                    <?php if($movie['status'] === 'now_showing'): ?>
                                        <a href="movie_details.php?id=<?php echo $movie['movie_id']; ?>" 
                                           class="btn btn-primary">Book Tickets</a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary" disabled>Coming Soon</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php require_once 'includes/footer.php'; ?>
</body>
</html>