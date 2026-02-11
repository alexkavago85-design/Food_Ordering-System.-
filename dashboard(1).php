<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

// Fetch foods
$stmt = $pdo->query('SELECT f.*, c.name as category_name FROM foods f LEFT JOIN categories c ON c.id = f.category_id ORDER BY f.id DESC');
$foods = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Admin Dashboard</title></head>
<body>
    <h1>Admin Dashboard</h1>
    <p><a href="add_food.php">Add Food</a> | <a href="categories.php">Categories</a> | <a href="logout.php">Logout</a></p>

    <h2>Foods</h2>
    <table>
        <thead><tr><th>ID</th><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($foods as $f): ?>
                <tr>
                    <td><?php echo e($f['id']); ?></td>
                    <td><?php if ($f['image']): ?><img src="../<?php echo e($f['image']); ?>" style="max-width:80px;"><?php else: ?>-<?php endif; ?></td>
                    <td><?php echo e($f['name']); ?></td>
                    <td><?php echo e($f['category_name']); ?></td>
                    <td><?php echo number_format($f['price'],2); ?></td>
                    <td><a href="edit_food.php?id=<?php echo $f['id']; ?>">Edit</a> | <a href="delete_food.php?id=<?php echo $f['id']; ?>">Delete</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Orders</h2>
    <p><a href="orders.php">View All Orders</a></p>
</body>
</html>