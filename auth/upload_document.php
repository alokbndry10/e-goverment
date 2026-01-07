<?php
require_once '../config.php';

if (!isLoggedIn()) {
    redirect('../login.html');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../dashboard.php');
}

$document_type = $_POST['document_type'] ?? '';
$document_number = trim($_POST['document_number'] ?? '');

// Validate document type
if (!in_array($document_type, ['nid', 'citizenship'])) {
    redirect('../dashboard.php?error=' . urlencode('Invalid document type'));
}

// Validate document number
if (empty($document_number)) {
    $redirect = $document_type === 'nid' ? '../nid.php' : '../citizenship.php';
    redirect($redirect . '?error=' . urlencode('Please enter document number'));
}

// Check for front image
if (!isset($_FILES['front_image']) || $_FILES['front_image']['error'] !== UPLOAD_ERR_OK) {
    $redirect = $document_type === 'nid' ? '../nid.php' : '../citizenship.php';
    redirect($redirect . '?error=' . urlencode('Please upload front side image'));
}

// Create uploads directory if not exists
$upload_dir = '../uploads/' . $_SESSION['user_id'] . '/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Allowed file types
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

// Process front image
$front_file = $_FILES['front_image'];
if (!in_array($front_file['type'], $allowed_types)) {
    $redirect = $document_type === 'nid' ? '../nid.php' : '../citizenship.php';
    redirect($redirect . '?error=' . urlencode('Invalid file type. Only JPG, PNG, GIF allowed'));
}

$front_ext = pathinfo($front_file['name'], PATHINFO_EXTENSION);
$front_name = $document_type . '_front_' . time() . '.' . $front_ext;
$front_path = $upload_dir . $front_name;

if (!move_uploaded_file($front_file['tmp_name'], $front_path)) {
    $redirect = $document_type === 'nid' ? '../nid.php' : '../citizenship.php';
    redirect($redirect . '?error=' . urlencode('Failed to upload front image'));
}

// Process back image (optional)
$back_path = null;
if (isset($_FILES['back_image']) && $_FILES['back_image']['error'] === UPLOAD_ERR_OK) {
    $back_file = $_FILES['back_image'];
    
    if (in_array($back_file['type'], $allowed_types)) {
        $back_ext = pathinfo($back_file['name'], PATHINFO_EXTENSION);
        $back_name = $document_type . '_back_' . time() . '.' . $back_ext;
        $back_path = $upload_dir . $back_name;
        
        if (!move_uploaded_file($back_file['tmp_name'], $back_path)) {
            $back_path = null;
        }
    }
}

// Save to database - remove leading ../
$front_db_path = 'uploads/' . $_SESSION['user_id'] . '/' . $front_name;
$back_db_path = $back_path ? 'uploads/' . $_SESSION['user_id'] . '/' . $back_name : null;

try {
    $stmt = $pdo->prepare("INSERT INTO documents (user_id, document_type, document_number, front_image, back_image) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $document_type,
        $document_number,
        $front_db_path,
        $back_db_path
    ]);
    
    redirect('../dashboard.php?success=' . urlencode('Document submitted successfully! Please wait for verification.'));
} catch (PDOException $e) {
    $redirect = $document_type === 'nid' ? '../nid.php' : '../citizenship.php';
    redirect($redirect . '?error=' . urlencode('Failed to submit document. Please try again.'));
}
