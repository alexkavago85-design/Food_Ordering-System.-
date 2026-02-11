<?php
require_once __DIR__ . '/includes/header.php';

$q = trim($_GET['q'] ?? $_GET['search'] ?? '');
$foods = [];
if ($q !== '') {
    $stmt = $pdo->prepare('SELECT f.*, c.name as category_name FROM foods f LEFT JOIN categories c ON c.id = f.category_id WHERE f.name LIKE ? OR f.description LIKE ? ORDER BY f.name ASC');
    $like = '%' . $q . '%';
    $stmt->execute([$like, $like]);
    $foods = $stmt->fetchAll();
}
?>

<section class="container">
    <h2>Search results for: <?php echo e($q); ?></h2>

    <?php if (empty($foods)): ?>
        <p>No foods found matching your search.</p>
    <?php else: foreach ($foods as $f): ?>
        <div class="food-menu-box">
            <div class="food-menu-img">
                <?php $img = $f['image'] ? $f['image'] : 'images/default-food.jpg'; ?>
                <img src="<?php echo e($img); ?>" alt="<?php echo e($f['name']); ?>" class="img-responsive img-curve">
            </div>
            <div class="food-menu-desc">
                <h4><?php echo e($f['name']); ?></h4>
                <p class="food-price">$<?php echo number_format($f['price'],2); ?></p>
                <p class="food-detail"><?php echo e($f['description']); ?></p>
                <br>
                <a href="cart.php?add=<?php echo $f['id']; ?>" class="btn btn-primary">Add to Cart</a>
                <a href="order.php?food_id=<?php echo $f['id']; ?>" class="btn">View</a>
            </div>
        </div>
    <?php endforeach; endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>