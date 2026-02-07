<?php
require_once __DIR__ . '/includes/header.php';

// Fetch some categories
$cats = $pdo->query('SELECT * FROM categories ORDER BY id ASC LIMIT 3')->fetchAll();

// Fetch latest foods
$stmt = $pdo->query('SELECT f.*, c.name as category_name FROM foods f LEFT JOIN categories c ON c.id = f.category_id ORDER BY f.id DESC LIMIT 6');
$foods = $stmt->fetchAll();
?>

<!-- CAtegories Section Starts Here -->
<section class="categories">
    <div class="container">
        <h2 class="text-center">Explore Foods</h2>

        <?php if (!empty($cats)): foreach ($cats as $c):
            $imgPath = 'images/' . strtolower(preg_replace('/[^a-z0-9]+/i', '-', $c['name'])) . '.jpg';
            if (!file_exists(__DIR__ . '/' . $imgPath)) $imgPath = 'images/default-cat.jpg';
        ?>
            <a href="category-foods.php?category_id=<?php echo e($c['id']); ?>">
            <div class="box-3 float-container">
                <img src="<?php echo e($imgPath); ?>" alt="<?php echo e($c['name']); ?>" class="img-responsive img-curve">

                <h3 class="float-text text-white"><?php echo e($c['name']); ?></h3>
            </div>
            </a>
        <?php endforeach; else: ?>
            <p>No categories available.</p>
        <?php endif; ?>

        <div class="clearfix"></div>
    </div>
</section>
<!-- Categories Section Ends Here -->

<!-- fOOD MEnu Section Starts Here -->
<section class="food-menu">
    <div class="container">
        <h2 class="text-center">Food Menu</h2>

        <?php if (!empty($foods)): foreach ($foods as $f): ?>
            <div class="food-menu-box">
                <div class="food-menu-img">
                    <?php $img = $f['image'] ? $f['image'] : 'images/default-food.jpg'; ?>
                    <img src="<?php echo e($img); ?>" alt="<?php echo e($f['name']); ?>" class="img-responsive img-curve">
                </div>

                <div class="food-menu-desc">
                    <h4><?php echo e($f['name']); ?></h4>
                    <p class="food-price">$<?php echo number_format($f['price'],2); ?></p>
                    <p class="food-detail">
                        <?php echo e(substr($f['description'], 0, 120)); ?>
                    </p>
                    <br>

                    <a href="cart.php?add=<?php echo $f['id']; ?>" class="btn btn-primary">Order Now</a>
                </div>
            </div>
        <?php endforeach; else: ?>
            <p>No foods available.</p>
        <?php endif; ?>

        <div class="clearfix"></div>

    </div>

    <p class="text-center">
        <a href="foods.php">See All Foods</a>
    </p>
</section>
<!-- fOOD Menu Section Ends Here -->

<?php require __DIR__ . '/includes/footer.php'; ?>