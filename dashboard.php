<?php
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.html');
}

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get user's documents
$stmt = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? ORDER BY submitted_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$documents = $stmt->fetchAll();

// Separate documents by type
$nidDoc = null;
$citizenshipDoc = null;
foreach ($documents as $doc) {
    if ($doc['document_type'] === 'nid' && !$nidDoc) {
        $nidDoc = $doc;
    }
    if ($doc['document_type'] === 'citizenship' && !$citizenshipDoc) {
        $citizenshipDoc = $doc;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Nagarik App</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <div class="header-logo">
                    <span>नागरिक</span>
                </div>
                <div class="header-title">
                    <h1>Nagarik App</h1>
                    <p>Citizen Portal</p>
                </div>
            </div>
            <div class="header-right">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <span><?php echo sanitize($user['full_name']); ?></span>
                </div>
                <div class="header-actions">
                    <span>EN-US</span>
                    <a href="auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="main-content">
            <h2 class="section-title">AVAILABLE SERVICES</h2>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success" style="max-width: 600px; margin: 0 auto 20px;">
                    <?php echo sanitize($_GET['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error" style="max-width: 600px; margin: 0 auto 20px;">
                    <?php echo sanitize($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <div class="services-grid">
                <!-- National ID Card -->
                <a href="nid.php" class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h3>National ID</h3>
                    <?php if ($nidDoc): ?>
                        <span class="status-badge status-<?php echo $nidDoc['status']; ?>">
                            <?php echo ucfirst($nidDoc['status']); ?>
                        </span>
                    <?php endif; ?>
                </a>
                
                <!-- Citizenship -->
                <a href="citizenship.php" class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-passport"></i>
                    </div>
                    <h3>Citizenship</h3>
                    <?php if ($citizenshipDoc): ?>
                        <span class="status-badge status-<?php echo $citizenshipDoc['status']; ?>">
                            <?php echo ucfirst($citizenshipDoc['status']); ?>
                        </span>
                    <?php endif; ?>
                </a>
                
                <!-- Disabled services (for display only) -->
                <div class="service-card disabled">
                    <div class="service-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3>Driving License</h3>
                </div>
                
                <div class="service-card disabled">
                    <div class="service-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Police Clearance</h3>
                </div>
                
                <div class="service-card disabled">
                    <div class="service-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Malpot</h3>
                </div>
                
                <div class="service-card disabled">
                    <div class="service-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Education</h3>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
