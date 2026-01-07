<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../register.php');
}

$full_name = trim($_POST['full_name'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate input
if (empty($full_name) || empty($mobile) || empty($password) || empty($confirm_password)) {
    redirect('../register.php?error=' . urlencode('Please fill in all fields'));
}

// Validate mobile number
if (!preg_match('/^[0-9]{10}$/', $mobile)) {
    redirect('../register.php?error=' . urlencode('Please enter a valid 10-digit mobile number'));
}

// Check password match
if ($password !== $confirm_password) {
    redirect('../register.php?error=' . urlencode('Passwords do not match'));
}

// Check password length
if (strlen($password) < 6) {
    redirect('../register.php?error=' . urlencode('Password must be at least 6 characters'));
}

// Check if mobile already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE mobile = ?");
$stmt->execute([$mobile]);
if ($stmt->fetch()) {
    redirect('../register.php?error=' . urlencode('Mobile number already registered'));
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user
try {
    $stmt = $pdo->prepare("INSERT INTO users (full_name, mobile, password) VALUES (?, ?, ?)");
    $stmt->execute([$full_name, $mobile, $hashed_password]);
    
    redirect('../login.html?success=' . urlencode('Registration successful! Please login.'));
} catch (PDOException $e) {
    redirect('../register.php?error=' . urlencode('Registration failed. Please try again.'));
}
