<?php
session_start();
require_once '../config/database.php';

// Fetch languages and genres
$languages = $pdo->query("SELECT * FROM languages ORDER BY name")->fetchAll();
$genres = $pdo->query("SELECT * FROM genres ORDER BY name")->fetchAll();

if(isset($_POST['add_movie'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $duration = (int)$_POST['duration'];
    $release_date = $_POST['release_date'];
    $language_id = (int)$_POST['language_id'];
    $movie_type = $_POST['movie_type'];
    $status = $_POST['status'];
    $selected_genres = isset($_POST['genres']) ? $_POST['genres'] : [];

    try {
        $pdo->beginTransaction();

        // Upload poster
        $poster_url = null; // Set default value
        if(isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
            $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
            $poster_url = 'uploads/posters/' . uniqid() . '.' . $ext;
            if(!move_uploaded_file($_FILES['poster']['tmp_name'], '../' . $poster_url)) {
                throw new Exception("Failed to upload poster image");
            }
        } else {
            throw new Exception("Please select a valid poster image");
        }

        // Insert movie
        $stmt = $pdo->prepare("INSERT INTO movies (title, description, duration, release_date, poster_url, status, language_id, movie_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $duration, $release_date, $poster_url, $status, $language_id, $movie_type]);
        $movie_id = $pdo->lastInsertId();

        // Insert genres
        if(!empty($selected_genres)) {
            $stmt = $pdo->prepare("INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)");
            foreach($selected_genres as $genre_id) {
                $stmt->execute([$movie_id, $genre_id]);
            }
        }

        $pdo->commit();
        $_SESSION['success'] = "Movie added successfully!";
        header("Location: manage_movies.php");
        exit();
    } catch(Exception $e) {
        $pdo->rollBack();
        $error = "Failed to add movie: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Movie - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <?php include 'includes/admin_header.php'; ?>

    <div class="container mt-4">
        <h2>Add New Movie</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="title" class="form-label">Movie Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration</label>
                        <div class="row">
                            <div class="col">
                                <input type="number" class="form-control" id="hours" name="hours" 
                                       placeholder="Hours" required min="0" max="5"
                                       value="<?php echo isset($_POST['hours']) ? floor($_POST['duration'] / 60) : ''; ?>">
                            </div>
                            <div class="col">
                                <input type="number" class="form-control" id="minutes" name="minutes" 
                                       placeholder="Minutes" required min="0" max="59"
                                       value="<?php echo isset($_POST['minutes']) ? ($_POST['duration'] % 60) : ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="language_id" class="form-label">Language</label>
                        <select class="form-select" id="language_id" name="language_id" required>
                            <option value="">Select Language</option>
                            <?php foreach($languages as $language): ?>
                                <option value="<?php echo $language['language_id']; ?>">
                                    <?php echo htmlspecialchars($language['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="movie_type" class="form-label">Movie Type</label>
                        <select class="form-select" id="movie_type" name="movie_type" required>
                            <option value="2D">2D</option>
                            <option value="3D">3D</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="coming_soon">Coming Soon</option>
                            <option value="now_showing">Now Showing</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="genres" class="form-label">Genres</label>
                <select class="form-select" id="genres" name="genres[]" multiple required>
                    <?php foreach($genres as $genre): ?>
                        <option value="<?php echo $genre['genre_id']; ?>">
                            <?php echo htmlspecialchars($genre['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="release_date" class="form-label">Release Date</label>
                <input type="date" class="form-control" id="release_date" name="release_date" required>
            </div>

            <div class="mb-3">
                <label for="poster" class="form-label">Movie Poster</label>
                <input type="file" class="form-control" id="poster" name="poster" accept="image/*" required>
            </div>

            <button type="submit" name="add_movie" class="btn btn-primary">Add Movie</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#genres').select2({
                placeholder: 'Select Genres',
                allowClear: true
            });
        });
    </script>
</body>
</html>

<script>
    document.getElementById('movieForm').addEventListener('submit', function(e) {
        const hours = parseInt(document.getElementById('hours').value) || 0;
        const minutes = parseInt(document.getElementById('minutes').value) || 0;
        const totalMinutes = (hours * 60) + minutes;
        
        // Add hidden input for total duration in minutes for database storage
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'duration';
        input.value = totalMinutes;
        this.appendChild(input);
    });
</script>