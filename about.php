<?php
session_start();
require_once 'config/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - My Cinema</title>
    <link rel="icon" type="image/logo.png" href="images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .navbar-brand img {
            height: 55pt;
            border-radius: 17px;
        }
        .about-section {
            padding: 50px 0;
        }
        .about-image {
            max-width: 100%;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .feature-box {
            padding: 20px;
            border-radius: 10px;
            background: #f8f9fa;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .feature-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container about-section">
        <div class="row align-items-center mb-5">
            <div class="col-md-6">
                <h1 class="mb-4">Welcome to My Cinema</h1>
                <p class="lead">Experience the magic of movies in ultimate comfort and style.</p>
                <p>At My Cinema, we're passionate about delivering the best movie-watching experience to our valued customers. With state-of-the-art technology, comfortable seating, and exceptional service, we make every visit memorable.</p>
            </div>
            <div class="col-md-6">
                <img src="images/logo.png" alt="Cinema Interior" class="about-image">
            </div>
        </div>

        <h2 class="text-center mb-4">Why Choose Us?</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="feature-box">
                    <h3><i class="bi bi-film"></i> Latest Movies</h3>
                    <p>We bring you the newest releases and blockbusters as soon as they hit the screens.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <h3><i class="bi bi-speaker"></i>Dolby Audio 7.1 Sound</h3>
                    <p>Experience crystal-clear audio with our advanced sound systems.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <h3><i class="bi bi-display"></i> 4K Projection</h3>
                    <p>Enjoy stunning visuals with our high-definition projection technology.</p>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-8 offset-md-2 text-center">
                <h2 class="mb-4">Our Mission</h2>
                <p>To provide an unparalleled cinema experience that combines comfort, technology, and entertainment, making every movie moment unforgettable for our guests.</p>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>