<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.html');
}

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get NID document
$stmt = $pdo->prepare("SELECT * FROM documents WHERE user_id = ? AND document_type = 'nid' ORDER BY submitted_at DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$nidDoc = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>National ID - Nagarik App</title>
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
                    <a href="auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="main-content">
            <a href="dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            
            <h2 class="section-title">NATIONAL ID</h2>
            
            <?php if ($nidDoc && $nidDoc['status'] === 'verified'): ?>
                <!-- Show verified document -->
                <div class="upload-container">
                    <div class="document-view">
                        <h3 style="color: #1e3c72; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-check-circle" style="color: #28a745;"></i>
                            Your National ID has been verified!
                        </h3>
                        <p style="color: #666; margin: 10px 0;">NID Number: <?php echo sanitize($nidDoc['document_number']); ?></p>
                        <div class="document-images">
                            <div class="document-image-card">
                                <img src="<?php echo sanitize($nidDoc['front_image']); ?>" alt="NID Front">
                                <p>Front Side</p>
                            </div>
                            <?php if ($nidDoc['back_image']): ?>
                            <div class="document-image-card">
                                <img src="<?php echo sanitize($nidDoc['back_image']); ?>" alt="NID Back">
                                <p>Back Side</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
            <?php elseif ($nidDoc && $nidDoc['status'] === 'pending'): ?>
                <!-- Show pending status -->
                <div class="upload-container">
                    <div class="document-view">
                        <h3 style="color: #856404; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-clock"></i>
                            Your document is pending verification
                        </h3>
                        <p style="color: #666; margin: 10px 0;">NID Number: <?php echo sanitize($nidDoc['document_number']); ?></p>
                        <p style="color: #666;">Submitted on: <?php echo date('F j, Y', strtotime($nidDoc['submitted_at'])); ?></p>
                        <div class="document-images">
                            <div class="document-image-card">
                                <img src="<?php echo sanitize($nidDoc['front_image']); ?>" alt="NID Front">
                                <p>Front Side</p>
                            </div>
                            <?php if ($nidDoc['back_image']): ?>
                            <div class="document-image-card">
                                <img src="<?php echo sanitize($nidDoc['back_image']); ?>" alt="NID Back">
                                <p>Back Side</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
            <?php elseif ($nidDoc && $nidDoc['status'] === 'rejected'): ?>
                <!-- Show rejected status and allow resubmission -->
                <div class="upload-container">
                    <div class="alert alert-error">
                        <strong>Your previous submission was rejected.</strong><br>
                        <?php if ($nidDoc['remarks']): ?>
                            Reason: <?php echo sanitize($nidDoc['remarks']); ?>
                        <?php endif; ?>
                    </div>
                    
                    <h3 style="color: #1e3c72; margin-bottom: 20px;">Submit New National ID Document</h3>
                    
                    <form action="auth/upload_document.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="document_type" value="nid">
                        
                        <div class="upload-section">
                            <div class="form-group">
                                <label>NID Number *</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-id-card"></i>
                                    <input type="text" name="document_number" placeholder="Enter your NID number" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="upload-section">
                            <h3>Front Side of NID *</h3>
                            <div class="file-input-wrapper" onclick="document.getElementById('front_image').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload front side image</p>
                                <input type="file" id="front_image" name="front_image" accept="image/*" required onchange="previewImage(this, 'frontPreview')">
                                <img id="frontPreview" class="preview-image" style="display: none;">
                            </div>
                        </div>
                        
                        <div class="upload-section">
                            <h3>Back Side of NID (Optional)</h3>
                            <div class="file-input-wrapper" onclick="document.getElementById('back_image').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload back side image</p>
                                <input type="file" id="back_image" name="back_image" accept="image/*" onchange="previewImage(this, 'backPreview')">
                                <img id="backPreview" class="preview-image" style="display: none;">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-primary" style="width: 100%; justify-content: center;">
                            <i class="fas fa-upload"></i> Submit for Verification
                        </button>
                    </form>
                </div>
                
            <?php else: ?>
                <!-- Upload form -->
                <div class="upload-container">
                    <form action="auth/upload_document.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="document_type" value="nid">
                        
                        <div class="upload-section">
                            <div class="form-group">
                                <label>NID Number *</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-id-card"></i>
                                    <input type="text" name="document_number" placeholder="Enter your NID number" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="upload-section">
                            <h3>Front Side of NID *</h3>
                            <div class="file-input-wrapper" onclick="document.getElementById('front_image').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload front side image</p>
                                <input type="file" id="front_image" name="front_image" accept="image/*" required onchange="previewImage(this, 'frontPreview')">
                                <img id="frontPreview" class="preview-image" style="display: none;">
                            </div>
                        </div>
                        
                        <div class="upload-section">
                            <h3>Back Side of NID (Optional)</h3>
                            <div class="file-input-wrapper" onclick="document.getElementById('back_image').click()">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload back side image</p>
                                <input type="file" id="back_image" name="back_image" accept="image/*" onchange="previewImage(this, 'backPreview')">
                                <img id="backPreview" class="preview-image" style="display: none;">
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-primary" style="width: 100%; justify-content: center;">
                            <i class="fas fa-upload"></i> Submit for Verification
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <script src="js/main.js"></script>
</body>
</html>
