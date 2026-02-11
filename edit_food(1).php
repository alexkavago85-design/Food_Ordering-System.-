<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: dashboard.php'); exit;
}

$stmt = $pdo->prepare('SELECT * FROM foods WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$food = $stmt->fetch();
if (!$food) { header('Location: dashboard.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) { $errors[] = 'Invalid CSRF token.'; }
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);

    if (!$name || $price <= 0) $errors[] = 'Provide name and valid price.';

    $imagePath = $food['image'];
    try {
        if (!empty($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $imagePath = handle_image_upload($_FILES['image'], $food['image']);
        }
    } catch (Exception $ex) {
        $errors[] = $ex->getMessage();
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE foods SET name = ?, description = ?, price = ?, category_id = ?, image = ? WHERE id = ?');
        $stmt->execute([$name, $desc, $price, $category_id, $imagePath, $id]);
        header('Location: dashboard.php'); exit;
    }
}

$cats = $pdo->query('SELECT * FROM categories')->fetchAll();
?>
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Edit Food</title></head><body>
    <h2>Edit Food #<?php echo e($food['id']); ?></h2>
    <?php if (!empty($errors)): foreach ($errors as $e) echo '<p>'.e($e).'</p>'; endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <label>Name</label><input name="name" value="<?php echo e($_POST['name'] ?? $food['name']); ?>" required>
        <label>Description</label><textarea name="description"><?php echo e($_POST['description'] ?? $food['description']); ?></textarea>
        <label>Price</label><input name="price" type="number" step="0.01" value="<?php echo e($_POST['price'] ?? $food['price']); ?>" required>
        <label>Category</label>
        <select name="category_id">
            <option value="0">--None--</option>
            <?php foreach ($cats as $c): ?><option value="<?php echo $c['id']; ?>" <?php echo ((isset($_POST['category_id']) && $_POST['category_id']==$c['id']) || (!isset($_POST['category_id']) && $food['category_id']==$c['id']))?'selected':''; ?>><?php echo e($c['name']); ?></option><?php endforeach; ?>
        </select>
        <p>Current Image: <?php if ($food['image']): ?><br><img src="../<?php echo e($food['image']); ?>" style="max-width:150px;"><?php else: ?>No image<?php endif; ?></p>
        <label>Replace Image (optional)</label><input type="file" name="image" accept="image/*">
        <br>
        <button type="submit">Save</button>
    </form>
    <p><a href="dashboard.php">Back</a></p>
</body></html>