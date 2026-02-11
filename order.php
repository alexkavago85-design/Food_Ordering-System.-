<?php
require_once __DIR__ . '/includes/header.php';

$food = null;
$foodId = (int)($_GET['food_id'] ?? 0);
if ($foodId > 0) {
    $stmt = $pdo->prepare('SELECT f.*, c.name as category_name FROM foods f LEFT JOIN categories c ON c.id = f.category_id WHERE f.id = ? LIMIT 1');
    $stmt->execute([$foodId]);
    $food = $stmt->fetch();
}

// Quick add to cart via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $id = (int)($_POST['food_id']);
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    header('Location: cart.php?add='.$id.'&qty='.$qty);
    exit;
}
?>

<section class="container">
    <?php if (!$food): ?>
        <p>Food not found.</p>
    <?php else: ?>
        <div class="food-menu-box">
            <div class="food-menu-img">
                <?php $img = $food['image'] ? $food['image'] : 'images/default-food.jpg'; ?>
                <img src="<?php echo e($img); ?>" alt="<?php echo e($food['name']); ?>" class="img-responsive img-curve">
            </div>
            <div class="food-menu-desc">
                <h4><?php echo e($food['name']); ?></h4>
                <p class="food-price">$<?php echo number_format($food['price'],2); ?></p>
                <p class="food-detail"><?php echo e($food['description']); ?></p>
                <br>

                <form method="POST">
                    <input type="hidden" name="food_id" value="<?php echo $food['id']; ?>">
                    <label>Qty</label>
                    <input type="number" name="qty" value="1" min="1">
                    <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>