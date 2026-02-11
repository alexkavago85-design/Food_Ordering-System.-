<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    }
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);

    if (!$name || $price <= 0) $errors[] = 'Provide name and valid price.';

    // Handle image upload
    $imagePath = null;
    try {
        if (!empty($_FILES['image'])) {
            $imagePath = handle_image_upload($_FILES['image']);
        }
    } catch (Exception $ex) {
        $errors[] = $ex->getMessage();
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO foods (name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $desc, $price, $category_id, $imagePath]);
        header('Location: dashboard.php');
        exit;
    }
}

$cats = $pdo->query('SELECT * FROM categories')->fetchAll();
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Add Food</title></head><body>
    <h2>Add Food</h2>
    <?php if (!empty($errors)): foreach ($errors as $e) echo '<p>'.e($e).'</p>'; endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <label>Name</label><input name="name" value="<?php echo e($_POST['name'] ?? ''); ?>" required>
        <label>Description</label><textarea name="description"><?php echo e($_POST['description'] ?? ''); ?></textarea>
        <label>Price</label><input name="price" type="number" step="0.01" value="<?php echo e($_POST['price'] ?? ''); ?>" required>
        <label>Category</label>
        <select name="category_id">
            <option value="0">--None--</option>
            <?php foreach ($cats as $c): ?><option value="<?php echo $c['id']; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id']==$c['id'])?'selected':''; ?>><?php echo e($c['name']); ?></option><?php endforeach; ?>
        </select>
        <label>Image (jpg/png/gif, max 2MB)</label><input type="file" name="image" accept="image/*">
        <br>
        <button type="submit">Save</button>
    </form>
    <p><a href="dashboard.php">Back</a></p>
</body></html>