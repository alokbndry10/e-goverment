<?php
require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if (isset($_GET['error'])) {
    $error = sanitize($_GET['error']);
}
if (isset($_GET['success'])) {
    $success = sanitize($_GET['success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Nagarik App</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="gradient-bg">
        <div class="auth-card">
            <div class="logo-container">
                <div class="logo-placeholder">
                    <span class="nepal-text">नागरिक एप</span>
                    <span class="app-name">"सरकारी सेवा हातहातमा"</span>
                </div>
            </div>
            
            <h2 class="portal-title">CREATE ACCOUNT</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form id="registerForm" action="auth/register.php" method="POST">
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="full_name" placeholder="Full Name" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-wrapper" id="mobileWrapper">
                        <i class="fas fa-phone"></i>
                        <input type="text" name="mobile" id="mobile" placeholder="Mobile Number" required>
                        <i class="fas fa-exclamation-circle" style="right: 15px; left: auto; color: #dc143c; display: none;" id="mobileError"></i>
                    </div>
                    <span class="error-text" id="mobileErrorText"></span>
                </div>
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" placeholder="Password" required minlength="6">
                        <i class="fas fa-eye-slash toggle-password" onclick="togglePassword()"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                </div>
                
                <div class="form-footer">
                    <a href="login.html">Already have an account?</a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-user-plus"></i> REGISTER
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="js/main.js"></script>
</body>
</html>
