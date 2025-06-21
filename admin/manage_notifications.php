<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if(isset($_POST['add_notification'])) {
    $message = trim($_POST['message']);
    $stmt = $pdo->prepare("INSERT INTO notifications (message) VALUES (?)");
    $stmt->execute([$message]);
    header("Location: manage_notifications.php");
    exit();
}

// Fetch all notifications
$notifications = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/admin_header.php'; ?>
    
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h3>Manage Notifications</h3>
            </div>
            <div class="card-body">
                <form method="POST" class="mb-4">
                    <div class="mb-3">
                        <label for="message" class="form-label">Notification Message</label>
                        <input type="text" class="form-control" id="message" name="message" required>
                    </div>
                    <button type="submit" name="add_notification" class="btn btn-primary">Add Notification</button>
                </form>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Message</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($notifications as $notification): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($notification['message']); ?></td>
                            <td><?php echo date('Y-m-d H:i:s', strtotime($notification['created_at'])); ?></td>
                            <td>
                                <a href="delete_notification.php?id=<?php echo $notification['notification_id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>