<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$movie_id = $_GET['id'];

// Fetch movie with language information
$stmt = $pdo->prepare("
    SELECT m.*, l.language_id as current_language_id 
    FROM movies m 
    LEFT JOIN languages l ON m.language_id = l.language_id 
    WHERE m.movie_id = ?
");
$stmt->execute([$movie_id]);
$movie = $stmt->fetch();

// Fetch all available languages
$languages = $pdo->query("SELECT * FROM languages ORDER BY name")->fetchAll();

if(isset($_POST['submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $release_date = $_POST['release_date'];
    $status = $_POST['status'];
    $language_id = $_POST['language_id'];
    $movie_type = $_POST['movie_type'];
    $poster_url = $movie['poster_url'];

    if(isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['poster']['tmp_name'];
        $file_name = time() . '_' . $_FILES['poster']['name'];
        $upload_path = '../uploads/posters/' . $file_name;
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        $file_type = mime_content_type($file_tmp);
        
        if(in_array($file_type, $allowed_types)) {
            if(move_uploaded_file($file_tmp, $upload_path)) {
                // Delete old poster if exists
                if($movie['poster_url'] && file_exists('../' . $movie['poster_url'])) {
                    unlink('../' . $movie['poster_url']);
                }
                $poster_url = 'uploads/posters/' . $file_name;
            }
        }
    }

    $stmt = $pdo->prepare("
        UPDATE movies 
        SET title = ?, description = ?, duration = ?, 
            release_date = ?, poster_url = ?, status = ?,
            language_id = ?, movie_type = ?
        WHERE movie_id = ?
    ");
    $stmt->execute([
        $title, $description, $duration, 
        $release_date, $poster_url, $status,
        $language_id, $movie_type, $movie_id
    ]);
    
    header("Location: manage_movies.php");
    exit();
}
?>
    <?php include 'includes/admin_header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Movie</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($movie['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($movie['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="duration" class="form-label">Duration</label>
                <div class="row">
                    <div class="col">
                        <input type="number" class="form-control" id="hours" name="hours" 
                               placeholder="Hours" required min="0" max="5"
                               value="<?php echo floor($movie['duration'] / 60); ?>">
                    </div>
                    <div class="col">
                        <input type="number" class="form-control" id="minutes" name="minutes" 
                               placeholder="Minutes" required min="0" max="59"
                               value="<?php echo $movie['duration'] % 60; ?>">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="release_date" class="form-label">Release Date</label>
                <input type="date" class="form-control" id="release_date" name="release_date" value="<?php echo $movie['release_date']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="poster" class="form-label">Movie Poster</label>
                <input type="file" class="form-control" id="poster" name="poster" accept="image/jpeg,image/png,image/jpg">
                <small class="text-muted">Leave empty to keep current poster</small>
                <?php if($movie['poster_url']): ?>
                    <div class="mt-2">
                        <img src="../<?php echo htmlspecialchars($movie['poster_url']); ?>" alt="Current poster" style="max-width: 200px;">
                    </div>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="language_id" class="form-label">Language</label>
                <select class="form-control" id="language_id" name="language_id" required>
                    <option value="">Select Language</option>
                    <?php foreach($languages as $language): ?>
                        <option value="<?php echo $language['language_id']; ?>" 
                            <?php echo ($movie['language_id'] == $language['language_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($language['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="movie_type" class="form-label">Movie Type</label>
                <select class="form-control" id="movie_type" name="movie_type" required>
                    <option value="2D" <?php echo $movie['movie_type'] === '2D' ? 'selected' : ''; ?>>2D</option>
                    <option value="3D" <?php echo $movie['movie_type'] === '3D' ? 'selected' : ''; ?>>3D</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="now_showing" <?php echo $movie['status'] === 'now_showing' ? 'selected' : ''; ?>>Now Showing</option>
                    <option value="coming_soon" <?php echo $movie['status'] === 'coming_soon' ? 'selected' : ''; ?>>Coming Soon</option>
                </select>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Update Movie</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const hours = parseInt(document.getElementById('hours').value) || 0;
    const minutes = parseInt(document.getElementById('minutes').value) || 0;
    const totalMinutes = (hours * 60) + minutes;
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'duration';
    input.value = totalMinutes;
    this.appendChild(input);
});
</script>