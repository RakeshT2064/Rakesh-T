<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Movie Booking System' : 'Movie Booking System'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .navbar-brand img {
            height: 55pt;
            border-radius: 15px;
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
            box-shadow: 0 2px 4px rgb(255, 255, 255);
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
                    <a class="nav-link" href="about.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
            </ul>
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
            <!-- Add this search form -->
            <form class="d-flex" action="search.php" method="GET">
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
        </div>
    </div>
</nav>