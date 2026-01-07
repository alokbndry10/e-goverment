<?php
require_once '../config.php';

// Redirect if already logged in as admin
if (isAdminLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        // Check both hashed password and plain text password
        if ($admin && (password_verify($password, $admin['password']) || $password === $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            redirect('dashboard.php');
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Nagarik App</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="gradient-bg">
        <div class="auth-card">
            <div class="logo-container">
                <div class="logo-placeholder">
                    <span class="nepal-text">नागरिक एप</span>
                    <span class="app-name">Admin Portal</span>
                </div>
            </div>
            
            <h2 class="portal-title">ADMIN LOGIN</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo sanitize($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" placeholder="Password" required>
                        <i class="fas fa-eye-slash toggle-password" onclick="togglePassword()"></i>
                    </div>
                </div>
                
                <div class="form-footer" style="justify-content: flex-end;">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-sign-in-alt"></i> LOGIN
                    </button>
                </div>
            </form>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="../login.html">← Back to User Login</a>
            </div>
        </div>
    </div>
    
    <script src="../js/main.js"></script>
</body>
</html>
