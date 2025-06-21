<?php
session_start();
require_once 'config/database.php';

if(isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Movie Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/logo.png" href="images/logo.png">

    <style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(to right,rgb(255, 255, 255),rgb(230, 227, 227));
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-form {
    background-color: white;
    padding: 40px 30px;
    border-radius: 15px;
    box-shadow: 0 8px 24px rgb(46, 43, 43);
    width: 100%;
    max-width: 450px;
    
}

.login-form h2 {
    font-weight: bold;
    margin-bottom: 25px;
}

.login-form .form-control {
    border: none;
    border-bottom: 2px solid #2874f0;
    border-radius: 0;
    margin-bottom: 20px;
    background-color: transparent;
    box-shadow: none;
}

.login-form .form-control:focus {
    border-color: #2874f0;
    box-shadow: none;
}

.login-form button {
    background-color: #fb641b;
    color: white;
    font-weight: bold;
    border: none;
    padding: 20px;
    width: 100%;
    border-radius: 5px;
    margin-bottom: 10px;
}

.login-form a {
    color: #2874f0;
    text-decoration: none;
    font-weight: 500;
}

.login-form a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body class="bg-light">
    <div class="container"><center>
        <div class="login-form bg-white">
            <img src="images/logo.png" width="200px" height="100px"/>
            <h2 class="text-center mb-4">Welcome Back</h2>
            
            <!-- Add this after the h2 heading in login.php -->
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="form-floating">
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="Email Address" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <label for="email">Email Address</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Password" required>
                    <label for="password">Password</label>
                </div>

                <button type="submit" name="login" class="btn btn-primary w-100 mb-3">Login</button>
                
                <div class="text-center">
                    <div class="mb-2">
                        <a href="forgot_password.php">Forgot Password?</a>
                    </div>
                    <div>
                        Don't have an account? <a href="register.php">Register here</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>