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

/*slider*/
.slider{
    width: 1300px;
    max-width: 100vw;
    height: 500px;
    margin: auto;
    position: relative;
    overflow: hidden;
}
.slider .list{
    position: absolute;
    width: max-content;
    height: 100%;
    left: 0;
    top: 0;
    display: flex;
    transition: 1s;
}
.slider .list img{
    width: 1300px;
    max-width: 100vw;
    height: 100%;
    object-fit: cover;
}
.slider .buttons{
    position: absolute;
    top: 45%;
    left: 5%;
    width: 90%;
    display: flex;
    justify-content: space-between;
}
.slider .buttons button{
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: #fff5;
    color: #fff;
    border: none;
    font-family: monospace;
    font-weight: bold;
}
.slider .dots{
    position: absolute;
    bottom: 10px;
    left: 0;
    color: #fff;
    width: 100%;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
}
.slider .dots li{
    list-style: none;
    width: 10px;
    height: 10px;
    background-color: #fff;
    margin: 10px;
    border-radius: 20px;
    transition: 0.5s;
}
.slider .dots li.active{
    width: 30px;
}
@media screen and (max-width: 768px){
    .slider{
        height: 400px;
    }
}
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Movie Slider Section -->
<div class="slider">
        <div class="list">
            <div class="item">
                <img src="images/img1.png" alt="">
            </div>
            <div class="item">
                <img src="images/img2.png" alt="">
            </div>
            <div class="item">
                <img src="images/img3.png" alt="">
            </div>
        </div>
        <div class="buttons">
            <button id="prev"><</button>
            <button id="next">></button>
        </div>
        <ul class="dots">
            <li class="active"></li>
            <li></li>
            <li></li>
        </ul>
    </div>

    <script src="app.js"></script>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Latest Movies</h2>
        <a href="movies.php?status=now_showing" class="btn btn-outline-primary">View All</a>
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

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Coming Soon</h2>
        <a href="comingmovies.php?status=coming_soon" class="btn btn-outline-primary">View All</a>
    </div>
    <div id="comingSoonCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php 
            $chunks = array_chunk($comingSoon, 4);
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
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-event"></i> 
                                                Release Date: <?php echo date('M d, Y', strtotime($movie['release_date'])); ?>
                                            </small>
                                        </p>
                                        <div class="d-grid">
                                            <button class="btn btn-secondary" disabled>Coming Soon</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#comingSoonCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#comingSoonCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>


<style>
.notification-bar {
    background-color: #d42727;
    padding: 8px 0;
    margin-bottom: 20px;
}
.notification-bar marquee {
    font-size: 16px;
    font-weight: 500;
}
</style>

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