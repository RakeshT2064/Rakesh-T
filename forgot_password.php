<?php
session_start();
require_once 'config/database.php';

function generateAlphanumericPassword($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    $max = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[random_int(0, $max)];
    }
    return $password;
}

$new_password_message = '';
$email_verified = false;

if (isset($_POST['verify_email'])) {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $email_verified = true;
        $_SESSION['reset_email'] = $email;
    } else {
        $new_password_message = "Email not found.";
    }
}

if (isset($_POST['set_new_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $new_password_message = "Passwords do not match.";
    } elseif (!preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $new_password_message = "Password must be alphanumeric.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $email = $_SESSION['reset_email'];

        // Update the password in the database
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        if ($stmt->execute([$hashed_password, $email])) {
            $_SESSION['success_message'] = "Password has been reset successfully. Please login with your new password.";
            unset($_SESSION['reset_email']);
            header("Location: login.php");
            exit();
        } else {
            $new_password_message = "Failed to reset password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 60px;
        }
        .container {
            max-width: 400px;
            margin: auto;
        }
        .forgot-password-form {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        h2.text-center {
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }
        .form-control {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px 15px;
            box-shadow: none !important;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            border-color: #007bff;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert-info {
            margin-top: 20px;
            border-radius: 8px;
            padding: 12px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="forgot-password-form bg-white">
            <h2 class="text-center mb-4">Forgot Password</h2>
            <?php if (!$email_verified && !isset($_POST['set_new_password'])): ?>
                <form method="post">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required>
                        <label for="email">Enter your email address</label>
                    </div>
                    <button type="submit" name="verify_email" class="btn btn-primary w-100">Verify Email</button>
                </form>
            <?php elseif ($email_verified || isset($_POST['set_new_password'])): ?>
                <form method="post">
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password" required>
                        <label for="new_password">New Password</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                        <label for="confirm_password">Confirm Password</label>
                    </div>
                    <button type="submit" name="set_new_password" class="btn btn-primary w-100">Set New Password</button>
                </form>
            <?php endif; ?>
            <?php if ($new_password_message): ?>
                <div class="alert alert-info mt-3">
                    <?php echo $new_password_message; ?>
                </div>
            <?php endif; ?>
        </div>
        
    </div>
</body>
</html>