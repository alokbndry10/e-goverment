<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../login.html');
}

$mobile = trim($_POST['mobile'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($mobile) || empty($password)) {
    redirect('../login.html?error=' . urlencode('Please fill in all fields'));
}

// Check if user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE mobile = ?");
$stmt->execute([$mobile]);
$user = $stmt->fetch();

if (!$user) {
    redirect('../login.html?error=' . urlencode('Invalid mobile number or password'));
}

// Verify password
if (!password_verify($password, $user['password'])) {
    redirect('../login.html?error=' . urlencode('Invalid mobile number or password'));
}

// Set session
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['full_name'];
$_SESSION['user_mobile'] = $user['mobile'];

redirect('../dashboard.php');
