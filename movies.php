<?php
session_start();
require_once 'config/database.php';

$languages = $pdo->query("SELECT * FROM languages ORDER BY name")->fetchAll();
$genres = $pdo->query("SELECT * FROM genres ORDER BY name")->fetchAll();
// Update the movie queries at the top of the file
try {
    // Fetch featured movies for slider
    $stmt = $pdo->prepare("
        SELECT m.*, l.name as language_name, GROUP_CONCAT(g.name) as genre_names
        FROM movies m
        LEFT JOIN languages l ON m.language_id = l.language_id
        LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
        LEFT JOIN genres g ON mg.genre_id = g.genre_id
        WHERE m.status = 'now_showing'
        GROUP BY m.movie_id
        ORDER BY m.movie_id DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $featuredMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch now showing movies
    $stmt = $pdo->prepare("
        SELECT m.*, l.name as language_name, GROUP_CONCAT(g.name) as genre_names
        FROM movies m
        LEFT JOIN languages l ON m.language_id = l.language_id
        LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
        LEFT JOIN genres g ON mg.genre_id = g.genre_id
        WHERE m.status = 'now_showing'
        GROUP BY m.movie_id
    ");
    $stmt->execute();
    $nowShowing = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch coming soon movies
    $stmt = $pdo->prepare("
        SELECT m.*, l.name as language_name, GROUP_CONCAT(g.name) as genre_names
        FROM movies m
        LEFT JOIN languages l ON m.language_id = l.language_id
        LEFT JOIN movie_genres mg ON m.movie_id = mg.movie_id
        LEFT JOIN genres g ON mg.genre_id = g.genre_id
        WHERE m.status = 'coming_soon'
        GROUP BY m.movie_id
    ");
    $stmt->execute();
    $comingSoon = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $featuredMovies = [];
    $nowShowing = [];
    $comingSoon = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cinema</title>
    <link rel="icon" type="image/logo.png" href="images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .navbar-brand img {
            height: 55pt;
            border-radius: 17px;

        }
        .carousel-item {
            height: 500px;
            background-position: center;
            background-size: cover;
            position: relative;
        }
        .carousel-caption {
            background: rgba(0, 0, 0, 0.6);
            padding: 10px;
            border-radius: 10px;
        }
        .nav-link {
            font-size: 1.1rem;
            padding: 0.5rem 1rem !important;
        }
        .navbar {
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(255, 0, 0, 0.1);
        }
        .carousel-item {
        height: 100px;
        border-radius: 10px;
    }
    .carousel-item img {
        height: 85%;
        object-fit: cover;
    }
    .carousel-caption {
        background: rgba(0, 0, 0, 0.7);
        border-radius: 1000px;
        padding: 200px;
        padding-left: 100px;
        max-width: 100px;
        margin: 0 auto;
    }
    .card {
        transition: transform 0.2s;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border-radius:15px;
    }
    .card:hover {
        transform: translateY(5px);
    }
    .card-img-top {
        border-bottom: 0px solid #dee2e6;
        border-radius:15px;
    }
    .carousel-item {
        height: 600px;
        border-radius: 10px;
    }
    .carousel-item img {
        height: 100%;
        object-fit: cover;
    }
    .carousel-caption {
        background: rgba(0, 0, 0, 0.7);
        border-radius: 10px;
        padding: 20px;
        max-width: 500px;
        margin: 0 auto;
        justify-content:center;
    }
    .card {
        transition: transform 0.1s;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .card:hover {
        transform: translateY(-8px);
    }
    .card-img-top {
        border-bottom: -1px solid #dee2e6;
    }


    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/logo.png" alt="Cinema Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                </ul>
                <!-- Add search form here -->
                <form class="d-flex me-3" action="search.php" method="GET">
                    <input class="form-control me-2" type="search" 
                           placeholder="Search movies..." 
                           aria-label="Search" 
                           name="search" 
                           required
                           minlength="2">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
                <div class="navbar-nav">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a class="nav-link" href="my_bookings.php">
                            <i class="bi bi-ticket-perforated"></i> My Bookings
                        </a>
                        <a class="nav-link" href="profile.php">
                            <i class="bi bi-person-circle"></i> Profile
                        </a>
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>   
                    <?php else: ?>
                        <a class="nav-link" href="login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                        <a class="nav-link" href="register.php">
                            <i class="bi bi-person-plus"></i> Register
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>


<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Now Showing</h2>
        
    </div>
    <div id="nowShowingCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php 
            $chunks = array_chunk($nowShowing, 4);
            foreach($chunks as $index => $chunk): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <div class="row row-cols-1 row-cols-md-4 g-4">
                        <?php foreach($chunk as $movie): ?>
                            <div class="col">
                                <div class="card h-100">
                                    <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" 
                                        class="card-img-top" alt="<?php echo htmlspecialchars($movie['title']); ?>"
                                        style="height: 400px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h5>
                                        <p class="card-text text-muted">
                                                <?php if(!empty($movie['language_name'])): ?>
                                                <i class="bi bi-translate"></i> <?php echo htmlspecialchars($movie['language_name']); ?><br>
                                            <?php endif; ?>
                                            <?php if(!empty($movie['movie_type'])): ?>
                                            <?php endif; ?>
                                            <?php if(!empty($movie['genre_names'])): ?>
                                                <i class="bi bi-tags"></i> <?php echo htmlspecialchars($movie['genre_names']); ?>
                                            <?php endif; ?>
                                        </p>

                                        <div class="d-grid">
                                            <a href="movie_details.php?id=<?php echo $movie['movie_id']; ?>" 
                                                class="btn btn-primary">Book Tickets</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#nowShowingCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#nowShowingCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>



<?php require_once 'includes/footer.php'; ?>
<!-- Add this JavaScript at the bottom of the file, before closing body tag -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var myCarousel = document.getElementById('homeSlider');
        var carousel = new bootstrap.Carousel(myCarousel, {
            interval: 3000,
            wrap: true,
            touch: true
        });
    });
</script>
</body>
</html>