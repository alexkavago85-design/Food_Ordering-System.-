<?php
require_once __DIR__ . '/includes/header.php';

$catId = (int)($_GET['category_id'] ?? 0);
$category = null;
$foods = [];
if ($catId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ? LIMIT 1');
    $stmt->execute([$catId]);
    $category = $stmt->fetch();

    $stmt = $pdo->prepare('SELECT * FROM foods WHERE category_id = ? ORDER BY id DESC');
    $stmt->execute([$catId]);
    $foods = $stmt->fetchAll();
}
?>

<section class="container">
    <h2><?php echo e($category ? $category['name'] : 'Category'); ?></h2>

    <?php if (empty($foods)): ?>
        <p>No foods found in this category.</p>
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