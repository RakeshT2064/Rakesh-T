<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if(isset($_POST['add_theater'])) {
    $name = $_POST['name'];
    $seats_capacity = $_POST['seats_capacity'];
    
    $stmt = $pdo->prepare("INSERT INTO theaters (name, seats_capacity) VALUES (?, ?)");
    $stmt->execute([$name, $seats_capacity]);
}

$theaters = $pdo->query("SELECT * FROM theaters")->fetchAll();
require_once 'includes/admin_header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Theaters - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Manage Theaters</h2>
        
        <!-- Add Theater Form -->
        <form method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="name" placeholder="Theater Name" required>
                </div>
                <div class="col-md-4">
                    <input type="number" class="form-control" name="seats_capacity" placeholder="Seats Capacity" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="add_theater" class="btn btn-primary">Add Theater</button>
                </div>
            </div>
        </form>

        <!-- Theaters List -->
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Seats Capacity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($theaters as $theater): ?>
                <tr>
                    <td><?php echo htmlspecialchars($theater['name']); ?></td>
                    <td><?php echo $theater['seats_capacity']; ?></td>
                    <td>
                        <a href="edit_theater.php?id=<?php echo $theater['theater_id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="delete_theater.php?id=<?php echo $theater['theater_id']; ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>