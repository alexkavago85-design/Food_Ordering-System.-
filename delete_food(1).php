<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: dashboard.php'); exit; }

$stmt = $pdo->prepare('SELECT * FROM foods WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$food = $stmt->fetch();
if (!$food) { header('Location: dashboard.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) { die('Invalid CSRF'); }
    // delete image
    if ($food['image']) {
        $path = __DIR__ . '/../' . ltrim($food['image'], '/');
        if (file_exists($path)) @unlink($path);
    }
    $stmt = $pdo->prepare('DELETE FROM foods WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: dashboard.php'); exit;
}
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Delete Food</title></head><body>
    <h2>Delete Food #<?php echo e($food['id']); ?> - <?php echo e($food['name']); ?></h2>
    <p>Are you sure you want to delete this food? This action cannot be undone.</p>
    <form method="POST">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <button type="submit">Yes, delete</button>
        <a href="dashboard.php">Cancel</a>
    </form>
</body></html>