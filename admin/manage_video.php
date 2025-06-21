<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if(isset($_POST['upload_video'])) {
    $title = $_POST['title'];
    $video = $_FILES['video'];
    
    if($video['error'] === 0) {
        $allowed = ['mp4', 'webm'];
        $ext = strtolower(pathinfo($video['name'], PATHINFO_EXTENSION));
        
        if(in_array($ext, $allowed)) {
            $new_name = uniqid('video_') . '.' . $ext;
            $upload_dir = '../uploads/videos';
            
            if(!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if(move_uploaded_file($video['tmp_name'], $upload_dir . '/' . $new_name)) {
                // Deactivate all other videos first
                $pdo->query("UPDATE video_banner SET active = 0");
                
                // Insert new video
                $stmt = $pdo->prepare("INSERT INTO video_banner (title, video_url, active) VALUES (?, ?, 1)");
                $stmt->execute([$title, 'uploads/videos/' . $new_name]);
                
                $_SESSION['success'] = "Video uploaded successfully!";
                header("Location: manage_video.php");
                exit();
            }
        }
    }
    $_SESSION['error'] = "Failed to upload video. Please try again.";
}

$videos = $pdo->query("SELECT * FROM video_banner ORDER BY created_at DESC")->fetchAll();
require_once 'includes/admin_header.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Banner Video</h3>
        </div>
        <div class="card-body">
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Video Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Video File</label>
                            <input type="file" class="form-control" name="video" accept="video/mp4,video/webm" required>
                            <small class="text-muted">Supported formats: MP4, WebM</small>
                        </div>
                    </div>
                </div>
                <button type="submit" name="upload_video" class="btn btn-primary">Upload Video</button>
            </form>

            <hr>

            <div class="table-responsive mt-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Video</th>
                            <th>Status</th>
                            <th>Added On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($videos as $video): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($video['title']); ?></td>
                            <td>
                                <video width="200" controls>
                                    <source src="../<?php echo $video['video_url']; ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </td>
                            <td><?php echo $video['active'] ? 'Active' : 'Inactive'; ?></td>
                            <td><?php echo date('M d, Y', strtotime($video['created_at'])); ?></td>
                            <td>
                                <a href="manage_video.php?delete=<?php echo $video['id']; ?>" 
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
</div>

<?php require_once 'includes/admin_footer.php'; ?>