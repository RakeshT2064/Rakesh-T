<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$movie_id = $_GET['id'];

try {
    // First check if movie exists
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE movie_id = ?");
    $stmt->execute([$movie_id]);
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$movie) {
        $_SESSION['error'] = "Movie not found.";
        header("Location: manage_movies.php");
        exit();
    }

    // Start transaction
    $pdo->beginTransaction();

    // First delete bookings that reference showtimes of this movie
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE showtime_id IN 
        (SELECT showtime_id FROM showtimes WHERE movie_id = ?)");
    $stmt->execute([$movie_id]);

    // Then delete showtimes
    $stmt = $pdo->prepare("DELETE FROM showtimes WHERE movie_id = ?");
    $stmt->execute([$movie_id]);

    // Delete from movie_genres
    $stmt = $pdo->prepare("DELETE FROM movie_genres WHERE movie_id = ?");
    $stmt->execute([$movie_id]);

    // Finally delete the movie
    $stmt = $pdo->prepare("DELETE FROM movies WHERE movie_id = ?");
    $stmt->execute([$movie_id]);

    // Delete poster file if exists
    if ($movie['poster_url']) {
        $poster_path = '../' . $movie['poster_url'];
        if (file_exists($poster_path)) {
            unlink($poster_path);
        }
    }

    $pdo->commit();
    $_SESSION['success'] = "Movie deleted successfully.";
    
} catch(PDOException $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Error deleting movie: " . $e->getMessage();
}

header("Location: manage_movies.php");
exit();