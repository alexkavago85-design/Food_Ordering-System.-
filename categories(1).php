<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

$errors = [];
// Add category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!csrf_check($_POST['csrf'] ?? '')) { $errors[] = 'Invalid CSRF token.'; }
    $name = trim($_POST['name'] ?? '');
    if (!$name) $errors[] = 'Provide category name.';
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (?)');
        $stmt->execute([$name]);
        header('Location: categories.php'); exit;
    }
}

// Edit category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    if (!csrf_check($_POST['csrf'] ?? '')) { $errors[] = 'Invalid CSRF token.'; }
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    if (!$name) $errors[] = 'Provide category name.';
    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE categories SET name = ? WHERE id = ?');
        $stmt->execute([$name, $id]);
        header('Location: categories.php'); exit;
    }
}

$cats = $pdo->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll();
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Manage Categories</title></head><body>
    <h2>Categories</h2>
    <?php if (!empty($errors)): foreach ($errors as $e) echo '<p>'.e($e).'</p>'; endif; ?>

    <h3>Add Category</h3>
    <form method="POST">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="action" value="add">
        <label>Name</label><input name="name" required>
        <button type="submit">Add</button>
    </form>

    <h3>Existing</h3>
    <table>
        <thead><tr><th>ID</th><th>Name</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($cats as $c): ?>
            <tr>
                <td><?php echo e($c['id']); ?></td>
                <td><?php echo e($c['name']); ?></td>
                <td>
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                        <input name="name" value="<?php echo e($c['name']); ?>">
                        <button type="submit">Save</button>
                    </form>
                    <form method="POST" action="delete_category.php" style="display:inline">
                        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
                        <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><a href="dashboard.php">Back</a></p>
</body></html>