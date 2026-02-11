<?php
// Common helpers: CSRF token, auth helpers
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_admin() {
    if (!is_admin()) {
        header('Location: ../admin/login.php');
        exit;
    }
}

// Image upload helper
function handle_image_upload($file, $oldPath = null) {
    if (empty($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return $oldPath;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('File upload error.');
    }

    $allowedMime = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!array_key_exists($mime, $allowedMime)) {
        throw new RuntimeException('Invalid image type.');
    }

    $maxSize = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxSize) {
        throw new RuntimeException('File too large (max 2MB).');
    }

    $ext = $allowedMime[$mime];
    $base = bin2hex(random_bytes(8));
    $filename = $base . '.' . $ext;
    $dest = __DIR__ . '/../uploads/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new RuntimeException('Failed to move uploaded file.');
    }

    // Optionally remove old file
    if ($oldPath) {
        $oldFull = __DIR__ . '/../' . ltrim($oldPath, '/');
        if (file_exists($oldFull)) @unlink($oldFull);
    }

    return 'uploads/' . $filename;
}
