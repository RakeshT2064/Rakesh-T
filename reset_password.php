<?php
session_start();
require_once 'config/database.php';

if(!isset($_GET['token'])) {
    header("Location: login.php");
    exit();
}

$token = $_GET['token'];
$stmt = $pdo->prepare("SELECT user_id FROM users WHERE reset_token = ? AND reset_expiry > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if(!$user) {
    $_SESSION['error'] = "Invalid or expired reset link.";
    header("Location: login.php");
    exit();
}

if(isset($_POST['update_password'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif(strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE user_id = ?");
        $stmt->execute([$hashed_password, $user['user_id']]);
        
        $_SESSION['success'] = "Password has been reset successfully. Please login with your new password.";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Movie Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/logo.png" href="images/logo.png">
    <style>
        body {
            background: linear-gradient(to right,rgb(255, 255, 255),rgb(230, 227, 227));
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .reset-form {
            background-color: white;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgb(46, 43, 43);
            width: 100%;
            max-width: 450px;
        }
        .form-control {
            border: none;
            border-bottom: 2px solid #2874f0;
            border-radius: 0;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #fb641b;
            border: none;
            padding: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-form">
            <h2 class="text-center mb-4">Reset Your Password</h2>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="password" name="password" required minlength="6">
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" name="update_password" class="btn btn-primary w-100">Update Password</button>
            </form>
        </div>
    </div>
</body>
</html>