<?php
session_start();
require_once 'config/database.php';
// Add this function if not already defined or included
function generateOTP($length = 6) {
    return str_pad(rand(0, pow(10, $length)-1), $length, '0', STR_PAD_LEFT);
}

if (isset($_POST['generate_otp'])) {
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
    $otp = generateOTP();
    $_SESSION['otp'] = $otp;
    $_SESSION['phone'] = $phone;
    $otp_message = "Your OTP is: " . $otp;
}

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $entered_otp = $_POST['otp'];

    $errors = [];
    if ($entered_otp != $_SESSION['otp']) {
        $errors[] = "Invalid OTP. Please try again.";
    }
    if(strlen($name) < 3) {
        $errors[] = "Name must be at least 3 characters long";
    }
    if(strlen($phone) < 10) {
        $errors[] = "Please enter a valid phone number";
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    if(strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }
    if($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if($stmt->fetchColumn() > 0) {
        $errors[] = "Email already registered";
    }

    if(empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $phone, $hashed_password])) {
                $_SESSION['success'] = "Registration successful! Please login.";
                header("Location: login.php");
                exit();
            }
        } catch(PDOException $e) {
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Movie Booking System</title>
    <link rel="icon" type="image/logo.png" href="images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right,rgb(255, 255, 255),rgb(230, 227, 227));
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 60px;
        }
        .container {
            max-width: 500px;
            margin: auto;
        }
        .register-form {
            background-color: #fff;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .register-form:hover {
            transform: translateY(-5px);
        }
        h2.text-center {
            font-weight: 600;
            color: #333;
            margin-bottom: 30px;
        }
        .form-floating {
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
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-form">
            <h2 class="text-center">Create Account</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="name" name="name" 
                           placeholder="Full Name" required
                           pattern="[A-Za-z\s]+"
                           value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                    <label for="name">Full Name</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="tel" class="form-control" id="phone" name="phone" 
                           placeholder="Phone Number" required 
                           maxlength="10"
                           pattern="[6-9][0-9]{9}"
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    <label for="phone">Phone Number</label>
                </div>

                <button type="submit" name="generate_otp" class="btn btn-secondary w-100 mb-3">Generate OTP</button>

                <?php if (isset($otp_message)): ?>
                    <div class="alert alert-info mb-3">
                        <?php echo $otp_message; ?>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="otp" name="otp" 
                               placeholder="Enter OTP" required maxlength="6" pattern="\d{6}" autocomplete="one-time-code"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,6)">
                        <label for="otp">Enter OTP</label>
                    </div>
                <?php endif; ?>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="Email Address" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <label for="email">Email Address</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Password" required minlength="6">
                    <label for="password">Password</label>
                </div>

                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                           placeholder="Confirm Password" required>
                    <label for="confirm_password">Confirm Password</label>
                </div>

                <button type="submit" name="register" class="btn btn-primary w-100 mb-3">Register</button>
                
                <div class="text-center">
                    Already have an account? <a href="login.php">Login here</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value;
            if (value.length === 1 && !['6','7','8','9'].includes(value)) {
                e.target.value = '';
            }
        });
        
        document.getElementById('name').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^A-Za-z\s]/g, '');
        });

        // Only allow 6 digits in OTP field
        document.addEventListener('DOMContentLoaded', function() {
            var otpInput = document.getElementById('otp');
            if (otpInput) {
                otpInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0,6);
                });
            }
        });
    </script>
</body>
</html>