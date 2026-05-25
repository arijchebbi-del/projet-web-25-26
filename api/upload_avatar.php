<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['ok' => false, 'message' => 'Authentication required.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['avatar'])) {
    echo json_encode(['ok' => false, 'message' => 'No avatar uploaded.']);
    exit();
}

$file = $_FILES['avatar'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['ok' => false, 'message' => 'Upload failed.']);
    exit();
}

$allowed = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'image/gif' => 'gif',
];

$mime = mime_content_type($file['tmp_name']);
if (!isset($allowed[$mime])) {
    echo json_encode(['ok' => false, 'message' => 'Only image files are allowed.']);
    exit();
}

$uploadDir = __DIR__ . '/../frontend/assets/images/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0775, true);
}

$filename = 'avatar_' . session_id() . '_' . time() . '.' . $allowed[$mime];
$target = $uploadDir . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $target)) {
    echo json_encode(['ok' => false, 'message' => 'Unable to save avatar.']);
    exit();
}

$avatarUrl = '/frontend/assets/images/uploads/' . $filename;

require_once __DIR__ . '/../backend/config/ConnexionDB.php';

$conn = ConnexionDB::getInstance();
$stmt = $conn->prepare('UPDATE users SET avatar_url = :avatar_url WHERE email = :email');
$stmt->execute([
    ':avatar_url' => $avatarUrl,
    ':email' => $_SESSION['email'],
]);

echo json_encode(['ok' => true, 'avatarUrl' => $avatarUrl]);
?>
