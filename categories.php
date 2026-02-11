<?php
require_once __DIR__ . '/includes/header.php';

$cats = $pdo->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll();
?>

<section class="container">
    <h2>All Categories</h2>

    <?php if (empty($cats)): ?>
        <p>No categories yet.</p>
    <?php else: foreach ($cats as $c):
        $imgPath = 'images/' . strtolower(preg_replace('/[^a-z0-9]+/i', '-', $c['name'])) . '.jpg';
        if (!file_exists(__DIR__ . '/' . $imgPath)) $imgPath = 'images/default-cat.jpg';
    ?>
        <a href="category-foods.php?category_id=<?php echo e($c['id']); ?>">
        <div class="box-3 float-container">
            <img src="<?php echo e($imgPath); ?>" alt="<?php echo e($c['name']); ?>" class="img-responsive img-curve">

            <h3 class="float-text text-white"><?php echo e($c['name']); ?></h3>
        </div>
        </a>
    <?php endforeach; endif; ?>

    <div class="clearfix"></div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>