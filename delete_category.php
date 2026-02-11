<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: categories.php'); exit; }
if (!csrf_check($_POST['csrf'] ?? '')) { die('Invalid CSRF'); }
$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) { header('Location: categories.php'); exit; }

// Optionally check if any foods belong to this category and handle or reassign; here we set to NULL
$stmt = $pdo->prepare('UPDATE foods SET category_id = NULL WHERE category_id = ?');
$stmt->execute([$id]);

$stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
$stmt->execute([$id]);
header('Location: categories.php'); exit;