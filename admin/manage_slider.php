<?php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if(isset($_POST['add_slide'])) {
    $title = $_POST['title'];
    
    // Handle image upload
    $image = $_FILES['image'];
    $image_url = '';
    if($image['error'] == 0) {
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $upload_path = '../uploads/slider/' . $filename;
        
        if(move_uploaded_file($image['tmp_name'], $upload_path)) {
            $image_url = 'uploads/slider/' . $filename;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO sliderimages (title, image_url) VALUES (?, ?)");
    $stmt->execute([$title, $image_url]);
    
    $_SESSION['success'] = "Slider image added successfully!";
    header("Location: manage_slider.php");
    exit();
}

if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT image_url FROM sliderimages WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetch();
    
    if($image && file_exists('../' . $image['image_url'])) {
        unlink('../' . $image['image_url']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM sliderimages WHERE id = ?");
    $stmt->execute([$id]);
    
    $_SESSION['success'] = "Slider image deleted successfully!";
    header("Location: manage_slider.php");
    exit();
}

$slides = $pdo->query("SELECT * FROM sliderimages ORDER BY created_at DESC")->fetchAll();
require_once 'includes/admin_header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Manage Slider Images</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" name="add_slide" class="btn btn-primary d-block">Add Slider Image</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($slides as $slide): ?>
                                <tr>
                                    <td>
                                        <img src="../<?php echo htmlspecialchars($slide['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($slide['title']); ?>"
                                             style="height: 50px;">
                                    </td>
                                    <td><?php echo htmlspecialchars($slide['title']); ?></td>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($slide['created_at'])); ?></td>
                                    <td>
                                        <a href="?delete=<?php echo $slide['id']; ?>" 
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
</div>

<?php require_once 'includes/admin_footer.php'; ?>