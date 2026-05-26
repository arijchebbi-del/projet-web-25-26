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
    $errors = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds server limit.',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds form limit.',
        UPLOAD_ERR_PARTIAL => 'Upload was incomplete.',
        UPLOAD_ERR_NO_FILE => 'No file uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION => 'Upload blocked by server extension.',
    ];
    $message = $errors[$file['error']] ?? 'Upload failed.';
    echo json_encode(['ok' => false, 'message' => $message]);
    exit();
}

$allowed = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'image/gif' => 'gif',
];

$mime = null;
if (function_exists('mime_content_type')) {
    $mime = mime_content_type($file['tmp_name']);
} elseif (class_exists('finfo')) {
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
} else {
    $mime = $file['type'] ?? null;
}

if (!$mime) {
    echo json_encode(['ok' => false, 'message' => 'Unable to detect file type.']);
    exit();
}
if (!isset($allowed[$mime])) {
    echo json_encode(['ok' => false, 'message' => 'Only image files are allowed.']);
    exit();
}

$uploadDir = __DIR__ . '/../frontend/assets/images/uploads';
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
    echo json_encode(['ok' => false, 'message' => 'Unable to create upload directory.']);
    exit();
}

$filename = 'avatar_' . session_id() . '_' . time() . '.' . $allowed[$mime];
$target = $uploadDir . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $target)) {
    echo json_encode(['ok' => false, 'message' => 'Unable to save avatar.']);
    exit();
}

$avatarUrl = '/frontend/assets/images/uploads/' . $filename;

require_once __DIR__ . '/../backend/config/ConnexionDB.php';

try {
    $conn = ConnexionDB::getInstance();
    $stmt = $conn->prepare('UPDATE users SET avatar_url = :avatar_url WHERE email = :email');
    $stmt->execute([
        ':avatar_url' => $avatarUrl,
        ':email' => $_SESSION['email'],
    ]);
} catch (Throwable $e) {
    echo json_encode(['ok' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    exit();
}

echo json_encode(['ok' => true, 'avatarUrl' => $avatarUrl]);
?>
