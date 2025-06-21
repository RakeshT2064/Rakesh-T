<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$theater_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM theaters WHERE theater_id = ?");
$stmt->execute([$theater_id]);
$theater = $stmt->fetch();

if(!$theater) {
    header("Location: manage_theaters.php");
    exit();
}

if(isset($_POST['submit'])) {
    $name = $_POST['name'];
    $seats_capacity = $_POST['seats_capacity'];

    $stmt = $pdo->prepare("UPDATE theaters SET name = ?, seats_capacity = ? WHERE theater_id = ?");
    $stmt->execute([$name, $seats_capacity, $theater_id]);
    
    header("Location: manage_theaters.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Theater - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Theater</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Theater Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($theater['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="seats_capacity" class="form-label">Seats Capacity</label>
                <input type="number" class="form-control" id="seats_capacity" name="seats_capacity" value="<?php echo $theater['seats_capacity']; ?>" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Update Theater</button>
            <a href="manage_theaters.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>