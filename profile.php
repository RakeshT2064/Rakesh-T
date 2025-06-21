<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$page_title = "My Profile";

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Handle profile update
if (isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, phone = ? WHERE user_id = ?");
        $stmt->execute([$username, $email, $phone, $_SESSION['user_id']]);
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit();
    } catch(PDOException $e) {
        $error = "Failed to update profile. Please try again.";
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $password_error = "New passwords do not match!";
    } else if (password_verify($current_password, $user['password'])) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt->execute([$hashed_password, $_SESSION['user_id']]);
        $_SESSION['success'] = "Password changed successfully!";
        header("Location: profile.php");
        exit();
    } else {
        $password_error = "Current password is incorrect!";
    }
}

require_once 'includes/headers.php';
?>
<html>
    <head>
        <title>Profile</title>
        <link rel="icon" type="image/logo.png" href="images/logo.png">
        <style>
            body {
    background-color: #f5f7fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.container {
    margin-top: 5px;
}

.card {
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    border: none;
    border-radius: 10px;
    background-color: #ffffff;
}

.card-header {
    background-color: #ffffff;
    border-bottom: 1px solid #eee;
}

.card-body {
    padding: 25px;
}

.card h5 {
    font-weight: 600;
    margin-bottom: 0;
}

.form-label {
    font-weight: 500;
    color: #333;
}

.form-control {
    border-radius: 6px;
    border: 1px solid #ced4da;
    box-shadow: none;
    transition: border-color 0.2s ease-in-out;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
}

.btn-warning {
    background-color: #f0ad4e;
    border: none;
    transition: background-color 0.3s;
}

.btn-warning:hover {
    background-color: #ec971f;
}

.alert {
    border-radius: 6px;
}

.bi-person-circle {
    color: #6c757d;
}

@media (max-width: 768px) {
    .card-body {
        padding: 15px;
    }

    .container {
        padding: 0 10px;
    }
}

            </style>
</head>
<body>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-person-circle" style="font-size: 5rem;"></i>
                    </div>
                    <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                    <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                    <p class="text-muted">Phone: <?php echo htmlspecialchars($user['phone']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                    </form>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($password_error)): ?>
                        <div class="alert alert-danger"><?php echo $password_error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>