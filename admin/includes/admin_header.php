<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

require_once '../config/database.php';  // Add this line to include database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Admin Dashboard' : 'Admin Dashboard'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 0.75rem 1.25rem;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }
        .sidebar .nav-link.active {
            background-color: #0d6efd;
        }
        .main-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Mycinema Admin</a>
            <div class="ms-auto d-flex align-items-center">
                <span class="text-light me-3">Welcome, Rakesh</span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
                               href="index.php">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'movie') !== false ? 'active' : ''; ?>" 
                               href="manage_movies.php">
                                <i class="bi bi-film me-2"></i>Movies
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'theater') !== false ? 'active' : ''; ?>" 
                               href="manage_theaters.php">
                                <i class="bi bi-building me-2"></i>Theaters
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'showtime') !== false ? 'active' : ''; ?>" 
                               href="manage_showtimes.php">
                                <i class="bi bi-clock me-2"></i>Showtimes
                            </a>
                        </li>
        
                        <li class="nav-item">
                            <a class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'booking') !== false ? 'active' : ''; ?>" 
                               href="manage_bookings.php">
                                <i class="bi bi-ticket-perforated me-2"></i>Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>" 
                               href="messages.php">
                                <i class="bi bi-envelope me-2"></i>Messages
                                <?php
                                $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'");
                                $unread_count = $stmt->fetchColumn();
                                if ($unread_count > 0): ?>
                                    <span class="badge bg-danger ms-2"><?php echo $unread_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                       
                    </ul>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto main-content">